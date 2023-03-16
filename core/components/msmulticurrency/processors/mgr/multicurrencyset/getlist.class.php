<?php
class MultiCurrencySetGetListProcessor extends modObjectGetListProcessor {
    public $languageTopics = array('msmulticurrency:default','msmulticurrency:multicurrencyset');
    public $classKey = 'MultiCurrencySet';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;
    /** @var MsMC $msmc  */
    public $msmc ;

    public function initialize()
    {
       // $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query');

        if (!empty($query)) {
            $c->where(array(
                'name:LIKE' => '%'.$query.'%',
                // 'OR:code:LIKE' => "%{$query}%",
            ));
        }

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $data = $object->toArray();

        if (!$this->getProperty('combo')) {
            $data['actions'] = array(
                array(
                    'cls' => array(
                        'menu' => 'green',
                        'button' => 'green',
                    ),
                    'icon' => 'icon icon-edit',
                    'title' => $this->modx->lexicon('msmulticurrency.set.menu.update'),
                    'action' => 'updateItem',
                    'button' => true,
                    'menu' => true,
                ),
                array(
                    'cls' => array(
                        'menu' => 'red',
                        'button' => 'red',
                    ),
                    'icon' => 'icon icon-trash-o',
                    'title' => $this->modx->lexicon('msmulticurrency.set.menu.remove'),
                    'multiple' => $this->modx->lexicon('msmulticurrency.set.menu.multiple_remove'),
                    'action' => 'removeItem',
                    'button' => true,
                    'menu' => true,
                ),
            );
        }

        return $data;
    }
}
return 'MultiCurrencySetGetListProcessor';