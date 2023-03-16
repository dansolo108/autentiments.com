<?php

class customFiltersHandler extends mse2FiltersHandler
{
    /** @var Polylang $polylang */
    public $polylang;
    /** @var PolylangTools $tools */
    public $tools;
    /** @var MsMC $msmc */
    public $msmc;
    /** @var  string $cultureKey */
    public $cultureKey;
    /** @var int */
    public $baseCurrencyId;
    /** @var array */
    public $userCurrency;

    public function __construct(mSearch2 &$mse2, array $config = array())
    {
        parent::__construct($mse2, $config);
        
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        $this->stikProductRemains = $this->modx->getService('stik', 'stikProductRemains', $this->modx->getOption('core_path').'components/stik/model/', []);
        $this->tools = $this->polylang->getTools();
        $this->cultureKey = $this->modx->getOption('cultureKey', $config, 'en', true);
        if ($this->tools->hasAddition('msmulticurrency')) {
            $this->msmc = $mse2->modx->getService('msmulticurrency', 'MsMC');
            $this->baseCurrencyId = $this->modx->getOption('msmulticurrency.base_currency', null, 0, true);
            $this->userCurrency = $this->msmc->getUserCurrencyData();
            if ($this->baseCurrencyId != $this->userCurrency['cid']) {
                $this->mse2->config['cache_prefix'] = $this->mse2->config['cache_prefix'] . $this->cultureKey . '/msmulticurrency_' . $this->userCurrency['cid'] . '/';
            }
        }
    }

    /**
     * Retrieves values from Template Variables table
     *
     * @param array $tvs Names of tvs
     * @param array $ids Ids of needed resources
     *
     * @return array Array with tvs values as keys and resources ids as values
     */
    public function getTvValues(array $tvs, array $ids)
    {
        if ($this->tools->isCurrentDefaultLanguage()) {
            return parent::getTvValues($tvs, $ids);
        }
        $filters = $results = array();
        $q = $this->modx->newQuery('modResource', array('modResource.id:IN' => $ids));
        $q->leftJoin('modTemplateVarTemplate', 'TemplateVarTemplate',
            'TemplateVarTemplate.tmplvarid IN (SELECT id FROM ' . $this->modx->getTableName('modTemplateVar') . ' WHERE name IN ("' . implode('","', $tvs) . '") )
			AND modResource.template = TemplateVarTemplate.templateid'
        );
        $q->leftJoin('modTemplateVar', 'TemplateVar', 'TemplateVarTemplate.tmplvarid = TemplateVar.id');
        $q->leftJoin('modTemplateVarResource', 'TemplateVarResource', 'TemplateVarResource.tmplvarid = TemplateVar.id AND TemplateVarResource.contentid = modResource.id');
        $q->leftJoin('PolylangTv', 'PolylangTv', array(
            'PolylangTv.tmplvarid = TemplateVar.id',
            'PolylangTv.content_id = modResource.id',
            'PolylangTv.culture_key:=' => $this->cultureKey,
        ));
        $q->select('TemplateVar.name, 
		            IF(
                        (TemplateVar.polylang_enabled = 1),
                        PolylangTv.value,
                        TemplateVarResource.value
                    ) value,
		            IF(
                        (TemplateVar.polylang_enabled = 1),
                        PolylangTv.content_id,
                        TemplateVarResource.contentid
                    ) id,
		            TemplateVar.type, 
		            TemplateVar.default_text
		');
        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (empty($row['id'])) {
                    continue;
                }
                if (is_null($row['value']) || trim($row['value']) == '') {
                    $row['value'] = $row['default_text'];
                }
                if ($row['type'] == 'tag' || $row['type'] == 'autotag') {
                    $row['value'] = str_replace(',', '||', $row['value']);
                }
                $tmp = strpos($row['value'], '||') !== false
                    ? explode('||', $row['value'])
                    : array($row['value']);
                foreach ($tmp as $v) {
                    $v = str_replace('"', '&quot;', trim($v));
                    if ($v == '') {
                        continue;
                    }
                    $name = strtolower($row['name']);
                    if (isset($filters[$name][$v])) {
                        $filters[$name][$v][$row['id']] = $row['id'];
                    } else {
                        $filters[$name][$v] = array($row['id'] => $row['id']);
                    }
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: " . $q->toSQL() . "\nResponse: " . print_r($q->stmt->errorInfo(), 1));
        }

        return $filters;
    }


    /**
     * @param string $sourceClass
     * @param string $targetClass
     * @param array $fields
     * @return string
     */
    public function buildSelectColumns($sourceClass, $targetClass, array $fields = array())
    {
        $result = array();
        $targetFields = $this->modx->getFields($targetClass);
        foreach ($fields as $field) {
            if (array_key_exists($field, $targetFields) && $field != 'id') {
                $result[] = "IF(
                         (`{$targetClass}`.`{$field}` IS NULL || `PolylangContent`.`active` = 0),
                         `{$sourceClass}`.`{$field}`,
                         `{$targetClass}`.`{$field}`
                     ) {$field}";
            } else {
                $result[] = "`{$sourceClass}`.`{$field}`";
            }
        }
        return implode(',', $result);
    }

