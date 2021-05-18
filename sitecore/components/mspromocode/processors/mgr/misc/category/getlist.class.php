<?php

class msCategoryGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msCategory';
    public $languageTopics = array('default', 'minishop2:product');
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';
    public $parent = 0;

    /** {@inheritdoc} */
    public function initialize()
    {
        if (!$this->getProperty('limit')) {
            $this->setProperty('limit', 20);
        }

        return parent::initialize();
    }

    /** {@inheritdoc} */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $include_ids = $this->getProperty('include_ids');
        if (!empty($include_ids) || $include_ids === '0') {
            $include_ids = explode(',', $include_ids);
            $c->where(array('id:IN' => $include_ids));
        }

        $exclude_ids = $this->getProperty('exclude_ids', '');
        if (!empty($exclude_ids)) {
            $exclude_ids = explode(',', $exclude_ids);
            $c->where(array('id:NOT IN' => $exclude_ids));
        }

        $published = $this->getProperty('published', '');
        if ($published != '') {
            $c->where(array('published' => !empty($published)));
        }

        $c->where(array('class_key' => 'msCategory'));
        $c->leftJoin('msCategory', 'Category', 'Category.id = msCategory.parent');
        if ($this->getProperty('combo')) {
            $c->select('msCategory.id,msCategory.pagetitle,msCategory.context_key,msCategory.published');
        } else {
            $c->select($this->modx->getSelectColumns('msCategory', 'msCategory'));
            $c->select($this->modx->getSelectColumns('msCategory', 'Category', 'category_', array('pagetitle')));
        }
        if ($query = $this->getProperty('query', null)) {
            $queryWhere = array(
                'msCategory.id' => $query, 'OR:msCategory.pagetitle:LIKE' => '%'.$query.'%', 'OR:Category.pagetitle:LIKE' => '%'.$query.'%',
            );
            $c->where($queryWhere);
        }

        // $c->prepare();
        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($c->toSQL(),1));

        return $c;
    }

    /** {@inheritdoc} */
    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $c->groupby($this->classKey.'.id');

        return $c;
    }

    /** {@inheritdoc} */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '', array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        if ($c->prepare() && $c->stmt->execute()) {
            $data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    /** {@inheritdoc} */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /* @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $this->prepareArray($array);
            ++$this->currentIndex;
        }
        $list = $this->afterIteration($list);

        return $list;
    }

    /** {@inheritdoc} */
    public function prepareArray(array $resourceArray)
    {
        if ($this->getProperty('combo')) {
            $resourceArray['parents'] = array();
            $parents = $this->modx->getParentIds($resourceArray['id'], 2, array('context' => $resourceArray['context_key']));
            if (empty($parents[count($parents) - 1])) {
                unset($parents[count($parents) - 1]);
            }
            if (!empty($parents) && is_array($parents)) {
                $q = $this->modx->newQuery('msCategory', array('id:IN' => $parents));
                $q->select('id,pagetitle');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $key = array_search($row['id'], $parents);
                        if ($key !== false) {
                            $parents[$key] = $row;
                        }
                    }
                }
                $resourceArray['parents'] = array_reverse($parents);
            }
        }

        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($resourceArray,1));

        return $resourceArray;
    }
}

return 'msCategoryGetListProcessor';
