<?php

/**
 * Create an Item
 */
class simpleQueueOfficeItemCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'simpleQueueItem';
	public $classKey = 'simpleQueueItem';
	public $languageTopics = array('simplequeue');
	//public $permission = 'create';


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('simplequeue_item_err_name'));
		}
		elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
			$this->modx->error->addField('name', $this->modx->lexicon('simplequeue_item_err_ae'));
		}

		return parent::beforeSet();
	}

}

return 'simpleQueueOfficeItemCreateProcessor';