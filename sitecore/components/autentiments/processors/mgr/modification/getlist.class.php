<?php
/**
 * Get a list of Remains
 */
class ModificationGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'Modification';
	public $classKey = 'Modification';
	public $languageTopics = ['autentiments:manager'];
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'ASC';

	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->getProperty('product_id'))
			return $this->modx->lexicon('invalid_data');
		if (!$this->getProperty('limit'))
			$this->setProperty('limit', 20);

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->select($this->modx->getSelectColumns('Modification','Modification'));
		$c->where([
			'product_id' => $this->getProperty('product_id')
		]);
//        $c->prepare();
//		$this->modx->log(1,print_r($c->toSQL(),1));
		return $c;
	}
    /**
     * Prepare the row for iteration
     * @param Modification $object
     * @return array
     */

    public function prepareRow(xPDOObject $object) {
        $obj = $object->toArray();
        /** @var ModificationDetail $detail */
        foreach($object->getMany('Details') as $detail){
            $obj['option:'.$detail->get('type_id')] = $detail->get('value');
        }
        /** @var ModificationRemain $remain */
        foreach($object->getMany('Remains') as $remain){
            $obj['store:'.$remain->get('store_id')] = $remain->get('remains');
        }
        $color = $object->getDetail('color');
        if($color) {
            $product = $object->getOne('Product');
            $files = $product->getOne('Data')->getMany('Files', ['description' => $color->get('value')]);
            if($files){
                $preview = array_keys($files)[0];
                $min = $files[$preview]->get('rank');
                foreach ($files as $key => $file){
                    if(min($min,(int)$file->get('rank')) < $min){
                        $min = $file->get('rank');
                        $preview = $key;
                    }
                }
                $obj['preview'] = $files[$preview]->get('url');
            }
        }
        return $obj;
    }
}

return 'ModificationGetListProcessor';