    /**
     * Retrieves values from miniShop2 Product table
     *
     * @param array $fields Names of ms2 fields
     * @param array $ids Ids of needed resources
     *
     * @return array Array with ms2 fields as keys and resources ids as values
     */
    public function getMsValues(array $fields, array $ids)
    {
        if ($this->tools->isCurrentDefaultLanguage()) {
            return parent::getMsValues($fields, $ids);
        }
        $filters = array();
        $q = $this->modx->newQuery('msProductData');
        $q->leftJoin('PolylangContent', 'PolylangContent', array(
            '`PolylangContent`.`content_id` = `msProductData`.`id`',
            '`PolylangContent`.`culture_key`:=' => $this->cultureKey,
            '`PolylangContent`.`active`:=' => 1,
        ));
        $q->leftJoin('PolylangProduct', 'PolylangProduct', array(
            '`PolylangProduct`.`content_id` = `PolylangContent`.`content_id`',
            '`PolylangProduct`.`culture_key`= `PolylangContent`.`culture_key`',
        ));

        $q->where(array('msProductData.id:IN' => $ids));
        $q->select($this->modx->getSelectColumns('msProductData', 'msProductData', '', array('id')));
        $q->select($this->buildSelectColumns('msProductData', 'PolylangProduct', $fields));

        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                foreach ($row as $k => $v) {
                    $v = str_replace('"', '&quot;', trim($v));
                    if ($k == 'id') {
                        continue;
                    } elseif (isset($filters[$k][$v])) {
                        $filters[$k][$v][$row['id']] = $row['id'];
                    } else {
                        $filters[$k][$v] = array($row['id'] => $row['id']);
                    }
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: " . $q->toSQL() . "\nResponse: " . print_r($q->stmt->errorInfo(), 1));
        }

        return $filters;
    }


