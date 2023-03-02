<?php


require_once dirname(dirname(__FILE__)) . '/misc/base/getlist.class.php';


/**
 * Get a list of msopModification
 */
class msopModificationGetListProcessor extends msopBaseGetListProcessor/* modObjectGetListProcessor*/
{
    public $objectType = 'msopModification';
    public $classKey = 'msopModification';
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'ASC';
    public $languageTopics = array('default', 'msoptionsprice');
    public $permission = '';

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $classGallery = trim($this->modx->getOption('msoptionsprice_modification_gallery_class', null,
            'msProductFile', true));

        switch ($classGallery) {
            case 'msProductFile':
            case 'UserFile':
            default:
                $c->leftJoin($classGallery, $classGallery, "{$classGallery}.id = {$this->classKey}.image");
                $c->select("{$classGallery}.name as image_name, {$classGallery}.url as thumbnail");
                break;
        }

        $rid = $this->getProperty('rid');
        if (!in_array($rid, array(null))) {
            $c->where("{$this->classKey}.rid='{$rid}'");
        }

        // sort by options key
        $key = trim($this->getProperty('key'));
        if ($key) {
            $option = "option_{$key}";
            $c->leftJoin('msopModificationOption', $option,
                "`{$option}`.mid = msopModification.id AND `{$option}`.key = '{$key}'");
            $c->where(array("{$option}.value:!=" => null));
        }

        // sort by options
        $pf = $this->modx->getOption('msoptionsprice_field_prefix', null, 'option_', true);
        $sort = $this->getProperty('sort');
        if (strpos($sort, $pf) === 0) {
            $option = substr($sort, strlen($pf));
            $c->leftJoin('msopModificationOption', $option,
                "`{$option}`.mid = msopModification.id AND `{$option}`.key = '{$option}'");
            $this->setProperty('sort', "{$option}.value");
        }

        return $c;
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionsprice_all'),
                ),
            ), $array);
        }
        if ($this->getProperty('novalue')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionsprice_no'),
                ),
            ), $array);
        }

        return parent::outputArray($array, $count);
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareArray(array $array)
    {
        if ($this->getProperty('combo')) {

        } else {

            $icon = 'icon';
            $array['actions'] = array();

            // Edit
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-edit green",
                'title'  => $this->modx->lexicon('msoptionsprice_action_update'),
                'action' => 'update',
                'button' => true,
                'menu'   => true,
            );

            // sep
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => '',
                'title'  => '',
                'action' => 'sep',
                'button' => false,
                'menu'   => true,
            );

            if (!$array['active']) {
                $array['actions'][] = array(
                    'cls'    => '',
                    'icon'   => "$icon $icon-toggle-off red",
                    'title'  => $this->modx->lexicon('msoptionsprice_action_turnon'),
                    'action' => 'active',
                    'button' => true,
                    'menu'   => true,
                );
            } else {
                $array['actions'][] = array(
                    'cls'    => '',
                    'icon'   => "$icon $icon-toggle-on green",
                    'title'  => $this->modx->lexicon('msoptionsprice_action_turnoff'),
                    'action' => 'inactive',
                    'button' => true,
                    'menu'   => true,
                );
            }

            // Remove
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-trash-o red",
                'title'  => $this->modx->lexicon('msoptionsprice_action_remove'),
                'action' => 'remove',
                'button' => true,
                'menu'   => true,
            );

            // sep
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => '',
                'title'  => '',
                'action' => 'sep',
                'button' => false,
                'menu'   => true,
            );
            // duplicate
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-files-o",
                'title'  => $this->modx->lexicon('msoptionsprice_action_duplicate'),
                'action' => 'duplicate',
                'button' => false,
                'menu'   => true,
            );


            /* get options */
            $array['options'] = $this->modx->call('msopModificationOption', 'getOptions',
                array(&$this->modx, $array['id'], $array['rid'], null, true));

            /* $options = $this->modx->call('msopModificationOption', 'getOptions',
                 array(&$this->modx, $array['id'], $array['rid'], null, true, 'option_'));
            $array = array_merge($array, $options);
            */

            $array = $this->prepareObjectData($array);

        }

        return $array;
    }

    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        /*  $s = $c->prepare();
          $sql = $c->toSQL();
          $s->execute();
          $this->modx->log(1, print_r($sql, 1));
          $this->modx->log(1, print_r($s->errorInfo(), 1));*/

        if ($c->prepare() AND $c->stmt->execute()) {
            $data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }


    /**
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $this->prepareArray($array);
            $this->currentIndex++;
        }
        $list = $this->afterIteration($list);

        return $list;
    }

}

return 'msopModificationGetListProcessor';