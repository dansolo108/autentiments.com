<?php
require_once(dirname(__FILE__) . '/polylangcontentmain.class.php');

class PolylangProduct extends PolylangContentMain
{
    /** @var array $optionKeys */
    protected $optionKeys = null;
    /** @var array $options */
    protected $options = null;

    /**
     * @param xPDO $xpdo
     * @param int $contentId
     * @param string $cultureKey
     * @return PolylangProduct|null
     */
    public static function getInstance(xPDO &$xpdo, $contentId, $cultureKey)
    {
        /** @var PolylangProduct $object */
        $object = $xpdo->getObject('PolylangProduct', array('content_id' => $contentId, 'culture_key' => $cultureKey));
        if (!$object) {
            $object = $xpdo->newObject('PolylangProduct');
            $object->set('content_id', $contentId);
            $object->set('culture_key', $cultureKey);
        }
        return $object;
    }


    public static function putSearchIndex(xPDO &$xpdo, mSearch2 &$mSearch2, modResource &$resource)
    {
        parent::putSearchIndex($xpdo, $mSearch2, $resource);
        $classKey = 'PolylangProductOption';
        $product = $xpdo->newObject('PolylangProduct');
        $product->set('content_id', $resource->get('id'));
        if (!$options = $product->getOptionKeys()) return;
        $options = array_flip($options);
        foreach ($mSearch2->fields as $field => $index) {
            if (strpos($field, 'option_') !== false) {
                $option = substr($field, 7);
                if (!isset($options[$option])) continue;
                $q = $xpdo->newQuery($classKey);
                $q->leftJoin('PolylangContent', 'Content', array(
                    "`Content`.`content_id` = `{$classKey}`.`content_id`",
                    "`Content`.`culture_key` = `{$classKey}`.`culture_key`",
                ));
                $q->select($xpdo->getSelectColumns($classKey, $classKey, '', array('culture_key', 'key')));
                $q->select(array("GROUP_CONCAT(`{$classKey}`.`value` SEPARATOR ' ') as value"));
                $q->where(array(
                    '`Content`.`active`' => 1,
                    "`{$classKey}`.`value`:!=" => '',
                    "`{$classKey}`.`content_id`" => $resource->get('id'),
                    "`{$classKey}`.`key`" => $option,
                ));
                $q->sortby("`{$classKey}`.`culture_key`,`{$classKey}`.`value`");
                $q->groupby("`{$classKey}`.`culture_key`,`{$classKey}`.`key`");
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fieldKey = "{$item['culture_key']}_option_{$option}";
                        $mSearch2->fields[$fieldKey] = $index;
                        $resource->set($fieldKey, $item['value']);
                    }
                }
            }
        }
    }

    public function prepareObject()
    {
        foreach ($this->getArraysValues() as $name => $array) {
            $array = $this->prepareOptionValues($array);
            parent::set($name, $array);
        }
    }

    /**
     * Loads product options
     */
    public
    function loadOptions()
    {
        if ($this->options === null) {
            $this->options = $this->xpdo->call('PolylangProduct', '_loadOptions', array(
                &$this->xpdo,
                $this->get('content_id'),
                $this->get('culture_key'),
            ));
        }

        return $this->options;
    }

    /**
     * @param xPDO $xpdo
     * @param int $contentId
     * @param string $cultureKey
     *
     * @return array
     */
    public
    static function _loadOptions(xPDO &$xpdo, $contentId, $cultureKey)
    {
        $c = $xpdo->newQuery('PolylangProductOption');
        $c->rightJoin('msOption', 'msOption', 'PolylangProductOption.key=msOption.key');
        $c->leftJoin('modCategory', 'Category', 'Category.id=msOption.category');
        $c->where(array(
            'msOption.polylang_enabled' => 1,
            'PolylangProductOption.culture_key' => $cultureKey,
            'PolylangProductOption.content_id' => $contentId,
        ));
        $c->select($xpdo->getSelectColumns('msOption', 'msOption'));
        $c->select($xpdo->getSelectColumns('PolylangProductOption', 'PolylangProductOption', '', array('key'), true));
        $c->select('Category.category AS category_name');
        $data = array();
        if ($c->prepare() && $c->stmt->execute()) {
            while ($option = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                // If the option is repeated, its value will be an array
                if (isset($data[$option['key']])) {
                    $data[$option['key']][] = $option['value'];
                } else {
                    $data[$option['key']] = array($option['value']);
                }
                foreach ($option as $key => $value) {
                    $data[$option['key'] . '.' . $key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * @param null $values
     *
     * @return array|null
     */
    public
    function prepareOptionValues($values = null)
    {
        if ($values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            // fix duplicate, empty option values
            $values = array_map('trim', $values);
            $values = array_keys(array_flip($values));
            $values = array_diff($values, array(''));
            //sort($values);

            if (empty($values)) {
                $values = null;
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getOptionFields()
    {
        $fields = array();
        /** @var xPDOQuery $c */
        $c = $this->prepareOptionListCriteria();

        $c->select(array(
            $this->xpdo->getSelectColumns('msOption', 'msOption'),
            $this->xpdo->getSelectColumns('msCategoryOption', 'msCategoryOption', '',
                array('id', 'option_id', 'category_id'), true
            ),
            'Category.category AS category_name',
        ));
        $c->sortby('msOption.category', 'ASC');
        $c->groupby('id');

        $options = $this->xpdo->getIterator('msOption', $c);

        /** @var msOption $option */
        foreach ($options as $option) {
            $field = $option->toArray();
            $value = $this->xpdo->call('PolylangProductOption', '_getValue', array(
                &$this->xpdo,
                &$option,
                $this->get('content_id'),
                $this->get('culture_key'),
            ));
            //$field['value'] = !is_null($value) ? $value : $field['value'];
            $field['value'] = !is_null($value) ? $value : '';
            $field['ext_field'] = $option->getManagerField($field);
            $fields[] = $field;
        }

        return $fields;
    }


    /**
     * @return xPDOQuery
     */
    public
    function prepareOptionListCriteria()
    {
        $categories = array();
        $q = $this->xpdo->newQuery('msCategoryMember', array('product_id' => $this->get('content_id')));
        $q->select('category_id');
        if ($q->prepare() && $q->stmt->execute()) {
            $categories = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        if ($product = $this->getOne('Resource')) {
            $categories[] = $product->get('parent');
        } elseif (!empty($_GET['parent'])) {
            $categories[] = (int)$_GET['parent'];
        }
        $categories = array_unique($categories);

        $c = $this->xpdo->newQuery('msOption');
        $c->leftJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.option_id = msOption.id');
        $c->leftJoin('modCategory', 'Category', 'Category.id = msOption.category');
        $c->sortby('msCategoryOption.rank');
        $c->where(array(
            'msCategoryOption.active' => 1,
            'msOption.polylang_enabled' => 1,
        ));
        if (!empty($categories[0])) {
            $c->where(array('msCategoryOption.category_id:IN' => $categories));
        }

        return $c;
    }

    /**
     * @param bool $force
     *
     * @return array
     */
    public
    function getOptionKeys($force = false)
    {
        if ($this->optionKeys === null || $force) {
            /** @var xPDOQuery $c */
            $c = $this->prepareOptionListCriteria();

            $c->groupby('msOption.id');
            $c->select('msOption.key');

            $this->optionKeys = $c->prepare() && $c->stmt->execute()
                ? $c->stmt->fetchAll(PDO::FETCH_COLUMN)
                : array();
        }

        return $this->optionKeys;
    }

    /**
     * @return array
     */
    public
    function getArraysValues()
    {
        $arrays = array();
        foreach ($this->_fieldMeta as $name => $field) {
            if (strtolower($field['phptype']) === 'json') {
                $arrays[$name] = parent::get($name);
            }
        }

        return $arrays;
    }

    protected function saveProductOptions()
    {
        $classKey = 'PolylangProductOption';
        $table = $this->xpdo->getTableName($classKey);
        $contentId = parent::get('content_id');
        $cultureKey = parent::get('culture_key');
        $add = $this->xpdo->prepare("INSERT INTO {$table} (`content_id`,`culture_key`, `key`, `value`) VALUES ({$contentId}, '{$cultureKey}',?, ?)");

        $arrays = $this->getArraysValues();
        // Copy JSON fields to options
        $c = $this->xpdo->newQuery($classKey);
        $c->command('DELETE');
        $c->where(array(
            'content_id' => $contentId,
            'culture_key' => $cultureKey,
            'key:IN' => array_keys($arrays),
        ));


        if ($c->prepare() && $c->stmt->execute()) {
            foreach ($arrays as $key => $array) {
                $array = $this->prepareOptionValues($array);
                if (is_array($array)) {
                    foreach ($array as $value) {
                        if (!$add->execute(array($key, $value))) {
                            $err = $this->xpdo->pdo->errorInfo();
                            if ($err[0] != '00000' && $err[0] != '01000') {
                                $this->xpdo->log(modX::LOG_LEVEL_ERROR, print_r($err, 1));
                            }
                        }
                    }
                }
            }
        }

        $optionKeys = $this->getOptionKeys();
        if ($optionKeys) {
            $options = array();
            foreach ($optionKeys as $key) {
                if (in_array($key, $this->_fieldMeta)) continue;
                $options[$key] = $this->get($key);
            }
            if ($options) {
                $c = $this->xpdo->newQuery($classKey);
                $c->command('DELETE');
                $c->where(array(
                    'content_id' => $contentId,
                    'culture_key' => $cultureKey,
                ));
                $c->andCondition(array(
                    'key:NOT IN' => array_merge($optionKeys, array_keys($arrays)),
                ), '', 1);

                if ($given_keys = array_keys($options)) {
                    $c->orCondition(array(
                        'key:IN' => $given_keys,
                    ), '', 1);
                }
                if ($c->prepare()) {
                    $c->stmt->execute();
                }
                foreach ($options as $key => $array) {
                    $array = $this->prepareOptionValues($array);
                    if (is_array($array)) {
                        foreach ($array as $value) {
                            $add->execute(array($key, $value));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param null $cacheFlag
     * @return bool
     */
    public
    function save($cacheFlag = null)
    {
        $this->prepareObject();
        $save = parent::save($cacheFlag);
        $this->saveProductOptions();
        return $save;
    }

    /**
     * @param array $ancestors
     * @return bool
     */
    public
    function remove(array $ancestors = array())
    {
        $this->xpdo->removeCollection('PolylangProductOption', array('content_id' => $this->get('content_id')));
        return parent::remove($ancestors);
    }

    /**
     * @param array|string $k
     * @param null $format
     * @param null $formatTemplate
     *
     * @return array|null
     */
    public
    function get($k, $format = null, $formatTemplate = null)
    {
        if (is_array($k)) {
            $array = array();
            foreach ($k as $v) {
                $array[$v] = isset($this->_fieldMeta[$v]) ? parent::get($v, $format, $formatTemplate) : $this->get($v, $format, $formatTemplate);
            }

            return $array;
        } else {
            $value = null;
            switch ($k) {
                case 'options':
                    $value = $this->getOptions();
                    break;
                default:
                    $value = parent::get($k, $format, $formatTemplate);
            }

            return $value;
        }
    }

    /**
     * @return array
     */
    public
    function getOptions()
    {
        $value = array();
        $c = $this->xpdo->newQuery('PolylangProductOption', array(
            'content_id' => $this->get('content_id'),
            'culture_key' => $this->get('culture_key'),
        ));
        $c->select('key,value');
        $c->sortby('value');
        if ($c->prepare() && $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (isset($value[$row['key']])) {
                    $value[$row['key']][] = $row['value'];
                } else {
                    $value[$row['key']] = array($row['value']);
                }
            }
        }
        return $value;
    }

    /**
     * @param string $keyPrefix
     * @param bool $rawValues
     * @param bool $excludeLazy
     * @param bool $includeRelated
     *
     * @return array
     */
    public
    function toArray($keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
    {
        $original = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
        $additional = $this->loadOptions();
        $intersect = array_keys(array_intersect_key($original, $additional));
        foreach ($intersect as $key) {
            unset($additional[$key]);
        }

        return array_merge($original, $additional);
    }

}