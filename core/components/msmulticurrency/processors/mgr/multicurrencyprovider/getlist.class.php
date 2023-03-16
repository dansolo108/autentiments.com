<?php

class MultiCurrencyProviderGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('msmulticurrency:multicurrency');
    public $classKey = 'MultiCurrencyProvider';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {

        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(
                array(
                    'name:LIKE' => '%' . $query . '%',
                    'OR:class_name:LIKE' => "%{$query}%",
                ));
        }
        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $data = $object->toArray();

        $data['actions'] = array(
            array(
                'cls' => array(
                    'menu' => 'green',
                    'button' => 'green',
                ),
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('msmulticurrency.menu.provider_update'),
                'action' => 'updateProvider',
                'button' => true,
                'menu' => true,
            ),
            array(
                'cls' => array(
                    'menu' => 'blue',
                    'button' => 'blue',
                ),
                'icon' => 'icon icon-info',
                'title' => $this->modx->lexicon('msmulticurrency.menu.provider_currency'),
                'action' => 'currencyProvider',
                'button' => true,
                'menu' => true,
            ),
            array(
                'cls' => array(
                    'menu' => 'red',
                    'button' => 'red',
                ),
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('msmulticurrency.menu.provider_remove'),
                'multiple' => $this->modx->lexicon('msmulticurrency.menu.provider_multiple_remove'),
                'action' => 'removeProvider',
                'button' => true,
                'menu' => true,
            ),
        );

        return $data;
    }
}

return 'MultiCurrencyProviderGetListProcessor';