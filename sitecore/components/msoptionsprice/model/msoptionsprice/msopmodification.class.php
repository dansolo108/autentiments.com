<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

class msopModification extends xPDOSimpleObject
{
    protected $image = null;
    protected $images = null;
    protected $thumb = null;
    protected $thumbs = null;
    protected $options = null;

    /**
     * @param xPDO $xpdo
     */
    public function __construct(xPDO $xpdo)
    {
        parent::__construct($xpdo);
    }

    public function toArray($keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
    {
        $original = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);

        $images = $this->loadImages();
        if (empty($images)) {
            $images = $this->loadImage();
        }
        $thumbs = $this->loadThumbs();
        if (empty($thumbs)) {
            $thumbs = $this->loadThumb();
        }

        $options = $this->loadOptions();

        $additional = array(
            'images'  => $images,
            'thumbs'  => $thumbs,
            'options' => $options,
        );

        return array_merge($original, $additional);
    }

    public function loadImage()
    {
        if ($this->image === null) {
            $this->image = array((string)$this->get('image'));
        }

        return $this->image;
    }

    public function loadImages()
    {
        if ($this->images === null) {
            $this->images = array();
            $q = $this->xpdo->newQuery('msopModificationImage', array(
                'mid' => $this->get('id'),
            ));
            $q->limit(0);
            $q->sortby('rank', 'ASC');
            $q->select('image');
            if ($q->prepare() && $q->stmt->execute()) {
                if (!$this->images = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
                    $this->images = array();
                }
                $this->images = array_merge_recursive(array((string)$this->get('image')), $this->images);
                $this->images = array_values(array_flip(array_flip($this->images)));
            }
        }


        return $this->images;
    }

