<?php
/**
 * Get a list of Remains
 */
class stikRemainsGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'stikRemains';
	public $classKey = 'stikRemains';
	public $languageTopics = ['stikproductremains:manager'];
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
		$c->select($this->modx->getSelectColumns('stikRemains','stikRemains'));
        $c->select([
            'Store.name as store_name'
        ]);
        $c->leftJoin('stikStore','Store', 'stikRemains.store_id = Store.id');
		$c->leftJoin('msProduct','msProduct', 'stikRemains.product_id = msProduct.id');
		$c->where([
			'product_id' => $this->getProperty('product_id')
			,'msProduct.class_key' => 'msProduct'
		]);
		
		return $c;
	}

	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object) {
		$resourceArray = $object->toArray('', true);
		if (is_iterable($resourceArray['options'])) {
			foreach ( $this->modx->fromJSON($resourceArray['options']) as $option => $value ) {
				$resourceArray[$option] = $value;
			}
		}

		return $resourceArray;
	}

}

return 'stikRemainsGetListProcessor';