    /**
     * Retrieves values from miniShop2 Product table
     *
     * @param array $keys Keys of ms2 products options
     * @param array $ids Ids of needed resources
     *
     * @return array Array with ms2 fields as keys and resources ids as values
     */
    public function getMsOptionValues(array $keys, array $ids)
    {
        if ($this->tools->isCurrentDefaultLanguage()) {
            return parent::getMsOptionValues($keys, $ids);
        }

        $filters = array();
        $q = $this->modx->newQuery('PolylangProductOption');
        $q->leftJoin('PolylangContent', 'PolylangContent', array(
            '`PolylangContent`.`content_id` = `PolylangProductOption`.`content_id`',
            '`PolylangContent`.`culture_key`=`PolylangProductOption`.`culture_key`',
        ));
        $q->where(array(
            '`PolylangProductOption`.`key`:IN' => $keys,
            '`PolylangProductOption`.`content_id`:IN' => $ids,
            '`PolylangProductOption`.`culture_key`' => $this->cultureKey,
            '`PolylangContent`.`active`' => 1,

        ));
        $q->select('`PolylangProductOption`.`content_id` as product_id, `PolylangProductOption`.`key`, `PolylangProductOption`.`value`');

        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $value = str_replace('"', '&quot;', trim($row['value']));
                //if ($value == '') {continue;}
                $key = $row['key'];
                // Get ready for the special options in "key==value" format
                if (strpos($value, '==')) {
                    list($key, $value) = explode('==', $value);
                    $key = preg_replace('/\s+/', '_', $key);
                }
                // --
                if (isset($filters[$key][$value])) {
                    $filters[$key][$value][$row['product_id']] = $row['product_id'];
                } else {
                    $filters[$key][$value] = array($row['product_id'] => $row['product_id']);
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: " . $q->toSQL() . "\nResponse: " . print_r($q->stmt->errorInfo(), 1));
        }
        return $filters;
    }

    /**
     * Retrieves values from Resource table
     *
     * @param array $fields Names of resource fields
     * @param array $ids Ids of needed resources
     *
     * @return array Array with resource fields as keys and resources ids as values
     */
    public function getResourceValues(array $fields, array $ids)
    {
        if ($this->tools->isCurrentDefaultLanguage()) {
            return parent::getResourceValues($fields, $ids);
        }
        $filters = array();
        $no_id = false;
        if (!in_array('id', $fields)) {
            $fields[] = 'id';
            $no_id = true;
        }
        $q = $this->modx->newQuery('modResource');
        $q->leftJoin('PolylangContent', 'PolylangContent', array(
            'PolylangContent.content_id = modResource.id',
            'PolylangContent.culture_key:=' => $this->cultureKey,
            'PolylangContent.active' => 1,
        ));

        $q->select($this->buildSelectColumns('modResource', 'PolylangContent', $fields));
        $q->where(array('modResource.id:IN' => $ids));

        if (in_array('parent', $fields) && $this->mse2->checkMS2()) {
            $q->leftJoin('msCategoryMember', 'Member', 'Member.product_id = modResource.id');
            $q->orCondition(array('Member.product_id:IN' => $ids));
            $q->select('category_id');
        }
        $tstart = microtime(true);

        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                foreach ($row as $k => $v) {
                    $v = str_replace('"', '&quot;', trim($v));
                    if ($k == 'category_id') {
                        if (!$v || $v == $row['parent']) {
                            continue;
                        } else {
                            $k = 'parent';
                        }
                    }
                    if ($k == 'id' && $no_id) {
                        continue;
                    } elseif (isset($filters[$k][$v])) {
                        $filters[$k][$v][$row['id']] = $row['id'];
                    } else {
                        $filters[$k][$v] = array($row['id'] => $row['id']);
                    }
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: " . $q->toSQL() . "\nResponse: " . print_r($q->stmt->errorInfo(), 1));
        }

        return $filters;
    }