    public function loadThumb($thumbs = null)
    {
        if ($this->thumb === null) {

            $this->thumb = array();
            $classGallery = trim($this->xpdo->getOption('msoptionsprice_modification_gallery_class', null,
                'msProductFile', true));
            if (is_null($thumbs)) {
                $thumbs = array_map('trim', explode(',', $this->xpdo->getOption('msoptionsprice_modification_thumbs')));
            }

            if (!empty($thumbs) AND !empty($classGallery)) {

                $q = $this->xpdo->newQuery($classGallery, array(
                    'id' => $this->get('image'),
                ));

                switch ($classGallery) {
                    case 'msProductFile':
                        $q->select("{$classGallery}.url as main");

                        foreach ($thumbs as $thumb) {
                            $tmp = '_' . $thumb;
                            $q->leftJoin($classGallery, $tmp,
                                "{$tmp}.parent = {$classGallery}.id AND {$tmp}.path LIKE '%{$thumb}%'");
                            $q->select("{$tmp}.url as {$thumb}");
                        }

                        break;
                    case 'UserFile':
                        $q->select("{$classGallery}.url as main");

                        foreach ($thumbs as $thumb) {
                            $tmp = '_' . $thumb;

                            $thumbnailSize = explode('x', $thumb);
                            $sizeLike = array();
                            if (!empty($thumbnailSize[0])) {
                                $sizeLike[] = 'w\":' . $thumbnailSize[0];
                            }
                            if (!empty($thumbnailSize[1])) {
                                $sizeLike[] = '"\h\":' . $thumbnailSize[1];
                            }
                            $sizeLike = implode(',', $sizeLike);

                            $q->leftJoin($classGallery, $tmp,
                                "{$tmp}.parent = {$classGallery}.id AND {$tmp}.properties LIKE '%{$sizeLike}%'");
                            $q->select("{$tmp}.url as {$thumb}");
                        }

                        break;

                    default:
                        break;
                }

                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        foreach ($row as $k => $v) {
                            if (!isset($this->thumb[$k])) {
                                $this->thumb[$k] = array();
                            }
                            if (!empty($v)) {
                                $this->thumb[$k][] = $v;
                            }
                        }
                    }
                }
            }
        }

        return $this->thumb;
    }

    public function loadThumbs($thumbs = null)
    {
        if ($this->thumbs === null) {

            $this->thumbs = array();
            $classImage = 'msopModificationImage';
            $classGallery = trim($this->xpdo->getOption('msoptionsprice_modification_gallery_class', null,
                'msProductFile', true));
            if (is_null($thumbs)) {
                $thumbs = array_map('trim', explode(',', $this->xpdo->getOption('msoptionsprice_modification_thumbs')));
            }
            if (!empty($thumbs) AND !empty($classGallery)) {

                $q = $this->xpdo->newQuery($classImage, array(
                    'mid' => $this->get('id'),
                ));
                $q->sortby("{$classImage}.rank", "ASC");

                switch ($classGallery) {
                    case 'msProductFile':
                        $q->leftJoin($classGallery, 'main', "{$classImage}.image = main.id");
                        $q->select("main.url as main");

                        foreach ($thumbs as $thumb) {
                            $tmp = '_' . $thumb;
                            $q->leftJoin($classGallery, $tmp,
                                "{$tmp}.parent = {$classImage}.image AND {$tmp}.path LIKE '%{$thumb}%'");
                            $q->select("{$tmp}.url as {$thumb}");
                        }
                        break;
                    case 'UserFile':
                        $q->leftJoin($classGallery, 'main', "{$classImage}.image = main.id");
                        $q->select("main.url as main");

                        foreach ($thumbs as $thumb) {
                            $tmp = '_' . $thumb;

                            $thumbnailSize = explode('x', $thumb);
                            $sizeLike = array();
                            if (!empty($thumbnailSize[0])) {
                                $sizeLike[] = 'w\":' . $thumbnailSize[0];
                            }
                            if (!empty($thumbnailSize[1])) {
                                $sizeLike[] = '"\h\":' . $thumbnailSize[1];
                            }
                            $sizeLike = implode(',', $sizeLike);

                            $q->leftJoin($classGallery, $tmp,
                                "{$tmp}.parent = {$classImage}.image AND {$tmp}.properties LIKE '%{$sizeLike}%'");
                            $q->select("{$tmp}.url as {$thumb}");
                        }
                        break;

                    default:
                        break;
                }

                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        foreach ($row as $k => $v) {
                            if (!isset($this->thumbs[$k])) {
                                $this->thumbs[$k] = array();
                            }
                            if (!empty($v)) {
                                $this->thumbs[$k][] = $v;
                            }
                        }
                    }
                }
            }
        }

        return $this->thumbs;
    }

    public function loadOptions()
    {
        if ($this->options === null) {
            $this->options = $this->xpdo->call('msopModificationOption', 'getOptions',
                array(&$this->xpdo, $this->get('id'), $this->get('rid')));
        }

        return $this->options;
    }

    public function getFirstImage($mid = 0)
    {
        if (empty($mid)) {
            $mid = $this->get('id');
        }
        $q = $this->xpdo->newQuery('msopModificationImage', array(
            'mid' => $mid,
        ));
        $q->limit(1);
        $q->sortby('rank', 'ASC');
        $q->select('image');
        $a = array();
        if ($q->prepare() && $q->stmt->execute()) {
            $a = $q->stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $a;
    }

    public static function getProductModification(xPDO & $xpdo, $rid = 0, $withOptions = true)
    {
        $modifications = array();

        $classModification = 'msopModification';
        $q = $xpdo->newQuery($classModification);
        $q->select($xpdo->getSelectColumns($classModification, $classModification, '', array(), true));
        $q->sortby("rank", "ASC");
        $q->where(array(
            "{$classModification}.rid" => "{$rid}",
        ));

        if ($q->prepare() AND $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($withOptions) {
                    $row['options'] = $xpdo->call('msopModificationOption', 'getOptions',
                        array(&$xpdo, $row['id'], $rid));
                }
                $modifications[] = $row;
            }
        }

        return $modifications;
    }

    public static function saveProductModification(
        xPDO & $xpdo,
        $rid = 0,
        array $modifications = array(),
        $setProductOptions = true
    )
    {
        $classModification = 'msopModification';
        /** @var msoptionsprice $msoptionsprice */
        $msoptionsprice = $xpdo->getService('msoptionsprice');

        foreach ((array)$modifications as $row) {
            $options = isset($row['options']) ? $row['options'] : array();
            if (!empty($options)) {
                unset($row['id'], $row['rank'], $row['options']);

                $row['rid'] = $rid;

                if ($modification = $msoptionsprice->getModificationByOptions(
                    $rid,
                    $options,
                    true,
                    array(0),
                    array(0),
                    null
                )
                ) {
                    /** @var msopModification $modification */
                    $modification = $xpdo->getObject($classModification, array('id' => (int)$modification['id']));
                }
                /** @var msopModification $modification */
                if (!$modification) {
                    $modification = $xpdo->newObject($classModification);
                }

                $modification->fromArray($row, '', true, true);
                if ($modification->save()) {
                    if ($setProductOptions) {
                        $msoptionsprice->setProductOptions($rid, $options);
                    }
                    $xpdo->call('msopModificationOption', 'removeOptions', array(&$xpdo, $modification->get('id'), $rid));
                    $xpdo->call('msopModificationOption', 'saveOptions',
                        array(&$xpdo, $modification->get('id'), $rid, $options));
                }
            } else if ($key = $msoptionsprice->getOption('get_modification_by', null, 'name')) {
                unset($row['id'], $row['rank'], $row['options']);
                $row['rid'] = $rid;


                $q = $xpdo->newQuery($classModification);
                $q->where(array(
                    'rid' => $rid,
                    $key  => isset($row[$key]) ? $row[$key] : null,
                ));

                /** @var msopModification $modification */
                if (!$modification = $xpdo->getObject($classModification, $q)) {
                    $modification = $xpdo->newObject($classModification);
                }

                $modification->fromArray($row, '', true, true);
                if ($modification->save()) {
                    $xpdo->call('msopModificationOption', 'removeOptions', array(&$xpdo, $modification->get('id'), $rid));
                }

            }

            if (0) {
                // TODO remove old
                unset($row['id'], $row['rank'], $row['options']);
                $row['rid'] = $rid;

                if ($modification = $msoptionsprice->getModificationByOptions(
                    $rid,
                    $options,
                    true,
                    array(0),
                    array(0),
                    null
                )
                ) {
                    /** @var msopModification $modification */
                    $modification = $xpdo->getObject($classModification, array('id' => (int)$modification['id']));
                }
                /** @var msopModification $modification */
                if (!$modification) {
                    $modification = $xpdo->newObject($classModification);
                }

                $modification->fromArray($row, '', true, true);
                if ($modification->save()) {
                    if ($setProductOptions) {
                        $msoptionsprice->setProductOptions($rid, $options);
                    }
                    $xpdo->call('msopModificationOption', 'removeOptions', array(&$xpdo, $modification->get('id'), $rid));
                    $xpdo->call('msopModificationOption', 'saveOptions',
                        array(&$xpdo, $modification->get('id'), $rid, $options));
                }
            }
        }

        return self::getProductModification($xpdo, $rid);
    }

    public static function removeProductModification(
        xPDO & $xpdo,
        $rid = 0,
        array $modifications = array(0),
        $removeProductOptions = true
    )
    {
        $classModification = 'msopModification';
        /** @var msoptionsprice $msoptionsprice */
        $msoptionsprice = $xpdo->getService('msoptionsprice');

        foreach ((array)$modifications as $row) {
            $where = array(
                'rid' => (int)$rid,
            );

            if (isset($row['name'])) {
                $where['name'] = $row['name'];
            }
            if (isset($row['type'])) {
                $where['type'] = $row['type'];
            }
            if (isset($row['price'])) {
                $where['price'] = $row['price'];
            }
            if (isset($row['article'])) {
                $where['article'] = $row['article'];
            }
            $q = $xpdo->newQuery($classModification);
            $q->where($where);

            /** @var msopModification $modification */
            if ($objects = $xpdo->getIterator($classModification, $q)) {
                foreach ($objects as $modification) {
                    $options = $xpdo->call('msopModificationOption', 'getOptions',
                        array(&$xpdo, $modification->get('id'), $rid));
                    if ($modification->remove()) {
                        if ($removeProductOptions) {
                            $msoptionsprice->removeProductOptions($rid, $options);
                        }
                        $xpdo->call('msopModificationOption', 'removeOptions',
                            array(&$xpdo, $modification->get('id'), $rid));
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        $isNew = $this->isNew();

        if ($isNew) {
            $q = $this->xpdo->newQuery('msopModification');
            $this->set('rank', $this->xpdo->getCount('msopModification', $q));
        }

        if ($this->xpdo instanceof modX) {
            $this->xpdo->invokeEvent('msopOnModificationBeforeSave', array(
                'mode'         => $isNew ? modSystemEvent::MODE_NEW : modSystemEvent::MODE_UPD,
                'modification' => &$this,
                'cacheFlag'    => $cacheFlag,
            ));
        }

        $saved = parent:: save($cacheFlag);

        if ($saved && $this->xpdo instanceof modX) {
            $this->xpdo->invokeEvent('msopOnModificationSave', array(
                'mode'         => $isNew ? modSystemEvent::MODE_NEW : modSystemEvent::MODE_UPD,
                'modification' => &$this,
                'cacheFlag'    => $cacheFlag,
            ));
        }

        return $saved;
    }

    public function remove(array $ancestors = array())
    {
        if ($this->xpdo instanceof modX) {
            $this->xpdo->invokeEvent('msopOnModificationBeforeRemove', array(
                'modification' => &$this,
                'ancestors'    => $ancestors,
            ));
        }

        $remove = parent::remove($ancestors);

        if ($remove) {
            $this->xpdo->call('msopModificationOption', 'removeOptions',
                array(&$this->xpdo, $this->get('id'), $this->get('rid')));
        }

        if ($this->xpdo instanceof modX) {
            $this->xpdo->invokeEvent('msopOnModificationRemove', array(
                'modification' => &$this,
                'ancestors'    => $ancestors,
            ));
        }

        return $remove;
    }

}