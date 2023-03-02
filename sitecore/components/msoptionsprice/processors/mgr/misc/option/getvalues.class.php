<?php

/**
 * Get a list of msProductOption
 */
class msProductOptionGetValuesProcessor extends modObjectProcessor
{
    public $classKey = 'msProductOption';
    public $permission = '';

    public function process()
    {
        $query = trim($this->getProperty('query'));
        $start = (int)$this->getProperty('start', 0);
        $limit = (int)$this->getProperty('limit', 10);
        $rid = (int)$this->getProperty('rid');

        $c = $this->modx->newQuery('msProductOption');

        if (!empty($query)) {
            $c->where(array('value:LIKE' => "%{$query}%"));
        }

        if ($this->getProperty('combo')) {
            $c->limit(0);
        }

        $values = $_values = array();
        $key = preg_replace('#^options-(.*?)#', '$1', $this->getProperty('key'));
        if ($key) {

            /** @var msOption $option */
            if ($option = $this->modx->getObject('msOption',
                    array('key' => $key)) AND $tmp = $option->get('properties')
            ) {
                $_values = $this->modx->getOption('values', $tmp);
                if (is_array($_values)) {
                    foreach ($_values as $k => $v) {
                        $values[] = array('value' => $v);
                    }
                }
            }

            $c->sortby('value', 'ASC');
            $c->select('value');
            $c->groupby('value');
            $c->andCondition(array(
                'product_id:!=' => 0,
                'key'           => $key,
            ));
           /* if ($rid) {
                $c->andCondition(array(
                    'product_id' => $rid
                ));
            }*/

            if (!empty($_values) AND is_array($_values)) {
                $c->andCondition(array(
                    'value:NOT IN' => $_values
                ));
            }

            $found = false;
            if ($c->prepare() AND $c->stmt->execute()) {
                if ($tmp = $c->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                    $values = array_merge($values, $tmp);
                }
                foreach ($values as $k => $v) {
                    if (empty($v['value'])) {
                        unset($values[$k]);
                        continue;
                    }
                    if ($v['value'] == $query) {
                        $found = true;
                    }
                }
            }

            if (!$found AND !empty($query)) {
                $values = array_merge_recursive(array(array('value' => $query)), $values);
            }
        } else {
            $c->sortby('value', 'ASC');
            $c->select('value,key');
            $c->groupby('value');
            $c->andCondition(array(
                'product_id' => $rid
            ));

            if ($c->prepare() AND $c->stmt->execute()) {
                while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $values[$row['key']][] = $row['value'];
                }
            }
        }

        $count = count($values);
        $values = array_slice($values, $start, $limit);

        return $this->outputArray($values, $count);

    }
}

return 'msProductOptionGetValuesProcessor';