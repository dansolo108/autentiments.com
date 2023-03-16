<?php

class PolylangElementTvGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('polylang:default');
    public $classKey = 'modTemplateVar';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        $type = $this->getProperty('type', '');
        $onlyPolylang = $this->getProperty('onlyPolylang', 0);
        $type = $this->polylang->getTools()->explodeAndClean($type);

        if (!empty($query)) {
            $c->where(array(
                'name:LIKE' => '%' . $query . '%',
                'OR:caption:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
            ));
        }
        if (!empty($type)) {
            $c->where(array(
                'type:IN' => $type
            ));
        }
        if ($onlyPolylang) {
            $c->where(array(
                'polylang_enabled' => 1
            ));
        }
        return $c;
    }
}

return 'PolylangElementTvGetListProcessor';