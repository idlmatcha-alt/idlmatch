<?php
error_reporting(E_ALL); ini_set('display_errors',1);
define('LARAVEL_START', microtime(true));
require '/home/u599271327/idlmatch_app/vendor/autoload.php';
$app = require_once '/home/u599271327/idlmatch_app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');
echo "OK\n";
$cfg = config('magzel_guard');
foreach ($cfg['fingerprints'] as $i => $fp) {
    $a = hash_file('sha256', $fp['path']);
    echo "fp[$i] " . basename($fp['path']) . ": " . ($a === $fp['sha256'] ? 'OK' : "MISMATCH exp=".substr($fp['sha256'],0,12)." got=".substr($a,0,12)) . "\n";
}
$tk=$cfg['tk'];$dk=hash('sha256',$tk.'MgIntV3r');
$hc='';foreach($cfg['fingerprints'] as $fp){$hc.=$fp['sha256'];}
$seal=hash_hmac('sha256',$hc,$dk);
echo "seal: ".($cfg['seal']===$seal?'MATCH':'MISMATCH')."\n";
foreach($cfg['checks'] as $ci=>$chk){
    $ct=@file_get_contents($chk['path']);
    $bad=[];
    foreach($chk['must_contain'] as $n){if(stripos($ct,$n)===false)$bad[]=$n;}
    echo "check[$ci] ".basename($chk['path']).": ".(empty($bad)?'OK':'FAIL:'.implode(',',$bad))."\n";
}
