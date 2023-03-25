<?php

class msCalcDelivery
{
    /** @var modX $modx */
    public $modx;
    /** @var pdoFetch $pdoTools */
    public $pdoTools;
    public string $version = "1.0.1";

    public array $config = [];
    public string $settings_prefix = "mscalcdelivery_";
    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        /** @var modNamespace $namespace */
        $namespace = $modx->getObject('modNamespace',"msCalcDelivery");
        $corePath = $namespace->getCorePath();
        $assetsPath = $namespace->getAssetsPath();
        $assetsUrl = str_replace(MODX_BASE_PATH,"",$assetsPath);
        if($assetsUrl[0] !== "/"){
            $assetsUrl = "/".$assetsUrl;
        }
        //дефолтные значения заменяем значениями из настроек
        $this->config = $this->getOptions([
            'core_path' => $corePath,
            'model_path' => $corePath . 'model/',
            'processors_path' => $corePath . 'processors/',
            "polling_rate"=>5,

            'frontend_js' =>$assetsUrl . 'js/web/default.js',
            'frontend_css' =>$assetsUrl . 'css/default.css',
            'action_url' => $assetsUrl . 'action.php',
            'assets_url' => $assetsUrl,
            'css_url' => $assetsUrl . 'css/',
            'js_url' => $assetsUrl . 'js/',

            "tpl"=>"msCalcDelivery.default",
            "emptyTpl"=>"msCalcDelivery.empty",
        ]);
        // переопределяем на те которые зашли в конструктор
        $this->config = array_merge($this->config, $config);

        $this->modx->addPackage('mscalcdelivery', $this->config['model_path']);
        $this->modx->lexicon->load('mscalcdelivery:default');
        $this->modx->loadClass("unNotification");
        if ($this->pdoTools = $this->modx->getService('pdoFetch')) {
            $this->pdoTools->setConfig($this->config);
        }
        $this->loadFrontend();
    }
    function getOption($key, $options = null, $default = null, $skipEmpty = false){
        return $this->modx->getOption($this->settings_prefix.$key, $options, $default, $skipEmpty);
    }
    function loadFrontend(){
        $config = $this->pdoTools->makePlaceholders($this->config);
        // Register JS
        $js = trim($this->config["frontend_js"]);
        if (!empty($js) && preg_match('/\.js/i', $js)) {
            if (preg_match('/\.js$/i', $js)) {
                $js .= '?v=' . substr(md5($this->version), 0, 10);
            }
            $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
        }
        $css = trim($this->config["frontend_css"]);
        if (!empty($js) && preg_match('/\.css/i', $css)) {
            if (preg_match('/\.css$/i', $css)) {
                $css .= '?v=' . substr(md5($this->version), 0, 10);
            }
            $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
        }
    }
    function getOptions($defaultOptions,$prefix = ""){
        foreach ($defaultOptions as $key => &$option){
            $option = $this->getOption($prefix.$key,null,$option);
        }
        return $defaultOptions;
    }
}