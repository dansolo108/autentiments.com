<?php

/**
 * Class msoptionspriceMainController
 */
abstract class msoptionspriceMainController extends modExtraManagerController
{
    /** @var msoptionsprice $msoptionsprice */
    public $msoptionsprice;


    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('msoptionsprice_core_path', null,
            $this->modx->getOption('core_path') . 'components/msoptionsprice/');
        require_once $corePath . 'model/msoptionsprice/msoptionsprice.class.php';

        $this->msoptionsprice = new msoptionsprice($this->modx);
        $this->addCss($this->msoptionsprice->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->msoptionsprice->config['jsUrl'] . 'mgr/msoptionsprice.js');
        $this->addHtml('
		<script type="text/javascript">
			msoptionsprice.config = ' . $this->modx->toJSON($this->msoptionsprice->config) . ';
			msoptionsprice.config.connector_url = "' . $this->msoptionsprice->config['connectorUrl'] . '";
		</script>
		');

        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('msoptionsprice:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends msoptionspriceMainController
{

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}