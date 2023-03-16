<?php
class PolylangPolylangProductOptionGetListProcessor extends modObjectGetListProcessor {
    public $languageTopics = array('polylang:default');
    public $classKey = 'PolylangProductOption';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;
    /** @var Polylang $polylang  */
    public $polylang ;

    public function initialize()
    {
       // $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function beforeQuery()
    {
        if ($this->getProperty('combo')) {
            $this->setProperty('limit', 0);
        }

        return parent::beforeQuery();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        /*$query = $this->getProperty('query');

        if (!empty($query)) {
            $c->where(array(
                'name:LIKE' => '%'.$query.'%',
                // 'OR:code:LIKE' => "%{$query}%",
            ));
        }*/

        /*if ($this->getProperty('combo')) {
            $c->where(array('enable' => 1));
        }*/

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
                    'title' => $this->modx->lexicon('polylang_menu_update'),
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
                    'title' => $this->modx->lexicon('polylang_menu_remove'),
                    'multiple' => $this->modx->lexicon('polylang_menu_multiple_remove'),
                    'action' => 'removeItem',
                    'button' => true,
                    'menu' => true,
                ),
            );
        }

        return $data;
    }
}
return 'PolylangPolylangProductOptionGetListProcessor';