<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');

// Load main services
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->getService('error','error.modError');

// Time limit
set_time_limit(600);
$tmp = 'Trying to set time limit = 600 sec: ';
$tmp .= ini_get('max_execution_time') == 600 ? 'done' : 'error';
$modx->log(modX::LOG_LEVEL_INFO,  $tmp);


$sql = "DELETE FROM Hvi2w7e_manager_log WHERE user = " . $modx->getOption('msync_user_id_import');
$count = $modx->exec($sql);


if (!XPDO_CLI_MODE) {echo '<pre>';}
// Получим количество удаленных записей
echo "\nOperation complete in ".number_format(microtime(true) - $modx->startTime, 7) . " s\n";
echo "Удалено $count записей.\n";
if (!XPDO_CLI_MODE) {echo '</pre>';}

?>