    public function getResourceValues_(array $fields, array $ids)
    {
        if ($this->tools->isCurrentDefaultLanguage()) {
            return parent::getResourceValues($fields, $ids);
        }
        $filters = array();
        $no_id = false;
        if (!in_array('id', $fields)) {
            $fields[] = 'id';
            $no_id = true;
        }
        $q = $this->modx->newQuery('modResource');
        $q->innerJoin('PolylangContent', 'PolylangContent', array(
            'PolylangContent.content_id = modResource.id',
            'PolylangContent.culture_key:=' => $this->cultureKey,
            'PolylangContent.active' => 1,
        ));
        $this->modx->loadClass('PolylangContent');
        $polylangFields = array_intersect(array_diff(array_keys($this->modx->map['PolylangContent']['fields']), array('content_id', 'culture_key', 'active')), $fields);
        $fields = array_diff($fields, $polylangFields);

        if (count($fields) > 0) {
            $q->select(implode(',', array_map(function ($value) {
                return "`modResource`.`{$value}`";
            }, $fields)));
        }
        if (count($polylangFields) > 0) {
            $q->select(implode(',', array_map(function ($value) {
                return "`PolylangContent`.`{$value}`";
            }, $polylangFields)));
        }

        $q->where(array('modResource.id:IN' => $ids));
        if (in_array('parent', $fields) && $this->mse2->checkMS2()) {
            $q->leftJoin('msCategoryMember', 'Member', 'Member.product_id = modResource.id');
            $q->orCondition(array('Member.product_id:IN' => $ids));
            $q->select('category_id');
        }
        $tstart = microtime(true);

        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                foreach ($row as $k => $v) {
                    $v = str_replace('"', '&quot;', trim($v));
                    if ($k == 'category_id') {
                        if (!$v || $v == $row['parent']) {
                            continue;
                        } else {
                            $k = 'parent';
                        }
                    }
                    if ($k == 'id' && $no_id) {
                        continue;
                    } elseif (isset($filters[$k][$v])) {
                        $filters[$k][$v][$row['id']] = $row['id'];
                    } else {
                        $filters[$k][$v] = array($row['id'] => $row['id']);
                    }
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: " . $q->toSQL() . "\nResponse: " . print_r($q->stmt->errorInfo(), 1));
        }

        return $filters;
    }

