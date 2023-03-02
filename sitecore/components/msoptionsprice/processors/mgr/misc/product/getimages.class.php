<?php

/*
 * TODO combobox ()
 *
 */

class msProductFileGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = '';
    public $languageTopics = array('default', 'minishop2:product');
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'ASC';
    public $permission = '';
    /** @var miniShop2 $miniShop2 */
    protected $miniShop2;


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $this->miniShop2 = $this->modx->getService('miniShop2');

        $classGallery = trim($this->modx->getOption('msoptionsprice_modification_gallery_class', null,
            'msProductFile', true));
        $this->classKey = $classGallery;

        return parent::initialize();
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        //modification_thumbs

        switch ($this->classKey) {
            case 'msProductFile':
                $thumbnailSize = $this->modx->getOption('msoptionsprice_modification_thumbs', null, 'small', true);
                $thumbnailSize = $this->modx->getOption('ms2_product_thumbnail_size', null, $thumbnailSize, true);

                $thumbnailSize = array_map('trim', explode(',', $thumbnailSize));
                $thumbnailSize = reset($thumbnailSize);

                $c->groupby("{$this->classKey}.id");
                $c->leftJoin('modMediaSource', 'Source');
                $c->leftJoin($this->classKey, 'Thumb',
                    $this->classKey . '.id = Thumb.parent AND Thumb.path LIKE "%' . $thumbnailSize . '/%"');

                $c->select('Source.name as source_name');
                $c->select('Thumb.url as thumbnail');

                $c->where(array('product_id' => $this->getProperty('rid')));

                $parent = $this->getProperty('parent');
                if ($parent !== false) {
                    $c->where(array('parent' => $parent));
                }

                break;
            case 'UserFile':
                $thumbnailSize = $this->modx->getOption('msoptionsprice_modification_thumbs', null, '120x90', true);
                $thumbnailSize = $this->modx->getOption('userfiles_image_thumb_default', null, $thumbnailSize, true);

                $thumbnailSize = array_map('trim', explode(',', $thumbnailSize));
                $thumbnailSize = reset($thumbnailSize);

                $thumbnailSize = explode('x', $thumbnailSize);

                $sizeLike = array();
                if (!empty($thumbnailSize[0])) {
                    $sizeLike[] = 'w\":' . $thumbnailSize[0];
                }
                if (!empty($thumbnailSize[1])) {
                    $sizeLike[] = '"\h\":' . $thumbnailSize[1];
                }
                $sizeLike = implode(',', $sizeLike);

                $list = 'default';
                if ($product = $this->modx->getObject('modResource', array('id' => (int)$this->getProperty('rid')))) {
                    $list = $this->modx->getOption('userfiles_list_template_' . $product->get('template'), null,
                        $this->modx->getOption('userfiles_list_default', null, 'default', true), true);
                }

                $c->groupby("{$this->classKey}.id");
                $c->leftJoin('modMediaSource', 'Source');
                $c->leftJoin($this->classKey, 'Thumb',
                    "{$this->classKey}.id = Thumb.parent AND Thumb.class = '{$this->classKey}' AND Thumb.properties LIKE '%{$sizeLike}%'");


                $c->select('Thumb.url as thumbnail');

                $c->where(array(
                    'parent'    => $this->getProperty('rid'),
                    'class:!='  => $this->classKey,
                    'list'      => $list,
                    'mime:LIKE' => 'image%',
                ));

                break;

            default:
                break;
        }

        $query = trim($this->getProperty('query'));
        if (!empty($query)) {
            $c->where(array(
                'file:LIKE'           => "%{$query}%",
                'OR:name:LIKE'        => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
            ));
        }

        return $c;
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

        if ($c->prepare() AND $c->stmt->execute()) {
            $data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    public function prepareArray(array $row)
    {
        return $row;
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

return 'msProductFileGetListProcessor';