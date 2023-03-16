<?php

require_once MODX_CORE_PATH . 'components/userfiles/processors/mgr/file/getlist.class.php';

class msopModificationFileGetListProcessor extends modUserFileGetListProcessor
{
    public $languageTopics = array('default', 'userfiles', 'msoptionsprice');

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        parent::prepareQueryBeforeCount($c);
        $c->andCondition(array('mime:LIKE' => 'image%'));

        $c->query['groupby'] = array();
        $c->query['sortby'] = array();
        $c->groupby($this->classKey . '.id, Thumbnail.url');

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

        unset($row['actions'][2]);

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