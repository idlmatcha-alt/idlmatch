<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/includes/collector.php';
require_once dirname(__DIR__).'/includes/campaign.php';
require_cli_or_secret();
$pdo=db();
$sources=$pdo->query('SELECT * FROM crawl_sources WHERE enabled=1 ORDER BY id')->fetchAll();
$total=['sources'=>0,'pages'=>0,'found'=>0];
foreach($sources as $src){ $total['sources']++; try { $r=crawl_source($src); $total['pages']+=$r['pages']; $total['found']+=$r['found']; $pdo->prepare('UPDATE crawl_sources SET last_run_at=?, last_found=?, last_error=NULL, updated_at=? WHERE id=?')->execute([date('Y-m-d H:i:s'),$r['found'],date('Y-m-d H:i:s'),$src['id']]); } catch(Throwable $e){ $pdo->prepare('UPDATE crawl_sources SET last_run_at=?, last_error=?, updated_at=? WHERE id=?')->execute([date('Y-m-d H:i:s'),$e->getMessage(),date('Y-m-d H:i:s'),$src['id']]); log_event('source_error',$e->getMessage(),['source_id'=>$src['id']]); } }
echo 'Collection: '.json_encode($total).PHP_EOL;
foreach(['idlfx.com','idlmatch.com'] as $brand){ $c=get_active_campaign($brand); if($c){ $r=send_campaign($c); echo $brand.': '.json_encode($r).PHP_EOL; } }
