<?php

class msmcFiltersHandler extends mse2FiltersHandler
{
    /** @var MsMC $msmc */
    public $msmc;

    public function __construct(mSearch2 &$mse2, array $config = array())
    {

        parent::__construct($mse2, $config);

        if ($this->isExistService('msmulticurrency')) {
            $this->mse2->config['cacheTime'] = -1;
            $this->msmc = $mse2->modx->getService('msmulticurrency', 'MsMC');
        }
    }

    /**
     * Retrieves values from miniShop2 Product table
     *
     * @param array $fields Names of ms2 fields
     * @param array $ids Ids of needed resources
     *
     * @return array Array with ms2 fields as keys and resources ids as values
     */
    public function getMsValues(array $fields, array $ids) {
        $filters = array();
        $get_price = in_array('price', $fields);
        $get_weight = in_array('weight', $fields);
        $c = $this->modx->newQuery('modPluginEvent');
        $c->innerJoin('modPlugin', 'Plugin');
        $c->where(array('event:IN' => array('msOnGetProductPrice', 'msOnGetProductWeight'), 'Plugin.disabled' => false));

        if (($get_price || $get_weight) && empty($this->config['noPreciseMSFilters']) && $this->modx->getCount('modPluginEvent', $c)) {
            foreach ($ids as $id) {
                /** @var msProductData $product */
                if ($product = $this->modx->getObject('msProductData', $id)) {
                    foreach ($fields as $field) {
                        switch ($field) {
                            case 'id':
                                continue 2;
                            case 'price':
                                $value = number_format($product->getPrice(), 2, '.', '');
                                if ($this->msmc) {
                                    $value = $this->msmc->getPrice($value, $product->get('id'), 0, 'price', false);
                                }
                                break;
                            case 'weight':
                                $value = number_format($product->getWeight(), 3, '.', '');
                                break;
                            default:
                                $value = isset($product->_fields[$field])
                                    ? str_replace('"', '&quot;', trim($product->_fields[$field]))
                                    : '';
                        }
                        if (isset($filters[$field][$value])) {
                            $filters[$field][$value][$id] = $id;
                        } else {
                            $filters[$field][$value] = array($id => $id);
                        }
                    }
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Could not load msProductData with id = " . $id);
                }
            }
        } else {
            $q = $this->modx->newQuery('msProductData');
            $q->where(array('id:IN' => $ids));
            $q->select('id,' . implode(',', $fields));
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
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: " . $q->toSQL() .
                    "\nResponse: " . print_r($q->stmt->errorInfo(), 1)
                );
            }
        }

        return $filters;
    }


    /**
     * Returns array with rounded minimum and maximum value
     *
     * @param array $values
     * @param string $name
     *
     * @return array
     */
   /* public function buildPriceFilter(array $values, $name = '')
    {
        if ($filters = $this->buildDecimalFilter($values, $name)) {
            $filters[0]['value'] = floor($filters[0]['value']);
            $filters[1]['value'] = ceil($filters[1]['value']);
            if ($this->msmc) {
                $filters[0]['value'] = $this->msmc->getPrice($filters[0]['value'], 0, 0, '', false);
                $filters[1]['value'] = $this->msmc->getPrice($filters[1]['value'], 0, 0, '', false);
            }
        }

        return $filters;
    }*/

    /**
     * Filters numbers. Values must be between min and max number
     *
     * @param array $requested Filtered ids of resources
     * @param array $values Filter data with min and max number
     * @param array $ids Ids of currently active resources
     *
     * @return array
     */
   /* public function filterPrice(array $requested, array $values, array $ids)
    {
        $matched = array();

        $min = floor(min($requested));
        $max = ceil(max($requested));

        if ($this->msmc) {
            $baseCurrencyId = $this->modx->getOption('msmulticurrency.base_currency', null, 0, true);
            $min = $this->msmc->getPrice($min, 0, $baseCurrencyId, '', false, false);
            $max = $this->msmc->getPrice($max, 0, $baseCurrencyId, '', false, false);
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
    }*/

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

        $baseCurrencyId = $this->modx->getOption('msmulticurrency.base_currency', null, 0, true);

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
}