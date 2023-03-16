<?php

class MultiCurrencySetMemberGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('msmulticurrency:default', 'msmulticurrency:multicurrencysetmember', 'msmulticurrency:ms2');
    public $classKey = 'MultiCurrencySetMember';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;
    /** @var MsMC $msmc */
    public $msmc;

    public function initialize()
    {
        // $this->msmc = $this->modx->getService('msmulticurrency', 'MsMC');
        return parent::initialize();
    }

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
        $sid = $this->getProperty('sid');
        $exclude = $this->getProperty('exclude', 0);

        $c->leftJoin('MultiCurrency', 'MultiCurrency', '`MultiCurrency`.`id` = `MultiCurrencySetMember`.`cid`');
        $c->leftJoin('MultiCurrencySet', 'MultiCurrencySet', '`MultiCurrencySet`.`id` = `MultiCurrencySetMember`.`sid`');

        $c->select($this->modx->getSelectColumns('MultiCurrencySetMember', 'MultiCurrencySetMember'));
        $c->select($this->modx->getSelectColumns('MultiCurrency', 'MultiCurrency', 'currency_', array('id'), true));
        $c->select($this->modx->getSelectColumns('MultiCurrencySet', 'MultiCurrencySet', 'set_', array('id', 'properties'), true));

        if (!empty($query)) {
            $c->where(
                array(
                    'MultiCurrency.name:LIKE' => '%' . $query . '%',
                    'OR:MultiCurrency.code:LIKE' => "%{$query}%",
                    'OR:MultiCurrency.symbol_left:LIKE' => "%{$query}%",
                    'OR:MultiCurrency.symbol_right:LIKE' => "%{$query}%",
                ));
        }

        if ($this->getProperty('combo')) {
            $c->where(array('enable' => 1));
        }

        if ($sid) {
            $c->where(array('sid' => $sid));
        }

        if ($exclude) {
            $c->where(array('cid:!=' => $exclude));
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
                    'title' => $this->modx->lexicon('msmulticurrency.setmember.menu.update'),
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
                    'title' => $this->modx->lexicon('msmulticurrency.setmember.menu.remove'),
                    'multiple' => $this->modx->lexicon('msmulticurrency.setmember.menu.multiple_remove'),
                    'action' => 'removeItem',
                    'button' => true,
                    'menu' => true,
                ),
            );
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {
        $beforeQuery = $this->beforeQuery();
        if ($beforeQuery !== true) {
            return $this->failure($beforeQuery);
        }
        $data = $this->getData();
        $list = $this->iterate($data);
        $exclude = $this->getProperty('exclude', 0);
        if ($this->getProperty('combo') && $exclude != -1) {
            array_unshift($list, array(
                'cid' => 0,
                'val' => '',
                'currency_name' => $this->modx->lexicon('ms2_product_currency_default'),
                'currency_code' => '',
                'currency_symbol_left' => '',
                'currency_symbol_right' => '',
            ));
            $data['total']++;
        }
        return $this->outputArray($list, $data['total']);
    }
}

return 'MultiCurrencySetMemberGetListProcessor';