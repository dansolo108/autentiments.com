<?php


class msfFavoriteObject extends xPDOObject
{

    public static $settableFields = ['lid', 'uid', 'kid'];


    public function __call($n, array $p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }


    public static function load(xPDO & $xpdo, $className, $criteria, $cacheFlag = true)
    {
        if (is_array($criteria)) {
            $criteria = self::prepareFields($criteria);
        }

        /** @var $instance msfFavoriteObject */
        $instance = parent::load($xpdo, 'msfFavoriteObject', $criteria, $cacheFlag);

        if (!is_object($instance) OR !($instance instanceof $className)) {
            if (!empty($criteria['lid']) AND !empty($criteria['kid']) AND !empty($criteria['uid'])) {
                if (!$instance = parent::load($xpdo, 'msfFavoriteObject', $criteria, false)) {
                    $instance = $xpdo->newObject('msfFavoriteObject');
                    $instance->set('lid', $criteria['lid']);
                    $instance->set('kid', $criteria['kid']);
                    $instance->set('uid', $criteria['uid']);
                    if ($instance->save()) {

                    }
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
                case 'lid':
                case 'kid':
                    $values[$k] = (int)$v;
                    break;
                case 'uid':
                    $values[$k] = trim((string)$v);
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
        $this->updateObjectExtra();
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


    public function remove(array $ancestors = [])
    {

        $remove = parent::remove($ancestors);

        if ($remove) {
            $this->updateObjectExtra();
        }

        return $remove;
    }


    public function validate(array $options = [])
    {
        $validated = parent::validate($options);
        if ($validated !== true) {
            return $validated;
        }

        $errors = [];

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


    public function updateObjectExtra()
    {
        $extra = $this->get('extra');
        $extra = is_array($extra) ? $extra : [];
        if (!empty($extra)) {
            $extra = serialize($extra);
        } else {
            $extra = '';
        }

        $lid = parent::get('lid');
        $kid = parent::get('kid');
        $uid = parent::get('uid');
        $table = $this->xpdo->getTableName('msfFavoriteObjectExtra');

        if (empty($extra)) {
            if ($stmt = $this->xpdo->prepare("DELETE FROM {$table} WHERE lid = :lid AND kid = :kid AND uid = :uid")) {
                $stmt->bindValue(':lid', $lid);
                $stmt->bindValue(':kid', $kid);
                $stmt->bindValue(':uid', $uid);
                if (!$stmt->execute()) {
                    $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "[msFavorites] Could not remove object extra. \n" . print_r($stmt->errorInfo(), true));
                }
            }
        } else {
            if ($stmt = $this->xpdo->prepare("INSERT INTO {$table} (lid,kid,uid,extra) VALUES (:lid,:kid,:uid,:extra) ON DUPLICATE KEY UPDATE lid = :lid, kid = :kid, uid = :uid")) {
                $stmt->bindValue(':lid', $lid);
                $stmt->bindValue(':kid', $kid);
                $stmt->bindValue(':uid', $uid);
                $stmt->bindValue(':extra', $extra);
                if (!$stmt->execute()) {
                    $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "[msFavorites] Could not update object extra. \n" . print_r($stmt->errorInfo(), true));
                }
            }
        }

    }


    public function getPls($additional = [], $exclude = [])
    {
        $pls = array_merge($additional, parent::toArray());
        if (!empty($exclude)) {
            // clear keys
            $pls = array_diff_key($pls, array_flip($exclude));
        }

        return $pls;
    }


    public function _getPls($additional = [], $exclude = [])
    {

        $pls = ['object' => parent::toArray()];

        /*foreach (['thumbs', 'options', 'place', 'metro'] as $a) {
            $pls[$a] = !empty($pls['object'][$a]) ? $pls['object'][$a] : [];
            unset($pls['object'][$a]);
        }

        foreach ($this->_meta as $a => $f) {
            if ($o = $this->loadAdditional($a) AND method_exists($o, 'getPls')) {
                $additional[strtolower($a)] = $o->getPls([], ['id']);
            }
        }*/

        return array_merge($pls, $additional);
    }

}