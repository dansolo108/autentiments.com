<?php


/**
 * Get an msoptionspriceOptions
 */
class msopModificationOptionCreateProcessor extends modProcessor
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
        /** @var  $rid */
        /** @var  $key */
        foreach (array('rid', 'key') as $k) {
            ${$k} = trim($this->getProperty($k));
            if (empty(${$k})) {
                $this->modx->error->addField($k, $this->modx->lexicon('msoptionsprice_err_ae'));
            }
        }

        $mid = (int)$this->getProperty('mid', 0);
        $value = trim($this->getProperty('value'));

        $options = $this->msoptionsprice->setProductOptions($rid, array($key => $value));
        $values = isset($options[$key]) ? $options[$key] : array();

        if (!in_array($value, $values)) {
            $this->modx->error->addField('key', $this->modx->lexicon('msoptionsprice_err_ae'));
            $this->modx->error->addField('value', $this->modx->lexicon('msoptionsprice_err_ae'));
        }

        if ($this->hasErrors()) {
            return $this->failure();
        }

        $this->modx->call('msopModificationOption', 'saveOptions',
            array(&$this->modx, $mid, $rid, array($key => $value)));

        return $this->success('', array($key => $values));
    }

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function doesAlreadyExist(array $criteria, $class = '')
    {
        $exist = false;
        if (empty($class)) {
            $class = $this->classKey;
        }
        $q = $this->modx->newQuery($class);
        $q->where($criteria);

        if ($q->prepare() AND $q->stmt->execute()) {
            $exist = $q->stmt->rowCount();
        }

        return $exist;
    }

}

return 'msopModificationOptionCreateProcessor';