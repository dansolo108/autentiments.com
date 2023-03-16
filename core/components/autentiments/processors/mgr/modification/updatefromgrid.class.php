<?php
class ModificationUpdateFromGridProcessor extends modObjectUpdateProcessor {
	public $objectType = 'Modification';
	public $classKey = 'Modification';
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
    public function afterSave()
    {
        $data = $this->getProperties();
        $defaultField = array_keys($this->object->_fieldMeta);
        foreach ($data as $key => $value){
            if(in_array($key,$defaultField))
                continue;
            $keys = explode(':',$key);
            if($keys[0] === 'store'){
                $remain = $this->modx->getObject('ModificationRemain',['modification_id'=>$this->object->get('id'),'store_id'=>$keys[1]]);
                if(empty($remain)){
                    $remain = $this->modx->newObject('ModificationRemain',['modification_id'=>$this->object->get('id'),'store_id'=>$keys[1]]);
                }
                $remain->set('remains',$value);
                $remain->save();
            }
            else if($keys[0] === 'option'){
                $option = $this->modx->getObject('ModificationDetail',['modification_id'=>$this->object->get('id'),'type_id'=>$keys[1]]);
                if(empty($option)){
                    $option = $this->modx->newObject('ModificationDetail',['modification_id'=>$this->object->get('id'),'type_id'=>$keys[1]]);
                }
                $option->set('value',$value);
                $option->save();
            }
        }
        return parent::afterSave();
    }
}
return 'ModificationUpdateFromGridProcessor';
