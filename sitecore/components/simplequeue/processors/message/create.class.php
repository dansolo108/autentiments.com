<?php

require_once 'sqmessageprocessor.trait.php';
if (class_exists('sqMessageCreateProcessor')) {
    return 'sqMessageCreateProcessor';
}
/**
 * Create an Item
 */
class sqMessageCreateProcessor extends modObjectCreateProcessor {
    use sqMessageProcessorTrait;

	public $objectType = 'message';
	public $classKey = 'sqMessage';
	public $languageTopics = array('simplequeue');
	//public $permission = 'create';
    public $action = simpleQueue::SQ_CREATE;


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$name = trim($this->getProperty('service'));
		if (empty($name)) {
			$this->modx->error->addField('service', $this->modx->lexicon('simplequeue_item_err_service'));
		}
//		elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
//			$this->modx->error->addField('name', $this->modx->lexicon('simplequeue_item_err_ae'));
//		}

		return parent::beforeSet();
	}

	/**
	 * @return bool
	 */
	public function beforeSave()
	{
		$this->object->fromArray(array(
			'createdon' => time(),
			'createdby' => $this->modx->user->id,
		));
		return parent::beforeSave();
	}

}

return 'sqMessageCreateProcessor';