<?php

class msOptionsPriceOnResourceDuplicate extends msOptionsPricePlugin
{
    public function run()
    {
        /** @var modResource $resource */
        $newResource = $this->modx->getOption('newResource', $this->scriptProperties, null, true);
        $oldResource = $this->modx->getOption('oldResource', $this->scriptProperties, null, true);
        if (
            !$this->msoptionsprice->getOption('create_modification_with_duplicate', null, true, false)
            OR
            !$newResource
            OR
            !$oldResource
            OR
            !$this->msoptionsprice->isWorkingClassKey($newResource)
            OR
            !$this->msoptionsprice->isWorkingTemplates($newResource)
        ) {
            return;
        }

        /* get old $modifications */
        $oldModifications = $this->modx->call('msopModification', 'getProductModification',
            array(&$this->modx, $oldResource->get('id')));

        /* save new $modifications */
        $newModifications = $this->modx->call('msopModification', 'saveProductModification',
            array(&$this->modx, $newResource->get('id'), $oldModifications));
        
        /* TODO images */
        
    }
}
