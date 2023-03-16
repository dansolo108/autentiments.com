<?php

class MultiCurrencyGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('msmulticurrency:multicurrency');
    public $classKey = 'MultiCurrency';
    public $defaultSortField = 'code';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;

    public function beforeQuery()
    {
        if ($this->getProperty('combo')) {
            $this->setProperty('limit', 0);
        }
        return parent::beforeQuery();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        $exclude = $this->getProperty('exclude', 0);
        if (!empty($query)) {
            $c->where(
                array(
                    'name:LIKE' => '%' . $query . '%',
                    'OR:code:LIKE' => "%{$query}%",
                    'OR:symbol_left:LIKE' => "%{$query}%",
                    'OR:symbol_right:LIKE' => "%{$query}%",
                ));
        }
        if ($exclude) {
            $c->where(array('id:!=' => $exclude));
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
                    'title' => $this->modx->lexicon('msmulticurrency.menu.update'),
                    'action' => 'updateCurrency',
                    'button' => true,
                    'menu' => true,
                ),
                array(
                    'cls' => array(
                        'menu' => 'red',
                        'button' => 'red',
                    ),
                    'icon' => 'icon icon-trash-o',
                    'title' => $this->modx->lexicon('msmulticurrency.menu.remove'),
                    'multiple' => $this->modx->lexicon('msmulticurrency.menu.multiple_remove'),
                    'action' => 'removeCurrency',
                    'button' => true,
                    'menu' => true,
                ),
            );
        }
        return $data;
    }

}

return 'MultiCurrencyGetListProcessor';