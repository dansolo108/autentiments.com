<?php


/**
 * Get a list of msopModificationOption
 */
class msopModificationOptionGetListProcessor extends modObjectGetListProcessor
{

    public $objectType = 'msopModificationOption';
    public $classKey = 'msopModificationOption';

    public $classMsOption = 'msOption';
    public $defaultSortField = 'key';
    public $defaultSortDirection = 'ASC';
    public $languageTopics = ['default', 'msoptionsprice'];
    public $permission = '';


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('msOption', 'msOption', 'msopModificationOption.key = msOption.key');

        // add sort
        if ($rid = (int)$this->getProperty('rid')) {
            $c->innerJoin('msProduct', 'msProduct', 'msProduct.id = ' . $rid);
            $c->leftJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.option_id = msOption.id AND msCategoryOption.category_id = msProduct.parent');
            $c->select("msCategoryOption.rank,msCategoryOption.active");
            $c->sortby('msCategoryOption.rank', 'ASC');
        }

        // groupby
        $c->groupby('msopModificationOption.key');
        // select
        $c->select('msOption.caption');

        $mid = $this->getProperty('mid');
        if (!in_array($mid, [null])) {
            $c->where("{$this->classKey}.mid='{$mid}'");
        }

        if (0) {
            $s = $c->prepare();
            $sql = $c->toSQL();
            $s->execute();
            $this->modx->log(1, print_r($sql, 1));
            $this->modx->log(1, print_r($s->errorInfo(), 1));
        }

        return $c;
    }


    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive([
                [
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionsprice_all'),
                ],
            ], $array);
        }
        if ($this->getProperty('novalue')) {
            $array = array_merge_recursive([
                [
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionsprice_no'),
                ],
            ], $array);
        }

        return parent::outputArray($array, $count);
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareArray(array $array)
    {
        /*if (!empty($array['key'])) {
            $classMsOption = 'msOption';
            $q = $this->modx->newQuery($classMsOption, array('key' => $array['key']));
            $q->select("{$classMsOption}.caption");
            if ($q->prepare() AND $q->stmt->execute()) {
                if ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $array['caption'] = $row['caption'];
                }
            }
        }*/

        if ($this->getProperty('combo')) {

        } else {

            $icon = 'icon';
            $array['actions'] = [];

            // Remove
            $array['actions'][] = [
                'cls'    => '',
                'icon'   => "$icon $icon-trash-o red",
                'title'  => $this->modx->lexicon('msoptionsprice_action_option_remove'),
                'action' => 'removeOption',
                'button' => true,
                'menu'   => true,
            ];

            // sep
            $array['actions'][] = [
                'cls'    => '',
                'icon'   => '',
                'title'  => '',
                'action' => 'sep',
                'button' => false,
                'menu'   => true,
            ];

            // Remove
            $array['actions'][] = [
                'cls'    => '',
                'icon'   => "$icon $icon-trash-o",
                'title'  => $this->modx->lexicon('msoptionsprice_action_remove'),
                'action' => 'remove',
                'button' => true,
                'menu'   => true,
            ];

        }

        return $array;
    }


    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = [];
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            [$this->getProperty('sort')]);
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

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
        $list = [];
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $this->prepareArray($array);
            $this->currentIndex++;
        }
        $list = $this->afterIteration($list);

        return $list;
    }

}


return 'msopModificationOptionGetListProcessor';