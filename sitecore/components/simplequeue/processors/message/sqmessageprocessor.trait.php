<?php
/**
 * Created by PhpStorm.
 * User: mvoevodskiy
 * Date: 26.11.17
 * Time: 10:29
 */

trait sqMessageProcessorTrait {

    public $stateBefore = array();
    public $stateAfter = array();
    /* @var modX $modx */
    public $modx;
    /** @var simpleQueue */
    public $sq = null;

    public function initialize()
    {
        if (!$this->getSQ()) {
            return $this->failure($this->modx->lexicon('simplequeue_service_nf'));
        }
        return parent::initialize();
    }

    /**
     * Getting simpleQueue service
     *
     * @return bool|string
     */
    public function getSQ()
    {
        if (!$this->sq = $this->modx->getService(
            'simplequeue',
            'simpleQueue',
            $this->modx->getOption('simplequeue_core_path', null, $this->modx->getOption('core_path') . 'components/simplequeue/') . 'model/simplequeue/',
            array())
        ) {
            return 'Could not load simpleQueue class!';
        }
        return true;
    }

    public function beforeSet()
    {
        $this->prepareLog($this->object, simpleQueue::SQ_LOG_BEFORE);
        return parent::beforeSet();
    }

    public function afterSave()
    {
        $this->prepareLog($this->object, simpleQueue::SQ_LOG_AFTER);
        $this->addLog($this->object->get('id'));
        return parent::afterSave();
    }

    /**
     * @param array|sqMessage $msg
     * @param string $place
     */
    public function prepareLog($msg, $place)
    {
        if (is_object($msg)) {
            $msg = $msg->toArray();
        }
        $var = 'state' . ucfirst($place);
        $this->$var = array(
            'processed' => $msg['processed'],
            'status' => $msg['status'],
            'properties' => $msg['properties'],
        );
        return $this->$var;
    }

    /**
     * @param $id
     * @param $action
     */
    public function addLog($id)
    {
        if (!$this->modx->getOption('simplequeue_log')) {
            return;
        }

        $closed = false;
        $diff = array();
        foreach ($this->stateAfter as $k => $v) {
            if (!isset($this->stateBefore[$k]) or $v != $this->stateBefore[$k]) {
                if ($k == 'processed' and $v == 1) {
                    $closed = true;
                } else {
                    $diff[$k] = $v;
                }
            }
        }
        if (!empty($diff)) {
            $this->sq->addLog($id, $this->action, $diff);
        }
        if ($closed or $this->action == simpleQueue::SQ_CLOSE) {
            $this->sq->addLog($id, simpleQueue::SQ_CLOSE);
        }
    }
}