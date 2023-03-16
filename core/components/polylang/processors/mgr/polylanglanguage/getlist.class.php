<?php

class PolylangPolylangLanguageGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('polylang:default');
    public $classKey = 'PolylangLanguage';
    public $defaultSortField = 'rank';
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
        $combo = $this->getProperty('combo');
        $defaultLanguage = $this->polylang->getTools()->getDefaultLanguage();
        $excludeDefaultLanguage = $this->getProperty('excludeDefaultLanguage', 1);

        if ($combo && $excludeDefaultLanguage) {
            $c->where(array(
                '`culture_key`:!=' => $defaultLanguage,
            ));
        }
        if (!empty($query)) {
            $c->where(array(
                '`name`:LIKE' => '%' . $query . '%',
                'OR:`culture_key`:LIKE' => '%' . $query . '%',
                'OR:`group`:LIKE' => '%' . $query . '%',
            ));
        }
        if ($this->getProperty('combo')) {
            $c->where(array(
                '`active`' => 1,
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
                    'title' => $this->modx->lexicon('polylang_menu_update'),
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
                    'title' => $this->modx->lexicon('polylang_menu_enable'),
                    'multiple' => $this->modx->lexicon('polylang_menu_multiple_enable'),
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
                    'title' => $this->modx->lexicon('polylang_menu_disable'),
                    'multiple' => $this->modx->lexicon('polylang_menu_multiple_disable'),
                    'action' => 'disableItem',
                    'button' => true,
                    'menu' => true,
                );
            }
        }
        $data['actions'][] = array(
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
        );

        return $data;
    }
}

return 'PolylangPolylangLanguageGetListProcessor';