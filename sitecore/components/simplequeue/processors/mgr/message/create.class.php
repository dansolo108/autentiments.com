<?php

/**
 * Create an Item
 */
class sqMessageCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'message';
	public $classKey = 'sqMessage';
	public $languageTopics = array('simplequeue');
	//public $permission = 'create';


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$name = trim($this->getProperty('service'));
		if (empty($name)) {
			$this->modx->error->addField('service', $this->modx->lexicon('simplequeue_item_err_service'));
		}
		elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
			$this->modx->error->addField('name', $this->modx->lexicon('simplequeue_item_err_ae'));
		}

		return parent::beforeSet();
	}

}

return 'sqMessageCreateProcessor';