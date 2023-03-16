<?php

class mspcActionUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'mspcAction';
    public $classKey = 'mspcAction';
    public $languageTopics = array('mspromocode:default');

    //public $permission = 'save';

    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        if (empty($id)) {
            return $this->modx->lexicon('mspromocode_err_ns');
        }

        $props['updatedon'] = date('Y-m-d H:i:s');
        $this->setProperties($props);

        $props = $this->getProperties();
        //$this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($props,1) );
        foreach ($props as $k => $v) {
            $props[$k] = $this->modx->mspromocode->sanitize($k, $v);
        }
        //$this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($props,1) );
        $this->setProperties($props);

        $required = array('name', 'discount');
        foreach ($required as $v) {
            if ($this->getProperty($v) == '') {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_err_field'));
            }
        }

        $unique = array('name');
        foreach ($unique as $v) {
            if ($this->modx->getCount($this->classKey, array($v => $this->getProperty($v), 'id:!=' => $id))) {
                $this->modx->error->addField($v, $this->modx->lexicon('mspromocode_err_ae'));
            }
        }

        if ($this->getProperty('ref') && $this->modx->getCount($this->classKey, array('ref' => true, 'id:!=' => $id))) {
            return $this->modx->lexicon('mspromocode_err_action_ref');
        }

        return parent::beforeSet();
    }

    public function afterSave()
    {
        $row = $this->object->toArray('', true);

        // $this->modx->addPackage('mspromocode', MODX_CORE_PATH . 'components/mspromocode/model/'); // подключаем модель компонента msPromoCode

        $resp_getlist = $this->modx->runProcessor('coupon/getlist', array(
            'limit' => 99999,
            'owner' => 'action',
            'action_id' => $row['id'],
            'action_ref' => $row['ref'],
            // 'discount'	=> $row['discount'],
        ), array('processors_path' => MODX_CORE_PATH . 'components/mspromocode/processors/mgr/'));

        $getlist = $this->modx->fromJSON($resp_getlist->response);
        // $this->modx->log(1, print_r($getlist, 1));

        foreach ($getlist['results'] as $c) {
            if (!$c['freeze']) {
                $resp_update = $this->modx->runProcessor('coupon/update', array(
                    'id' => $c['id'],
                    'code' => $c['code'],
                    'discount' => $row['discount'],
                    'begins' => $row['begins'],
                    'ends' => $row['ends'],
                ), array('processors_path' => MODX_CORE_PATH . 'components/mspromocode/processors/mgr/'));

                $update = $resp_update->response;
                // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($update,1) );
            }
        }

        return parent::afterSave();
    }
}

return 'mspcActionUpdateProcessor';
