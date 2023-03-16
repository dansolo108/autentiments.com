<?php

class PolylangPolylangContentGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('polylang:default');
    public $classKey = 'PolylangContent';
    public $defaultSortField = 'Language.rank';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;
    /** @var Polylang $polylang */
    public $polylang;

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

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {

        $query = $this->getProperty('query');
        $c->leftJoin('PolylangLanguage', 'Language', '`Language`.`culture_key` = `PolylangContent`.`culture_key`');

        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select($this->modx->getSelectColumns('PolylangLanguage', 'Language', 'language_', array('name')));

        $c->where(array(
            $this->classKey . '.`content_id`' => $this->getProperty('content_id')
        ));

        if (!empty($query)) {
            $c->where(array(
                '`Language`.`name`:LIKE' => "%{$query}%",
                'OR:`Language`.`culture_key`:LIKE' => '%' . $query . '%',
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
                    'title' => $this->modx->lexicon('polylang_content_menu_update'),
                    'action' => 'updateItem',
                    'button' => true,
                    'menu' => true,
                ),
            );
            if (!$data['active']) {
                $data['actions'][] = array(
                    'cls' => array(
                        'menu' => 'yellow',
                        'button' => 'yellow',
                    ),
                    'icon' => 'icon icon-power-off',
                    'title' => $this->modx->lexicon('polylang_content_menu_enable'),
                    'multiple' => $this->modx->lexicon('polylang_content_menu_multiple_enable'),
                    'action' => 'enableItem',
                    'button' => true,
                    'menu' => true,
                );
            } else {
                $data['actions'][] = array(
                    'cls' => array(
                        'menu' => 'gray',
                        'button' => 'gray',
                    ),
                    'icon' => 'icon icon-power-off',
                    'title' => $this->modx->lexicon('polylang_content_menu_disable'),
                    'multiple' => $this->modx->lexicon('polylang_content_menu_multiple_disable'),
                    'action' => 'disableItem',
                    'button' => true,
                    'menu' => true,
                );
            }
            $data['actions'][] = array(
                'cls' => array(
                    'menu' => 'red',
                    'button' => 'red',
                ),
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('polylang_content_menu_remove'),
                'multiple' => $this->modx->lexicon('polylang_content_menu_multiple_remove'),
                'action' => 'removeItem',
                'button' => true,
                'menu' => true,
            );

        }

        return $data;
    }
}

return 'PolylangPolylangContentGetListProcessor';