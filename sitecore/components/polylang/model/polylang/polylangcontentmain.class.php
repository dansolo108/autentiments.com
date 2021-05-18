<?php

abstract class  PolylangContentMain extends xPDOSimpleObject
{
    /**
     * @param string $classKey
     * @return string
     */
    public static function getFieldPrefix($classKey)
    {
        return '';
    }

    public static function getClassName()
    {
        return str_replace('_mysql', '', static::class);
    }

    /**
     * @param xPDO $xpdo
     * @param mSearch2 $mSearch2
     * @param modResource $resource
     */
    public static function putSearchIndex(xPDO &$xpdo, mSearch2 &$mSearch2, modResource &$resource)
    {
        $classKey = self::getClassName();
        $fields = $xpdo->getFields($classKey);
        $fields = array_keys(array_intersect_key($fields, $mSearch2->fields));
        if (empty($fields)) return;
        $fields[] = 'culture_key';
        $q = $xpdo->newQuery($classKey);
        $q->select($xpdo->getSelectColumns($classKey, $classKey, '', $fields));
        if ($classKey != 'PolylangContent') {
            $q->leftJoin('PolylangContent', 'Content', array(
                "`Content`.`content_id` = `{$classKey}`.`content_id`",
                "`Content`.`culture_key` = `{$classKey}`.`culture_key`",
            ));
        }
        $q->where(array(
            "`{$classKey}`.`content_id`" => $resource->get('id'),
            '`PolylangContent`.`active`' => 1,
        ));
        $q->sortby('culture_key');
        if ($q->prepare() && $q->stmt->execute()) {
            while ($values = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $cultureKey = $values['culture_key'];
                unset($values['culture_key']);
                foreach ($values as $key => $value) {
                    if (empty($value)) continue;
                    $fieldKey = "{$cultureKey}_{$key}";
                    $index = isset($mSearch2->fields[$key]) ? $mSearch2->fields[$key] : 1;
                    //  $xpdo->log(modX::LOG_LEVEL_ERROR, $fieldKey . ':' . $index);
                    $mSearch2->fields[$fieldKey] = $index;
                    $resource->set($fieldKey, $value);
                }
            }
        }
    }

    /**
     * Recursive implode
     * @param static $glue
     * @param array $array
     * @return string
     */
    public static function implode($glue, array $array)
    {
        $result = array();
        foreach ($array as $v) {
            $result[] = is_array($v) ? self::implode($glue, $v) : $v;
        }
        return implode($glue, $result);
    }

    /**
     * @param $str
     * @return bool
     */
    public static function isJSONStr($str)
    {
        return $str[0] == '[' || $str[0] == '{';
    }

}