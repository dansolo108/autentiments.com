<?php
require_once(dirname(__FILE__) . '/polylangcontentmain.class.php');

class PolylangContent extends PolylangContentMain
{
    /** @var array $tvs */
    protected $tvs = null;
    /** @var array $TVKeys */
    protected $TVKeys = null;
    /** @var array $_originalFieldMeta */
    public $_originalFieldMeta = array();

    /**
     * @param xPDO $xpdo
     */
    function __construct(xPDO &$xpdo)
    {
        parent::__construct($xpdo);
        $this->_originalFieldMeta = $this->_fieldMeta;

    }

    /**
     * @param array|string $options
     *
     * @return string
     */
    public static function getCacheKey($options)
    {
        return 'polylang' . DIRECTORY_SEPARATOR . sha1(is_array($options) ? serialize($options) : $options);
    }


    /**
     * @param string $classKey
     * @return string
     */
    public static function getFieldPrefix($classKey)
    {
        switch ($classKey) {
            case 'tvpolylang':
                $prefix = 'tv';
                break;
            default:
                $prefix = '';

        }
        return $prefix;
    }


    /**
     * @param xPDO $xpdo
     * @param mSearch2 $mSearch2
     * @param modResource $resource
     */
    public static function putSearchIndex(xPDO &$xpdo, mSearch2 &$mSearch2, modResource &$resource)
    {
        parent::putSearchIndex($xpdo, $mSearch2, $resource);
        $classKey = 'PolylangTv';
        $content = $xpdo->newObject('PolylangContent');
        $content->set('content_id', $resource->get('id'));
        if (!$tvs = $content->getTVKeys()) return;
        $tvs = array_flip($tvs);
        foreach ($mSearch2->fields as $field => $index) {
            if (strpos($field, 'tv_') !== false) {
                $tv = substr($field, 3);
                if (!isset($tvs[$tv])) continue;
                $q = $xpdo->newQuery($classKey);
                $q->leftJoin('PolylangContent', 'Content', array(
                    "`Content`.`content_id` = `{$classKey}`.`content_id`",
                    "`Content`.`culture_key` = `{$classKey}`.`culture_key`",
                ));
                $q->select($xpdo->getSelectColumns($classKey, $classKey, '', array('culture_key', 'value')));
                $q->where(array(
                    '`Content`.`active`' => 1,
                    "`{$classKey}`.`value`:!=" => '',
                    "`{$classKey}`.`content_id`" => $resource->get('id'),
                    "`{$classKey}`.`tmplvarid`" => $tvs[$tv],
                ));
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fieldKey = "{$item['culture_key']}_{$tv}";
                        $mSearch2->fields[$fieldKey] = $index;
                        if (self::isJSONStr($item['value'])) {
                            $tmp = $xpdo->fromJSON($item['value']);
                            if (is_array($tmp)) {
                                $item['value'] = self::implode(' ', $tmp);
                            }
                        }
                        $resource->set($fieldKey, $item['value']);
                    }
                }
            }
        }
    }

    /**
     * @return xPDOQuery
     */
    public static function prepareTVQuery(PolylangContent &$content)
    {
        $resource = $content->getOne('Resource');
        $c = $content->xpdo->newQuery('modTemplateVar');
        $c->innerJoin('modTemplateVarTemplate', 'tvtpl', array(
            'tvtpl.tmplvarid = modTemplateVar.id',
            'tvtpl.templateid' => $resource->get('template'),
        ));
        $c->groupby('modTemplateVar.id');
        $c->where(array(
            'modTemplateVar.polylang_enabled' => 1,
        ));
        return $c;
    }

    /**
     * @return modTemplateVar[]
     */
    public static function getTemplateVarCollection(PolylangContent &$content)
    {
        $c = $content->xpdo->call('PolylangContent', 'prepareTVQuery', array(&$content));
        $c->query['distinct'] = 'DISTINCT';
        $c->select($content->xpdo->getSelectColumns('modTemplateVar', 'modTemplateVar'));
        $c->select($content->xpdo->getSelectColumns('modTemplateVarTemplate', 'tvtpl', '', array('rank')));
        if ($content->isNew()) {
            $c->select(array(
                'modTemplateVar.default_text AS value',
                '0 AS resourceId'
            ));
        } else {
            $c->select(array(
                'IF(ISNULL(tvc.value),modTemplateVar.default_text,tvc.value) AS value',
                $content->get('content_id') . ' AS resourceId'
            ));
        }
        if (!$content->isNew()) {
            $c->leftJoin('PolylangTv', 'tvc', array(
                'tvc.tmplvarid = modTemplateVar.id',
                'tvc.content_id' => $content->get('content_id'),
                'tvc.culture_key' => $content->get('culture_key'),
            ));
        }
        $c->sortby('tvtpl.rank,modTemplateVar.rank');
        $c->leftJoin('modCategory', 'Category', 'Category.id=modTemplateVar.category');
        $c->select(array(
            'IF(ISNULL(Category.id),0,Category.id) AS category_id, Category.category AS category_name',
        ));
        return $content->xpdo->getCollection('modTemplateVar', $c);
    }

    /**
     * @return modTemplateVar[]
     */
    public function getTemplateVars()
    {
        return $this->xpdo->call('PolylangContent', 'getTemplateVarCollection', array(&$this));
    }

    /**
     * @return array
     */
    public static function _loadTVs(PolylangContent &$content)
    {
        $c = $content->xpdo->call('PolylangContent', 'prepareTVQuery', array(&$content));
        $c->query['distinct'] = 'DISTINCT';
        $c->select($content->xpdo->getSelectColumns('modTemplateVar', 'modTemplateVar'));
        $c->select($content->xpdo->getSelectColumns('modTemplateVarTemplate', 'tvtpl', '', array('rank')));
        if ($content->isNew()) {
            $c->select(array(
                'modTemplateVar.default_text AS value',
            ));
        } else {
            $c->select(array(
                'IF(ISNULL(tvc.value),modTemplateVar.default_text,tvc.value) AS value',
            ));
        }
        if (!$content->isNew()) {
            $c->leftJoin('PolylangTv', 'tvc', array(
                'tvc.tmplvarid = modTemplateVar.id',
                'tvc.content_id' => $content->get('content_id'),
                'tvc.culture_key' => $content->get('culture_key'),
            ));
        }
        $c->sortby('tvtpl.rank,modTemplateVar.rank');

        $data = array();
        if ($c->prepare() && $c->stmt->execute()) {
            while ($tv = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[$tv['name']] = $tv['value'];
            }
        }
        return $data;
    }

    public function loadTVs()
    {
        if ($this->tvs === null) {
            $this->tvs = $this->xpdo->call('PolylangContent', '_loadTVs', array(&$this));
        }
        return $this->tvs;
    }

    /**
     * @param bool $force
     * @return array
     */
    public function getTVKeys($force = false)
    {
        if ($this->TVKeys === null || $force) {
            /** @var xPDOQuery $c */
            $c = $this->xpdo->call('PolylangContent', 'prepareTVQuery', array(&$this));
            $c->select('modTemplateVar.id,modTemplateVar.name');
            $this->TVKeys = array();
            if ($c->prepare() && $c->stmt->execute()) {
                while ($tv = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->TVKeys[$tv['id']] = $tv['name'];
                }
            }
        }
        return $this->TVKeys;
    }

    /**
     * @param array|string $k
     * @param null $format
     * @param null $formatTemplate
     *
     * @return array|mixed|null|xPDOObject
     */
    public function get($k, $format = null, $formatTemplate = null)
    {
        if (is_array($k)) {
            $array = array();
            foreach ($k as $v) {
                $array[$v] = isset($this->_fieldMeta[$v])
                    ? parent::get($v, $format, $formatTemplate)
                    : $this->get($v, $format, $formatTemplate);
            }

            return $array;
        } elseif (isset($this->_fieldMeta[$k])) {
            return parent::get($k, $format, $formatTemplate);
        } elseif (in_array($k, $this->getTVKeys())) {
            if (isset($this->$k)) {
                return $this->$k;
            }
            $this->loadTVs();
            $value = isset($this->tvs[$k]) ? $this->tvs[$k] : null;
            return $value;
        } else {
            return parent::get($k, $format, $formatTemplate);
        }
    }

    /**
     * @param null $cacheFlag
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        $save = parent::save($cacheFlag);
        $this->saveTVs();

        return $save;
    }

    protected function saveTVs()
    {
        $tvids = [];
        $tvs = $this->getTemplateVars();
        if ($tvs) {
            foreach ($tvs as $tv) {
                $tvids[] = $tv->get('id');
                if (!$tv->checkResourceGroupAccess()) {
                    continue;
                }

                $tvKey = 'tv' . $tv->get('id');
                $value = $this->get('tv' . $tv->get('name'));

                if ($tv->get('type') != 'checkbox') {
                    $value = $value !== null ? $value : $tv->get('default_text');
                } else {
                    $value = $value ? $value : '';
                }

                switch ($tv->get('type')) {
                    case 'url':
                        $prefix = $this->getProperty($tvKey . '_prefix', '');
                        if ($prefix != '--') {
                            $value = str_replace(array('ftp://', 'http://'), '', $value);
                            $value = $prefix . $value;
                        }
                        /* $value = str_replace(array('ftp://', 'http://'), '', $value);
                         $value = $prefix . $value;*/
                        break;
                    case 'date':
                        $value = empty($value) ? '' : strftime('%Y-%m-%d %H:%M:%S', strtotime($value));
                        break;
                    case 'tag':
                    case 'autotag':
                        $tags = explode(',', $value);
                        $newTags = array();
                        foreach ($tags as $tag) {
                            $newTags[] = trim($tag);
                        }
                        $value = implode(',', $newTags);
                        break;
                    default:
                        if (is_array($value)) {
                            $featureInsert = array();
                            foreach ($value as $featureValue => $featureItem) {
                                if (isset($featureItem) && $featureItem === '') {
                                    continue;
                                }
                                $featureInsert[count($featureInsert)] = $featureItem;
                            }
                            $value = implode('||', $featureInsert);
                        }
                        break;
                }

                $default = $tv->processBindings($tv->get('default_text'), $this->get('content_id'));
                if (strcmp($value, $default) != 0) {
                    $tvc = $this->xpdo->getObject('PolylangTv', array(
                        'culture_key' => $this->get('culture_key'),
                        'tmplvarid' => $tv->get('id'),
                        'content_id' => $this->get('content_id'),
                    ));
                    if ($tvc == null) {
                        /** @var modTemplateVarResource $tvc add a new record */
                        $tvc = $this->xpdo->newObject('PolylangTv');
                        $tvc->set('tmplvarid', $tv->get('id'));
                        $tvc->set('culture_key', $this->get('culture_key'));
                        $tvc->set('content_id', $this->get('content_id'));
                    }
                    $tvc->set('value', $value);
                    $tvc->save();
                } else {
                    $tvc = $this->xpdo->getObject('PolylangTv', array(
                        'tmplvarid' => $tv->get('id'),
                        'culture_key' => $this->get('culture_key'),
                        'content_id' => $this->get('content_id'),
                    ));
                    if (!empty($tvc)) {
                        $tvc->remove();
                    }
                }
            }
            if (!empty($tvids)) {
                $this->xpdo->removeCollection('PolylangTv', array(
                    'tmplvarid:NOT IN' => $tvids,
                    'culture_key' => $this->get('culture_key'),
                    'content_id' => $this->get('content_id'),
                ));
            }
        }
    }


    public function toArray($keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
    {
        $original = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
        $additional = $this->loadTVs();
        $intersect = array_keys(array_intersect_key($original, $additional));
        foreach ($intersect as $key) {
            unset($additional[$key]);
        }
        return array_merge($original, $additional);
    }

    /**
     * Returns the processed output of a template variable.
     *
     * @access public
     * @param xPDO $xpdo
     * @param modTemplateVar||int||string $tv template variable element
     * @param string $value The TV value.
     * @param integer $resourceId The id of the resource; 0 defaults to the
     * current resource.
     * @return mixed The processed output of the template variable.
     */
    public static function renderTVOutput(xPDO &$xpdo, $tv, $value = '', $resourceId = 0)
    {
        if (!($tv instanceof modTemplateVar)) {
            $byName = !is_numeric($tv);

            $tv = $xpdo->getObject('modTemplateVar', $byName ? array('name' => $tv) : $tv);

            if ($tv == null) {
                return $value;
            }
        }

        /* process any TV commands in value */
        $value = $tv->processBindings($value, $resourceId);

        $params = array();
        /**
         * Backwards support for display_params
         * @deprecated To be removed in 2.2
         */
        if ($paramstring = $tv->get('display_params')) {
            $tv->xpdo->deprecated('2.2.0', 'Use output_properties instead.', 'modTemplateVar renderOutput display_params');
            $cp = explode("&", $paramstring);
            foreach ($cp as $p => $v) {
                $ar = explode("=", $v);
                if (is_array($ar) && count($ar) == 2) {
                    $params[$ar[0]] = $tv->decodeParamValue($ar[1]);
                }
            }
        }
        /* get output_properties for rendering properties */
        $outputProperties = $tv->get('output_properties');
        if (!empty($outputProperties) && is_array($outputProperties)) {
            $params = array_merge($params, $outputProperties);
        }

        /* run prepareOutput to allow for custom overriding */
        $value = $tv->prepareOutput($value, $resourceId);

        /* find the render */
        $outputRenderPaths = $tv->getRenderDirectories('OnTVOutputRenderList', 'output');
        return $tv->getRender($params, $value, $outputRenderPaths, 'output', $resourceId, $tv->get('display'));
    }

    /**
     * @param array $ancestors
     * @return bool
     */
    public function remove(array $ancestors = array())
    {

        $tvs = $this->xpdo->getIterator('PolylangTv', array(
            'culture_key' => $this->get('culture_key'),
            'content_id' => $this->get('content_id'),
        ));
        /** @var PolylangTv $tv */
        foreach ($tvs as $tv) {
            $tv->remove();
        }
        return parent::remove($ancestors);
    }

}