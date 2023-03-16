<?php

class msOptionsPriceOnEmptyTrash extends msOptionsPricePlugin
{
    public function run()
    {
        $ids = (array)$this->modx->getOption('ids', $this->scriptProperties, array(), true);
        $this->modx->removeCollection('msopModification', array('rid:IN' => $ids));
        $this->modx->removeCollection('msopModificationOption', array('rid:IN' => $ids));
    }
}
