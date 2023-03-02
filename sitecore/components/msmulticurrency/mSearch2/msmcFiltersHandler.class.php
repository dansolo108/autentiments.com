<?php

class msmcFiltersHandler extends mse2FiltersHandler
{
    /** @var MsMC $msmc */
    public $msmc;
    /** @var int */
    public $baseCurrencyId;
    /** @var array */
    public $userCurrency;

    public function __construct(mSearch2 &$mse2, array $config = array())
    {

        parent::__construct($mse2, $config);

        if ($this->isExistService('msmulticurrency')) {
            $this->msmc = $mse2->modx->getService('msmulticurrency', 'MsMC');
            $this->baseCurrencyId = $this->modx->getOption('msmulticurrency.base_currency', null, 0, true);
            $this->userCurrency = $this->msmc->getUserCurrencyData();
            if ($this->baseCurrencyId != $this->userCurrency['cid']) {
                $this->mse2->config['cache_prefix'] = $this->mse2->config['cache_prefix'] . 'msmulticurrency_' . $this->userCurrency['cid'] . '/';
            }
        }
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

    /**
     * @param string $service
     * @return bool
     */
    public function isExistService($service = '')
    {
        $service = strtolower($service);

        return file_exists(MODX_CORE_PATH . 'components/' . $service . '/model/' . $service . '/');
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

    public function getMsocValues(array $tmp, array $ids)
    {
        $filters = $fields = $keys = array();
        foreach ($tmp as $v) {
            $v = explode('~', $v);
            $fields[array_shift($v)] = implode('~', $v);
            $keys = array_merge($keys, $v);
        }
        $keys = array_keys(array_flip($keys));
        $keys = array_merge(array('rid', 'key', 'value'), $keys);

        $classColor = 'msocColor';
        $classProductOption = 'msProductOption';
        $q = $this->modx->newQuery($classColor);
        $q->innerJoin($classProductOption, $classProductOption,
            "{$classProductOption}.key = {$classColor}.key AND {$classProductOption}.value = {$classColor}.value AND {$classProductOption}.product_id = {$classColor}.rid AND {$classColor}.active = 1");
        $q->where(array(
            "{$classColor}.rid:IN"         => $ids,
            "{$classProductOption}.key:IN" => array_keys($fields),
        ));

        $q->select($this->modx->getSelectColumns($classColor, $classColor, '', $keys, false));
        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $key = strtolower($row['key']);

                //$value = str_replace(array_keys($row), array_values($row), $fields[$key]);

                $value = implode('~', array_intersect_key($row, array_flip(explode('~', $fields[$key]))));

                if (!is_array($filters[$key])) {
                    $filters[$key] = array();
                }
                if (isset($filters[$key][$value])) {
                    $filters[$key][$value][$row['rid']] = $row['rid'];
                } else {
                    $filters[$key][$value] = array($row['rid'] => $row['rid']);
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                "[mSearch2] Error on get filter params.\nQuery: " . $q->toSql() . "\nResponse: " . print_r($q->stmt->errorInfo(),
                    1));
        }

        return $filters;
    }
}