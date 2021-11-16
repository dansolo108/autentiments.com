<?php

class smsControllersMainManagerController extends modExtraManagerController
{

    public $sms;

    public function initialize()
    {
        $this->sms = $this->modx->getService('sms', 'sms', MODX_CORE_PATH . 'components/sms/model/sms/');
        parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('sms:default');
    }

    public function checkPermissions()
    {
        return true;
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('sms_title');
    }

    public function loadCustomCssJs()
    {
        $this->addCss($this->sms->config['cssUrl'] . 'mgr/main.css?v='.$this->sms->version);
        $this->addCss($this->sms->config['cssUrl'] . 'mgr/bootstrap.buttons.css?v='.$this->sms->version);
        $this->addJavascript($this->sms->config['jsUrl'] . 'mgr/sms.js?v='.$this->sms->version);
        $this->addJavascript($this->sms->config['jsUrl'] . 'mgr/misc/utils.js?v='.$this->sms->version);
        $this->addJavascript($this->sms->config['jsUrl'] . 'mgr/misc/combo.js?v='.$this->sms->version);
        $this->addJavascript($this->sms->config['jsUrl'] . 'mgr/widgets/send/grid.js?v='.$this->sms->version);
        $this->addJavascript($this->sms->config['jsUrl'] . 'mgr/widgets/send/windows.js?v='.$this->sms->version);
        $this->addJavascript($this->sms->config['jsUrl'] . 'mgr/widgets/panel.js?v='.$this->sms->version);
        $this->addJavascript($this->sms->config['jsUrl'] . 'mgr/sections/page.js?v='.$this->sms->version);
        $this->addHtml('<script type="text/javascript">
        sms.config = ' . json_encode($this->sms->config) . ';
        sms.config.connector_url = "' . $this->sms->config['connectorUrl'] . '";
        sms.config.fields = {
            send: ' . json_encode(array_map('trim', explode(',', $this->sms->config['smsSendFields']))) . ',
        };
        Ext.onReady(function() {
            MODx.load({ xtype: "sms-page-main"});
        });
        </script>
        ');
    }

    public function getTemplateFile()
    {
        return $this->sms->config['templatesPath'] . 'main.tpl';
    }
}