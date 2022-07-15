<?php

class mspcOrderGetProcessor extends modObjectProcessor
{
    public $objectType = 'mspcOrder';
    public $classKey = 'mspcOrder';
    public $primaryKeyField = 'id';
    public $secondaryKeyField = 'order_id';
    public $languageTopics = array('mspromocode:default');
    //public $permission = 'view';
    public $checkViewPermission = true;

    /** 
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize()
    {
        $where = $this->getProperty('where', '');
        $where = $this->modx->fromJSON($where);
        if (!empty($where) && is_array($where)) {
            $this->object = $this->modx->getObject($this->classKey, $where);
        }

        if (empty($this->object)) {
            $primaryKey = $this->getProperty($this->primaryKeyField, false);
            $secondaryKey = $this->getProperty($this->secondaryKeyField, false);
            if (empty($primaryKey) && empty($secondaryKey)) {
                return $this->modx->lexicon($this->objectType . '_err_ns');
            }
            if (!empty($primaryKey)) {
                $this->object = $this->modx->getObject($this->classKey, $primaryKey);
            } elseif (!empty($secondaryKey)) {
                $this->object = $this->modx->getObject($this->classKey, array($this->secondaryKeyField => $secondaryKey));
            }
            if (empty($this->object)) {
                return '';
            }

            if ($this->checkViewPermission &&
                $this->object instanceof modAccessibleObject &&
                !$this->object->checkPolicy('view')
            ) {
                return $this->modx->lexicon('access_denied');
            }

            return parent::initialize();
        } else {
            return true;
        }
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $this->beforeOutput();

        return $this->cleanup();
    }

    /**
     * Return the response
     * @return array
     */
    public function cleanup()
    {
        $array = $this->object->toArray('', true);

        return $this->success('', $array);
    }

    /**
     * Used for adding custom data in derivative types
     * @return void
     */
    public function beforeOutput()
    {
    }
}

return 'mspcOrderGetProcessor';
