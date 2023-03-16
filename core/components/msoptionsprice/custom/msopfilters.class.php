<?php

class msopFilters extends mse2FiltersHandler
{
    /* @var mSearch2 $mse2 */
    public $mse2;
    /* @var modX $modx */
    public $modx;
    public $config = [];

    public $showZeroCount = true;

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
            $select[] = 'msopModification.'.$field;
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
                    } else {
                        if (isset($filters[$k][$v])) {
                            $filters[$k][$v][$row['id']] = $row['id'];
                        } else {
                            $filters[$k][$v] = [$row['id'] => $row['id']];
                        }
                    }
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: ".$q->toSQL().
                "\nResponse: ".print_r($q->stmt->errorInfo(), 1)
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
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: ".$q->toSQL()."\nResponse: ".print_r($q->stmt->errorInfo(), 1));
        }

        return $filters;
    }


    public function getMsopModificationValues(array $keys, array $ids)
    {
        $filters = [];

        $q = $this->modx->newQuery('msopModification');
        if ($this->showZeroCount) {
            $q->where(['type' => 1, 'active' => 1, 'id:IN' => $ids]);
        } else {
            $q->where(['type' => 1, 'active' => 1, 'count:>' => 0, 'id:IN' => $ids]);
        }
        $q->groupby('msopModification.id');

        // add select
        $select = ['msopModification.id'];
        foreach ($keys as $field) {
            $select[] = 'msopModification.'.$field;
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
                    } else {
                        if (isset($filters[$k][$v])) {
                            $filters[$k][$v][$row['id']] = $row['id'];
                        } else {
                            $filters[$k][$v] = [$row['id'] => $row['id']];
                        }
                    }
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: ".$q->toSQL().
                "\nResponse: ".print_r($q->stmt->errorInfo(), 1)
            );
        }

        return $filters;
    }


    public function getMsopModificationOptionValues(array $keys, array $ids)
    {
        $filters = [];

        $q = $this->modx->newQuery('msopModificationOption');
        $q->setClassAlias('ModificationOption');
        $q->where(['ModificationOption.mid:IN' => $ids, 'ModificationOption.key:IN' => $keys]);
        if ($this->showZeroCount) {
            $q->innerJoin('msopModification', 'Modification', 'Modification.id = ModificationOption.mid AND Modification.type = 1 AND Modification.active = 1');
        } else {
            $q->innerJoin('msopModification', 'Modification', 'Modification.id = ModificationOption.mid AND Modification.type = 1 AND Modification.active = 1 AND Modification.count > 1');
        }
        $q->select('Modification.id as product_id, ModificationOption.key, ModificationOption.value');

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
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[mSearch2] Error on get filter params.\nQuery: ".$q->toSQL()."\nResponse: ".print_r($q->stmt->errorInfo(), 1));
        }

        //$this->modx->log(1, print_r($filters ,1));

        return $filters;
    }


}