<?php
class stikRemainsUpdateFromGridProcessor extends modObjectUpdateProcessor {
	public $objectType = 'stikRemains';
	public $classKey = 'stikRemains';
	public $languageTopics = array('resource','minishop2:default');
// 	public $beforeSaveEvent = 'msprOnBeforeChangeRemains';
// 	public $afterSaveEvent = 'msprOnChangeRemains';
	public $permission = 'msproduct_save';

	/** {@inheritDoc} */
	public function initialize() {
		$data = $this->modx->fromJSON($this->getProperty('data'));
		if (empty($data) || $data['remains'] === '') {
			return $this->modx->lexicon('invalid_data');
		}
		$this->setProperties($data);
		$this->unsetProperty('data');
		return parent::initialize();
	}

}
return 'stikRemainsUpdateFromGridProcessor';
