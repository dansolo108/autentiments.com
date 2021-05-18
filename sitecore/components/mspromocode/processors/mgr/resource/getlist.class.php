<?php

class mspcResourceGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'mspcResource';
    public $classKey = 'mspcResource';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    //public $permission = 'list';

    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $owner = $this->getProperty('owner', 'coupon');
        $obj_id = (int) $this->getProperty('obj_id', 0);
        if (!empty($obj_id)) {
            $c->where(array("{$this->classKey}.{$owner}_id" => $obj_id));
        }

        $query = trim($this->getProperty('query', ''));
        if ($query != '') {
            $c->where(array(
                'Product.name:LIKE' => "%{$query}%",
                'OR:Product.description:LIKE' => "%{$query}%",
                'OR:Category.name:LIKE' => "%{$query}%",
                'OR:Category.description:LIKE' => "%{$query}%",
            ));
        }

        $type = $this->getProperty('type', false);
        if (!empty($type)) {
            $c->where(array("{$this->classKey}.type" => $type));
        }

        $c->leftJoin('msProduct', 'Product', "Product.id = {$this->classKey}.resource_id AND {$this->classKey}.type = 'product'");
        $c->leftJoin('msCategory', 'Category', "Category.id = {$this->classKey}.resource_id AND {$this->classKey}.type = 'category'");

        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select($this->modx->getSelectColumns('msProduct', 'Product', 'p_', array('id', 'pagetitle', 'published')));
        //$c->select($this->modx->getSelectColumns('msProductData','Data', '', array('id'), true));
        $c->select($this->modx->getSelectColumns('msCategory', 'Category', 'c_', array('id', 'pagetitle', 'published')));

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray('', true);

        $array['rid'] = (int) (!empty($array['p_id']) ? $array['p_id'] : $array['c_id']);
        $array['pagetitle'] = !empty($array['p_pagetitle']) ? $array['p_pagetitle'] : $array['c_pagetitle'];
        $array['published'] = (int) ($array['p_published'] != '' ? $array['p_published'] : $array['c_published']);

        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($array,1));

        $array['actions'] = array();

        // Отвязать
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-chain-broken action-red',
            'title' => $this->modx->lexicon('mspromocode_resource_detach'),
            'multiple' => $this->modx->lexicon('mspromocode_resources_detach'),
            'action' => 'detachResource',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }
}

return 'mspcResourceGetListProcessor';
