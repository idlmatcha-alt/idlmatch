<?php
declare(strict_types=1);
require_once __DIR__.'/smtp.php';

function get_active_campaign(string $brand): ?array {
    $s=db()->prepare('SELECT * FROM campaigns WHERE brand=? AND enabled=1 ORDER BY id DESC LIMIT 1'); $s->execute([$brand]); return $s->fetch() ?: null;
}
function personalize(string $s,array $c): string { return strtr($s,['{{email}}'=>$c['email'],'{{name}}'=>($c['name'] ?: 'there')]); }
function send_campaign(array $campaign): array {
    global $config; $pdo=db(); $limit=(int)$config['sending']['max_recipients_per_run'];
    $sql="SELECT c.* FROM contacts c WHERE c.consent_status='approved' AND NOT EXISTS(SELECT 1 FROM send_log l WHERE l.contact_id=c.id AND l.campaign_id=?) ORDER BY c.id ASC LIMIT $limit";
    $s=$pdo->prepare($sql); $s->execute([$campaign['id']]); $rows=$s->fetchAll(); $result=['attempted'=>0,'sent'=>0,'failed'=>0,'skipped'=>0];
    $mailer=new SmtpMailer($config['smtp']);
    foreach($rows as $c){ $result['attempted']++; $subject=personalize($campaign['subject'],$c); $html=personalize($campaign['body_html'],$c); $text=personalize($campaign['body_text'] ?: strip_tags($html),$c); $status='sent'; $response='';
        try { if(!$config['sending']['dry_run']) $mailer->send($c['email'],$subject,$html,$text); else $response='DRY_RUN'; $result['sent']++; }
        catch(Throwable $e){ $status='failed'; $response=$e->getMessage(); $result['failed']++; }
        $pdo->prepare('INSERT INTO send_log(campaign_id,contact_id,to_email,status,response,sent_at) VALUES(?,?,?,?,?,?)')->execute([$campaign['id'],$c['id'],$c['email'],$status,$response,date('Y-m-d H:i:s')]);
        usleep((int)$config['sending']['delay_ms']*1000);
    }
    return $result;
}
