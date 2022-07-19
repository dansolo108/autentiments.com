<?php


class stikMoySklad {
    function __construct (modX &$modx, array $config = []) {
        $this->modx =& $modx;
        $this->namespace = $this->modx->getOption('namespace', $config, 'stik');
        $corePath = $this->modx->getOption('core_path') . 'components/stik/';
        $assetsPath = $this->modx->getOption('assets_path') . 'components/stik/';
        $assetsUrl = $this->modx->getOption('assets_url') . 'components/stik/';
        $this->config = array_merge(array(
            'core_path' => $corePath,
            'assets_path' => $assetsPath,
            'model_path' => $corePath . 'model/',
            'processors_path' => $corePath . 'processors/',

            'assets_url' => $assetsUrl,
            'action_url' => $assetsUrl . 'action.php',
            'connector_url' => $assetsUrl . 'connector.php',
            'apiKey' => $this->modx->getOption('stik_moySklad_api_key'),
            'serverAddress' => $this->modx->getOption('stik_moySklad_url'), //
        ), $config);

        $this->modx->addPackage('stik', $this->config['model_path']);

        /* @var modRest $this->modRestClient */
        $this->modRestClient = $this->modx->getService('rest', 'rest.modRest');
        $this->modRestClient->setOption('baseUrl', rtrim($this->config['serverAddress'], '/'));
        $this->modRestClient->setOption('format', 'json');
        $this->modRestClient->setOption('suppressSuffix', true);
        $this->modRestClient->setOption('headers', [
            'Content-type' => 'application/json', // Сообщаем сервису что хотим получить ответ в json формате
            'Authorization:' => "Bearer ".$this->config['apiKey'],
        ]);
    }
    public function getProduct(){

    }
}