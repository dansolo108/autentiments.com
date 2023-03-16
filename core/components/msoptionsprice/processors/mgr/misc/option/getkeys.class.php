<?php

/**
 * Get a list of msProductOption
 */
class msProductOptionGetKeysProcessor extends modObjectGetListProcessor
{
    public $objectType = 'msOption';
    public $classKey = 'msOption';
    public $defaultSortField = 'key';
    public $defaultSortDirection = 'ASC';
    public $languageTopics = array('default');
    public $permission = '';

    /** @var msoptionsprice $msoptionsprice */
    public $msoptionsprice;

    /** @var miniShop2 $miniShop2 */
    public $miniShop2;

    public function initialize()
    {
        $this->msoptionsprice = $this->modx->getService('msoptionsprice');
        $this->msoptionsprice->initialize($this->getProperty('context', $this->modx->context->key));

        $this->miniShop2 = $this->modx->getService('miniShop2');

        return parent::initialize();
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if ($rid = (int)$this->getProperty('rid')) {
            $c->innerJoin('msProduct', 'msProduct', 'msProduct.id = ' . $rid);
            $c->innerJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.option_id = msOption.id AND msCategoryOption.category_id = msProduct.parent AND msCategoryOption.active = 1');
            $c->select("msCategoryOption.rank,msCategoryOption.active");
            $c->sortby('msCategoryOption.rank', 'ASC');
        }

        $c->select($this->modx->getSelectColumns('msOption', 'msOption'));
        $c->groupby("{$this->classKey}.key");

        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                "{$this->classKey}.key:LIKE" => "%{$query}%",
            ));
        }

        return $c;
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        $this->modx->loadClass('msProductData');
        $this->miniShop2->loadPlugins();
        $meta = $this->modx->map['msProductData']['fieldMeta'];
        foreach ($meta as $k => $v) {
            if (in_array($k, array('article', 'image', 'thumb'))) {
                continue;
            }
            if (in_array($v['phptype'], array('json', 'string'))) {
                $array = array_merge_recursive(array(
                    array(
                        'key'     => $k,
                        'caption' => '',
                    ),
                ), $array);
            }
        }

        $includeOptions = $this->msoptionsprice->getOption('include_modification_options', null);
        $includeOptions = $this->msoptionsprice->explodeAndClean($includeOptions);

        $excludeOptions = $this->msoptionsprice->getOption('exclude_modification_options', null);
        $excludeOptions = $this->msoptionsprice->explodeAndClean($excludeOptions);

        $fields = array();
        foreach ($array as $v) {
            if (!empty($includeOptions)) {
                if (!in_array($v['key'], $includeOptions)) {
                    continue;
                }
            }
            if (!empty($excludeOptions)) {
                if (in_array($v['key'], $excludeOptions)) {
                    continue;
                }
            }
            $fields[] = array(
                'caption'    => $v['caption'],
                'product_id' => 0,
                'key'        => $v['key'],
                'value'      => '',
            );
        }
        $array = $fields;

        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'key'     => '',
                    'caption' => $this->modx->lexicon('msoptionsprice_all'),
                ),
            ), $array);
        }
        if ($this->getProperty('novalue')) {
            $array = array_merge_recursive(array(
                array(
                    'key'     => '',
                    'caption' => $this->modx->lexicon('msoptionsprice_no'),
                ),
            ), $array);
        }

        $start = (int)$this->getProperty('start', 0);
        $limit = (int)$this->getProperty('limit', 10);

        $count = count($array);
        $array = array_slice($array, $start, $limit);

        return parent::outputArray($array, $count);
    }


    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = array();

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select('key');

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        $c->limit('limit', 0);

        if ($c->prepare() AND $c->stmt->execute()) {
            $data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $array;
            $this->currentIndex++;
        }
        $list = $this->afterIteration($list);

        return $list;
    }

}

return 'msProductOptionGetKeysProcessor';