<?php
/**
 * Polylang
 * @package polylang
 * @var modX $modx
 * @var Polylang $polylang
 * @var PolylangTools $tools
 */

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->switchContext('mgr');

$polylang = $modx->getService('polylang', 'Polylang');
$tools = $polylang->getTools();

$wait = 1000; // microseconds время задержки между обращением к API переводчика
$overwrite = false; // нужно ли делать перевод для уже существующих лексиконов с учетом опции polylang_disallow_translation_completed_field
$classKeys = array(
    'modDocument',    // обычные ресурсы Modx
    // 'msCategory', // категории miniShop2
    // 'msProduct', // товар miniShop2
);

$q = $modx->newQuery('modResource');
$q->select($modx->getSelectColumns('modResource', 'modResource', '', array('id')));
$q->where(array(
    'class_key:IN' => $classKeys
));

$total = 0;
if ($q->prepare() && $q->stmt->execute()) {
    while ($id = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
        /** @var modProcessorResponse $response */
        $response = $polylang->runProcessor('mgr/polylangcontent/generate', array(
            'rid' => $id,
            'translate' => true,
            'overwrite' => $overwrite,
        ));
        if ($response->isError()) {
            $err = $response->getMessage();
            $modx->log(modX::LOG_LEVEL_ERROR, $err);
            exit('Error! ' . $err);
        }
        $total++;
        usleep($wait);

    }
}

echo "Done! Total translate: {$total}\n";