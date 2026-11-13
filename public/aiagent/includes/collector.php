<?php
declare(strict_types=1);
require_once __DIR__.'/bootstrap.php';

function http_fetch(string $url): array {
    global $config;
    $ch=curl_init($url);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_MAXREDIRS=>4,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>$config['collector']['request_timeout'],CURLOPT_USERAGENT=>$config['collector']['user_agent'],CURLOPT_ENCODING=>'',CURLOPT_HEADER=>true]);
    $raw=curl_exec($ch); $info=curl_getinfo($ch); $err=curl_error($ch); curl_close($ch);
    if ($raw===false) throw new RuntimeException($err ?: 'HTTP fetch failed');
    $body=substr($raw,(int)$info['header_size']);
    if (strlen($body)>$config['collector']['max_bytes_per_page']) throw new RuntimeException('Page too large');
    return ['body'=>$body,'type'=>strtolower($info['content_type'] ?? ''),'url'=>$info['url'] ?? $url,'status'=>$info['http_code'] ?? 0];
}
function domain_allowed(string $url): bool {
    global $config;
    $host=strtolower(parse_url($url,PHP_URL_HOST) ?: '');
    foreach ($config['collector']['allowed_domains'] as $d) { if ($host===$d || str_ends_with($host,'.'.$d)) return true; }
    return false;
}
function normalize_email(string $e): string { return strtolower(trim(rtrim($e,".,;:()[]{}<>\"'"))); }
function extract_emails(string $body): array {
    preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,63}/i',$body,$m);
    $out=[]; foreach ($m[0] as $e) { $e=normalize_email($e); if (filter_var($e,FILTER_VALIDATE_EMAIL)) $out[$e]=true; }
    return array_keys($out);
}
function extract_links(string $html, string $baseUrl): array {
    $links=[]; libxml_use_internal_errors(true); $dom=new DOMDocument(); if (!@$dom->loadHTML($html)) return [];
    $base=parse_url($baseUrl); foreach ($dom->getElementsByTagName('a') as $a) {
        $href=trim($a->getAttribute('href')); if ($href==='' || str_starts_with($href,'#') || str_starts_with($href,'mailto:') || str_starts_with($href,'tel:')) continue;
        if (preg_match('#^javascript:#i',$href)) continue;
        if (str_starts_with($href,'//')) $href=($base['scheme']??'https').':'.$href;
        elseif (str_starts_with($href,'/')) $href=($base['scheme']??'https').'://'.($base['host']??'').$href;
        elseif (!preg_match('#^https?://#i',$href)) {
            $dir=rtrim(dirname($base['path']??'/'),'/'); $href=($base['scheme']??'https').'://'.($base['host']??'').$dir.'/'.$href;
        }
        $href=preg_replace('/#.*$/','',$href); if (domain_allowed($href)) $links[$href]=true;
    }
    return array_keys($links);
}
function upsert_email(string $email,string $url,string $title=''): bool {
    $pdo=db(); $now=date('Y-m-d H:i:s'); $host=parse_url($url,PHP_URL_HOST) ?: '';
    $stmt=$pdo->prepare('SELECT id FROM contacts WHERE email=?'); $stmt->execute([$email]); $row=$stmt->fetch();
    if ($row) { $pdo->prepare('UPDATE contacts SET last_seen_at=?, source_url=COALESCE(NULLIF(?,\'\'),source_url), source_title=COALESCE(NULLIF(?,\'\'),source_title), updated_at=? WHERE id=?')->execute([$now,$url,$title,$now,$row['id']]); return false; }
    $pdo->prepare('INSERT INTO contacts(email,source_domain,source_url,source_title,first_seen_at,last_seen_at,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)')->execute([$email,$host,$url,$title,$now,$now,$now,$now]);
    return true;
}
function crawl_source(array $source): array {
    $queue=[['url'=>$source['url'],'depth'=>0]]; $seen=[]; $found=0; $pages=0; $urls=[];
    while ($queue && $pages < (int)$GLOBALS['config']['collector']['max_pages_per_run']) {
        $item=array_shift($queue); $url=$item['url']; $depth=(int)$item['depth']; if(isset($seen[$url])) continue; $seen[$url]=1;
        try { $r=http_fetch($url); } catch(Throwable $e) { log_event('crawl_error',$e->getMessage(),['url'=>$url]); continue; }
        $pages++; $ctype=$r['type']; $emails=extract_emails($r['body']);
        foreach($emails as $email){ if(upsert_email($email,$r['url'],basename(parse_url($r['url'],PHP_URL_PATH) ?: ''))) $found++; }
        if ($depth < (int)$source['max_depth'] && (str_contains($ctype,'html') || $ctype==='')) {
            foreach(extract_links($r['body'],$r['url']) as $link) { if(count($seen)+count($queue)<300) $queue[]=['url'=>$link,'depth'=>$depth+1]; }
        }
        $urls[]=$r['url'];
    }
    return ['pages'=>$pages,'found'=>$found,'urls'=>$urls];
}
