<?php

class msOptionsPriceOnDocFormSave extends msOptionsPricePlugin
{
    public function run()
    {
        return;

        /** @var modResource $resource */
        $resource = $this->modx->getOption('resource', $this->scriptProperties, null, true);
        if (
            !$resource
            OR
            !$this->msoptionsprice->isWorkingTemplates($resource)
        ) {
            return;
        }

        //$data = $resource->toArray();
        
    }
}