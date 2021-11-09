<?php

require_once 'sqmessageprocessor.trait.php';

/**
 * Close the Message(s)
 */
class sqMessageCloseProcessor extends modObjectProcessor {
    use sqMessageProcessorTrait;

	public $objectType = 'message';
	public $classKey = 'sqMessage';
	public $languageTopics = array('simplequeue');
	//public $permission = 'save';
    public $action = simpleQueue::SQ_CLOSE;

	/**
	 * @return array|string
	 */
	public function process() {
	    if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		$ids = $this->modx->fromJSON($this->getProperty('ids'));
		if (empty($ids)) {
		    $ids = array($this->getProperty('id'));
        }
		if (empty($ids)) {
			return $this->failure($this->modx->lexicon('simplequeue_sqmessage_err_ns'));
		}

		foreach ($ids as $id) {
			/** @var sqMessage $object */
			if (!$object = $this->modx->getObject($this->classKey, $id)) {
				return $this->failure($this->modx->lexicon('simplequeue_sqmessage_err_nf'));
			}

			$object->set('processed', true);
			if ($object->save()) {
			    $this->stateAfter['processed'] = true;
                $this->addLog($object->id);
            }
		}

		return $this->success();
	}

}

return 'sqMessageCloseProcessor';
