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
    public $languageTopics = array('default', 'msoptionsprice');
    public $permission = '';

    //msopModificationOption

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin($this->classMsOption, $this->classMsOption, "{$this->classKey}.key = {$this->classMsOption}.key");

        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select($this->modx->getSelectColumns($this->classMsOption, $this->classMsOption, '', array('caption'), false));

        $mid = $this->getProperty('mid');
        if (!in_array($mid, array(null))) {
            $c->where("{$this->classKey}.mid='{$mid}'");
        }

        return $c;
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionsprice_all')
                )
            ), $array);
        }
        if ($this->getProperty('novalue')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionsprice_no')
                )
            ), $array);
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
        if ($this->getProperty('combo')) {

        } else {

            $icon = 'icon';
            $array['actions'] = array();

            // Remove
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-trash-o red",
                'title'  => $this->modx->lexicon('msoptionsprice_action_option_remove'),
                'action' => 'removeOption',
                'button' => true,
                'menu'   => true,
            );

            // sep
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => '',
                'title'  => '',
                'action' => 'sep',
                'button' => false,
                'menu'   => true,
            );

            // Remove
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-trash-o",
                'title'  => $this->modx->lexicon('msoptionsprice_action_remove'),
                'action' => 'remove',
                'button' => true,
                'menu'   => true,
            );

        }

        return $array;
    }

    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
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
        $list = array();
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