    /**
     * Prepares values for filter
     * Returns array with human-readable parents of resources
     *
     * @param array $values
     * @param string $name Filter name
     * @param integer $depth
     * @param string $separator
     *
     * @return array Prepared values
     */
    public function buildParentsFilter(array $values, $name = '', $depth = 1, $separator = ' / ')
    {
        if ($this->tools->isCurrentDefaultLanguage()) {
            return parent::buildParentsFilter($values, $name, $depth, $separator);
        }
        $results = $parents = $menuindex = array();
        $q = $this->modx->newQuery('modResource', array('modResource.id:IN' => array_keys($values), 'published' => 1));
        $q->leftJoin('PolylangContent', 'PolylangContent', array(
            'PolylangContent.content_id = modResource.id',
            'PolylangContent.culture_key:=' => $this->cultureKey,
            'PolylangContent.active' => 1,
        ));
        $q->select($this->buildSelectColumns('modResource', 'PolylangContent', array('id', 'pagetitle', 'menutitle', 'context_key', 'menuindex')));

        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $parents[$row['id']] = $row;
                $menuindex[$row['id']] = $row['menuindex'];
            }
        }

        foreach ($values as $value => $ids) {
            if ($value === 0 || !isset($parents[$value])) {
                continue;
            }
            $parent = $parents[$value];
            $titles = array();
            if ($depth > 0) {
                $pids = $this->modx->getParentIds($value, $depth, array('context' => $parent['context_key']));
                if (!empty($pids)) {
                    $q = $this->modx->newQuery('modResource', array('modResource.id:IN' => array_reverse($pids), 'published' => 1));
                    $q->leftJoin('PolylangContent', 'PolylangContent', array(
                        'PolylangContent.content_id = modResource.id',
                        'PolylangContent.culture_key:=' => $this->cultureKey,
                        'PolylangContent.active' => 1,
                    ));
                    $q->select($this->buildSelectColumns('modResource', 'PolylangContent', array('id', 'pagetitle', 'menutitle')));
                    $tstart = microtime(true);
                    if ($q->prepare() && $q->stmt->execute()) {
                        $this->modx->queryTime += microtime(true) - $tstart;
                        $this->modx->executedQueries++;
                        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            $titles[$row['id']] = !empty($row['menutitle'])
                                ? $row['menutitle']
                                : $row['pagetitle'];
                        }
                    }
                }
            }
            $titles[$value] = !empty($parent['menutitle'])
                ? $parent['menutitle']
                : $parent['pagetitle'];

            $title = implode($separator, $titles);
            $results[$menuindex[$value]][$title] = array(
                'title' => $title,
                'value' => $value,
                'type' => 'parents',
                'resources' => $ids,
            );
        }

        return count($results) < 2 && empty($this->config['showEmptyFilters'])
            ? array()
            : $this->sortFilters($results, 'parents', array('name' => $name));

    }

    /**
     * Prepares values for filter
     * Returns array with human-readable grandparent of resource
     *
     * @param array $values
     * @param string $name
     * @param boolean $filter
     *
     * @return array
     */
    public function buildGrandParentsFilter(array $values, $name = '', $filter = false)
    {
        if ($this->tools->isCurrentDefaultLanguage()) {
            return parent::buildGrandParentsFilter($values, $name, $filter);
        }
        if (count($values) < 2 && empty($this->config['showEmptyFilters'])) {
            return array();
        }

        $grandparents = array();
        $q = $this->modx->newQuery('modResource', array('modResource.id:IN' => array_keys($values), 'published' => 1));
        $q->innerJoin('PolylangContent', 'PolylangContent', array(
            'PolylangContent.content_id = modResource.id',
            'PolylangContent.culture_key:=' => $this->cultureKey,
            'PolylangContent.active' => 1,
        ));
        $q->select('modResource.id,parent');
        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $grandparents[$row['id']] = $row['parent'];
            }
        }

        $tmp = array();
        foreach ($values as $k => $v) {
            if (isset($grandparents[$k]) && $grandparents[$k] != 0) {
                $parent = $grandparents[$k];
                if (!isset($tmp[$parent])) {
                    $tmp[$parent] = $v;
                } else {
                    $tmp[$parent] = array_merge($tmp[$parent], $v);
                }
            } else {
                $tmp[$k] = $v;
            }
        }

        return $filter ? $tmp : $this->buildParentsFilter($tmp, $name, 0);
    }


    /**
     * Returns array with rounded minimum and maximum value
     *
     * @param array $values
     * @param string $name
     *
     * @return array
     */
    public function buildPriceFilter(array $values, $name = '')
    {
        if ($filters = $this->buildDecimalFilter($values, $name)) {
            $filters[0]['value'] = floor($filters[0]['value']);
            $filters[1]['value'] = ceil($filters[1]['value']);
            if ($this->msmc) {
                $filters[0]['value'] = $this->msmc->getPrice($filters[0]['value'], 0, 0, 0, false);
                $filters[1]['value'] = $this->msmc->getPrice($filters[1]['value'], 0, 0, 0, false);
            }
        }

        return $filters;
    }

    /**
     * Filters numbers. Values must be between min and max number
     *
     * @param array $requested Filtered ids of resources
     * @param array $values Filter data with min and max number
     * @param array $ids Ids of currently active resources
     *
     * @return array
     */
    public function filterPrice(array $requested, array $values, array $ids)
    {
        $matched = array();

        $min = floor(min($requested));
        $max = ceil(max($requested));

        if ($this->msmc) {
            $min = $this->msmc->getPrice($min, 0, $this->baseCurrencyId, 0, false, false);
            $max = $this->msmc->getPrice($max, 0, $this->baseCurrencyId, 0, false, false);
        }

        $tmp = array_flip($ids);
        foreach ($values as $number => $resources) {
            if ($number >= $min && $number <= $max) {
                foreach ($resources as $id) {
                    if (isset($tmp[$id])) {
                        $matched[] = $id;
                    }
                }
            }
        }

        return $matched;
    }

    public function getMsopValues(array $keys, array $ids)
    {
        $filters = [];

        $q = $this->modx->newQuery('msProductData');
        if ($this->showZeroCount) {
            $q->leftJoin('msopModification', 'msopModification', 'msProductData.id = msopModification.rid AND msopModification.type = 1 AND msopModification.active = 1');
        } else {
            $q->leftJoin('msopModification', 'msopModification', 'msProductData.id = msopModification.rid AND msopModification.type = 1 AND msopModification.active = 1 AND msopModification.count > 1');
        }

        $q->where(['id:IN' => $ids]);
        $q->groupby('msopModification.id');

        // add select
        $select = ['msProductData.id'];
        foreach ($keys as $field) {
            $select[] = 'msopModification.' . $field;
        }
        $q->select(implode(',', $select));

        $tstart = microtime(true);

        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                foreach ($row as $k => $v) {
                    $v = str_replace('"', '&quot;', trim($v));
                    if ($k === 'id') {
                        continue;
                    } else if (isset($filters[$k][$v])) {
                        $filters[$k][$v][$row['id']] = $row['id'];
                    } else {
                        $filters[$k][$v] = [$row['id'] => $row['id']];
                    }
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: " . $q->toSQL() .
                "\nResponse: " . print_r($q->stmt->errorInfo(), 1)
            );
        }

        return $filters;
    }

    public function getMsopOptionValues(array $keys, array $ids)
    {
        $filters = [];

        $q = $this->modx->newQuery('msopModificationOption');
        $q->setClassAlias('ModificationOption');
        $q->where(['ModificationOption.rid:IN' => $ids, 'ModificationOption.key:IN' => $keys]);
        if ($this->showZeroCount) {
            $q->innerJoin('msopModification', 'Modification', 'Modification.id = ModificationOption.mid AND Modification.type = 1 AND Modification.active = 1');
        } else {
            $q->innerJoin('msopModification', 'Modification', 'Modification.id = ModificationOption.mid AND Modification.type = 1 AND Modification.active = 1 AND Modification.count > 1');
        }
        $q->select('Modification.rid as product_id, ModificationOption.key, ModificationOption.value');

        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $value = str_replace('"', '&quot;', trim($row['value']));
                //if ($value == '') {continue;}
                $key = strtolower($row['key']);
                // Get ready for the special options in "key==value" format
                if (strpos($value, '==')) {
                    list($key, $value) = explode('==', $value);
                    $key = preg_replace('/\s+/', '_', $key);
                }
                // --
                if (isset($filters[$key][$value])) {
                    $filters[$key][$value][$row['product_id']] = $row['product_id'];
                } else {
                    $filters[$key][$value] = [$row['product_id'] => $row['product_id']];
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: " . $q->toSQL() . "\nResponse: " . print_r($q->stmt->errorInfo(), 1));
        }

        return $filters;
    }




	public function buildSizeFilter(array $values, $name = '') {
		if (count($values) < 2 && empty($this->config['showEmptyFilters'])) {
			return array();
		}

		$results = array();
		foreach ($values as $value => $ids) {
			if ($value !== '') {
				$results[$value] = array(
					'title' => $value,
					'value' => $value,
					'type' => 'default',
					'resources' => $ids
				);
			}
		}

		$sorted = $this->sortFilters($results, 'default', array('name' => $name));
// 		$this->modx->log(1, print_r($sorted,1));
        $sortSizes = $this->stikProductRemains->getSortSizes();
        uksort($sorted, function($a, $b) use ($sortSizes) {
            return array_search($a, $sortSizes) - array_search($b, $sortSizes);
        });
		return $sorted;
	}
	
	public function filterSize(array $requested, array $values, array $ids) {
		$matched = array();

		$tmp = array_flip($ids);
		foreach ($requested as $value) {
			$value = str_replace('"', '&quot;', $value);
			if (isset($values[$value])) {
				$resources = $values[$value];
				foreach ($resources as $id) {
					if (isset($tmp[$id])) {
						$matched[] = $id;
					}
				}
			}
		}

		return $matched;
	}
}