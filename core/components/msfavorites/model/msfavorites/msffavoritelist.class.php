<?php


class msfFavoriteList extends xPDOSimpleObject
{
    
    public static $settableFields = ['list', 'type'];


    public function __call($n, array $p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }


    public static function load(xPDO & $xpdo, $className, $criteria, $cacheFlag = true)
    {
        if (is_array($criteria)) {
            $criteria = self::prepareFields($criteria);
        }

        /** @var $instance msfFavoriteList */
        $instance = parent::load($xpdo, 'msfFavoriteList', $criteria, $cacheFlag);

        if (!is_object($instance) OR !($instance instanceof $className)) {
            if (!empty($criteria['list']) AND !empty($criteria['type'])) {

                $instance = $xpdo->newObject('msfFavoriteList');
                $instance->set('list', $criteria['list']);
                $instance->set('type', $criteria['type']);
                if ($instance->save()) {

                }
            }
        }

        return $instance;
    }


    public static function prepareFields($values, $key = '')
    {
        if (!empty($key)) {
            $values = [$key => $values];
        }
        $values = is_array($values) ? $values : [];
        $values = array_intersect_key($values, array_flip(self::$settableFields));
        foreach ($values as $k => $v) {
            switch ($k) {
                case 'list':
                case 'type':
                    $values[$k] = trim(mb_strtolower((string)$v, 'utf-8'));
                    break;
                default:
                    break;
            }
        }

        if (!empty($key)) {
            return isset($values[$key]) ? $values[$key] : null;
        }

        return $values;
    }


    public function beforeSave()
    {
        foreach (self::$settableFields as $k) {
            parent::set($k, self::prepareFields(parent::get($k), $k));
        }
    }


    public function afterSave()
    {

    }


    public function save($cacheFlag = null)
    {
        $this->beforeSave();

        $saved = parent:: save($cacheFlag);

        if ($saved) {
            $this->afterSave();
        }

        return $saved;
    }


    public function validate(array $options = [])
    {
        $validated = parent::validate($options);
        if ($validated !== true) {
            return $validated;
        }

        $errors = [];

        $limit = $this->xpdo->getOption('msfavorites_list_limit', null, 100, true);
        if ($limit < $this->xpdo->getCount('msfFavoriteList')) {
            $errors['id'] = 'msfavorites_err_list_limit';
        }

        if (!empty($errors)) {
            $validated = false;
            /** @var xPDOValidator $validator */
            if ($validator = parent::getValidator()) {
                foreach ($errors as $field => $error) {
                    $validator->addMessage($field, '', $error);
                }
            }
        }

        return $validated;
    }

}