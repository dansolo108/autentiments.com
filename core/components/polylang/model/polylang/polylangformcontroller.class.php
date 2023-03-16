<?php
if (!class_exists('modManagerController')) {
    require_once MODX_CORE_PATH . 'model/modx/modmanagercontroller.class.php';
}

class PolylangFormController extends modManagerController
{

    public function process(array $scriptProperties = array())
    {

        $this->prepareLanguage();
        $tpl = $this->getTemplateFile();
        if ($this->isFailure) {
            $this->setPlaceholder('_e', $this->modx->error->failure($this->failureMessage));
            $content = $this->fetchTemplate('error.tpl');
        } else if (!empty($tpl)) {
            $content = $this->fetchTemplate($tpl);
        }
        return $content;
    }

    public function loadCustomCssJs()
    {
    }

    public function checkPermissions()
    {
        return true;
    }

    public function getPageTitle()
    {
        return '';
    }

    public function getTemplateFile()
    {
        return MODX_CORE_PATH . 'components/polylang/elements/templates/tvs.tpl';
    }
}