<?php

class PolylangPolylangTvTmplvarsGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('polylang:default');
    public $classKey = 'PolylangTvTmplvars';
    public $defaultSortField = 'id';
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
        $tvId = $this->getProperty('tmplvarid');
        $cultureKey = $this->getProperty('culture_key');


        $c->leftJoin('modTemplateVar', 'Tv', '`Tv`.`id` = `PolylangTvTmplvars`.`tmplvarid`');
        $c->leftJoin('PolylangLanguage', 'Language', '`Language`.`culture_key` = `PolylangTvTmplvars`.`culture_key`');

        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select($this->modx->getSelectColumns('modTemplateVar', 'Tv', 'tv_'));
        $c->select($this->modx->getSelectColumns('PolylangLanguage', 'Language', 'language_', array('name')));

        if ($cultureKey) {
            $c->where(array(
                '`PolylangTvTmplvars`.`culture_key`' => $cultureKey,
            ));
        }
        if ($tvId) {
            $c->where(array(
                '`PolylangTvTmplvars`.`tmplvarid`' => $tvId,
            ));
        }

        if (!empty($query)) {
            $c->where(array(
                '`PolylangTvTmplvars`.`values`:LIKE' => "%{$query}%",
                'OR:`PolylangTvTmplvars`.`default_text`:LIKE' => '%' . $query . '%',
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

return 'PolylangPolylangTvTmplvarsGetListProcessor';