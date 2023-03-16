<?php

require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/gallery/getlist.class.php';

class msopModificationFileGetListProcessor extends msProductFileGetListProcessor
{
    public $languageTopics = array('default', 'minishop2:product', 'msoptionsprice');

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        parent::prepareQueryBeforeCount($c);

//        $c->query['groupby'] = array();
//        $c->groupby($this->classKey . '.id');

        $source = $this->getProperty('source');
        if ($source !== false) {
            $c->where(array('source' => $source));
        }

        $classMI = 'msopModificationImage';
        $mid = $this->getProperty('mid', 0);

        $c->leftJoin($classMI, $classMI, "{$this->classKey}.id = {$classMI}.image AND {$classMI}.mid = '{$mid}'");
        $c->select("{$classMI}.image as modification_image,{$classMI}.rank as modification_rank");
        $c->sortby("COALESCE({$classMI}.rank,99999999)", 'ASC');

        return $c;
    }

    public function prepareArray(array $row)
    {

        $row = parent::prepareArray($row);

        $icon = 'icon';
        $row['over_actions'] = array();

        if (empty($row['modification_image'])) {
            $row['over_actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-plus green msoptionsprice-over-icon",
                'title'  => '&nbsp',
                'action' => 'addFile',
                'button' => true,
                'menu'   => true,
            );
        } else {
            $row['over_actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-minus red msoptionsprice-over-icon",
                'title'  => '&nbsp',
                'action' => 'removeFile',
                'button' => true,
                'menu'   => true,
            );
        }

        return $row;
    }
}

return 'msopModificationFileGetListProcessor';