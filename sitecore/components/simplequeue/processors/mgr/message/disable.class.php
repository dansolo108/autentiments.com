<?php

/**
 * Disable an Item
 */
class sqMessageDisableProcessor extends modObjectProcessor {
	public $objectType = 'message';
	public $classKey = 'sqMessage';
	public $languageTopics = array('simplequeue');
	//public $permission = 'save';


	/**
	 * @return array|string
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		$ids = $this->modx->fromJSON($this->getProperty('ids'));
		if (empty($ids)) {
			return $this->failure($this->modx->lexicon('simplequeue_sqmessage_err_ns'));
		}

		foreach ($ids as $id) {
			/** @var sqMessage $object */
			if (!$object = $this->modx->getObject($this->classKey, $id)) {
				return $this->failure($this->modx->lexicon('simplequeue_sqmessage_err_nf'));
			}

			$object->set('processed', true);
			$object->save();
		}

		return $this->success();
	}

}

return 'sqMessageDisableProcessor';
