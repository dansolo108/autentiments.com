<?php


/**
 * Get an msoptionspriceValues
 */
class msopModificationOptionRemoveProcessor extends modProcessor
{
    public $classKey = 'msopModificationOption';
    public $classField = 'msopModificationOption';
    public $permission = '';

    /** @var msoptionsprice $msoptionsprice */
    public $msoptionsprice;

    public function initialize()
    {
        $this->msoptionsprice = $this->modx->getService('msoptionsprice');
        $this->msoptionsprice->initialize($this->getProperty('context', $this->modx->context->key));

        return parent::initialize();
    }

    public function process()
    {
        $ids = $this->modx->fromJSON($this->getProperty('ids', '[]'));
        @list($mid, $rid, $key, $value) = $this->getProperty('id', $ids[0]);

        $this->modx->call('msopModificationOption', 'removeOptions', array(&$this->modx, $mid, $rid, $key));

        if ($this->getProperty('field_name')) {
            $options = $this->msoptionsprice->removeProductOptions($rid, array($key => $value));
            $values = isset($options[$key]) ? $options[$key] : array();

            return $this->success('', array($key => $values));
        }

        return $this->success('');
    }

}

return 'msopModificationOptionRemoveProcessor';
