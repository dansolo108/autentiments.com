<?php
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/config.inc.php';
require_once MODX_BASE_PATH . 'index.php';

// Load main services
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->setLogLevel($is_debug ? modX::LOG_LEVEL_INFO : modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');

// Time limit
set_time_limit(600);
$tmp = 'Trying to set time limit = 600 sec: ';
$tmp .= ini_get('max_execution_time') == 600 ? 'done' : 'error';
$modx->log(modX::LOG_LEVEL_INFO,  $tmp);

$working_dir = MODX_CORE_PATH . 'components/stik/cron/';
$dl_archive_name = 'cities15000'; // cities15000, cities5000, cities500

// download 
$curl = curl_init('http://download.geonames.org/export/dump/'.$dl_archive_name.'.zip');
$fp =fopen($working_dir . 'cities.zip','w');
curl_setopt($curl, CURLOPT_FILE, $fp);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_exec($curl);
curl_close($curl);
fflush($fp);
fclose($fp);

echo "\nTrying to extract zip archive\n";
$zip = new ZipArchive;
if ($zip->open($working_dir . 'cities.zip') === TRUE) {
    $zip->extractTo($working_dir . 'extracted');
    $zip->close();
    echo "\nOK\n";
} else {
    echo "\nFailed, code: $zip\n";
}

$old_file = $working_dir . 'extracted/'.$dl_archive_name.'.txt';
$file = $working_dir . 'extracted/'.$dl_archive_name.'.csv';
rename($old_file, $file); // меняем расширение на csv

if (!preg_match('/\.csv$/i', $file)) {
	$modx->log(modX::LOG_LEVEL_ERROR, 'Wrong file extension. File must be an *.csv.');
	exit;
}

$file = str_replace('//', '/', $file);
if (!file_exists($file)) {
	$modx->log(modX::LOG_LEVEL_ERROR, 'File not found at '.$file.'.');
	exit;
}

// exit;

$connect = mysqli_connect($database_server, $database_user, $database_password, $dbase);

mysqli_query($connect, 'TRUNCATE TABLE Hvi2w7e_stik_cities');

//Получаем CSV файл
$handle = fopen($file,"r"); 

$i = 0;

while (($data = fgetcsv($handle, 0, "\t")) !== false) {
    mysqli_query($connect, 'INSERT INTO Hvi2w7e_stik_cities (`geonameid`,`name`,`asciiname`,`alternatenames`,`latitude`,`longitude`,`feature_class`,`feature_code`,`country_code`,`cc2`,`admin1_code`,`admin2_code`,`admin3_code`,`admin4_code`,`population`,`elevation`,`dem`,`timezone`,`modification_date`) VALUES 
        ("' . implode('", "', $data) . '")
    ');
    $i++;
}

$count = count($i);

fclose($handle);

if (!XPDO_CLI_MODE) {echo '<pre>';}
echo "\nImport complete in ".number_format(microtime(true) - $modx->startTime, 7) . " s\n";
echo "\nTotal rows:	$count\n";
if (!XPDO_CLI_MODE) {echo '</pre>';}