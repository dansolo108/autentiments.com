<?php

class msOptionsPriceOnDocFormPrerender extends msOptionsPricePlugin
{
    public function run()
    {
        $mode = $this->modx->getOption('mode', $this->scriptProperties, modSystemEvent::MODE_NEW, true);
        if ($mode == modSystemEvent::MODE_NEW) {
            return;
        }

        /** @var modResource $resource */
        $resource = $this->modx->getOption('resource', $this->scriptProperties, null, true);
        if (
            !$resource
            OR
            !$this->msoptionsprice->isWorkingClassKey($resource)
            OR
            !$this->msoptionsprice->isWorkingTemplates($resource)
        ) {
            return;
        }

        $this->msoptionsprice->loadControllerJsCss($this->modx->controller, array(
            'css'             => true,
            'config'          => true,
            'tools'           => true,
            'option'          => true,
            'modification'    => true,
            'gallery'         => true,
            'resource/inject' => true,
        ));
    }
}