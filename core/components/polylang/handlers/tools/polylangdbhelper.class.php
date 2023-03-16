<?php
class PolylangDbHelper
{

    /** @var modX $modx */
    public $modx = null;
    /** @var array $config */
    public $config = array();

    public function __construct(modX &$modx, $config = array())
    {
        $this->modx = &$modx;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param string $class
     * @param string $name
     * @param array $meta
     * @param array $options
     * @return bool
     */
    public function addField($class, $name, array $meta, array $options = array())
    {
        $result = $this->addFieldMap($class, $name, $meta, $options);
        if ($result) {
            $this->modx->cacheManager->refresh();
            $result = $this->modx->getManager()->addField($class, $name, $options);
        }
        return $result;
    }

    /**
     * @param string $class
     * @param string $name
     * @param array $meta
     * @param array $options
     * @return bool
     */
    public function alterField($class, $name, array $meta, array $options = array())
    {
        $result = $this->alterFieldMap($class, $name, $meta, $options);
        if ($result) {
            $result = $this->modx->getManager()->alterField($class, $name, $options);
        }
        return $result;
    }

    /**
     * @param string $class
     * @param string $name
     * @param array $options
     * @return bool
     */
    public function removeField($class, $name, array $options = array())
    {
        $result = $this->removeFieldMap($class, $name, $options);
        if ($result) {
            $result = $this->modx->getManager()->removeField($class, $name, $options);
        }
        return $result;
    }

    /**
     * @param string $class
     * @param string $name
     * @param array $meta
     * @param array $options
     * @return bool
     */
    public function addFieldMap($class, $name, array $meta, array $options = array())
    {
        $result = false;
        $file = $this->getPathMetaFile($class);
        if ($file) {
            if (include($file)) {
                $xpdo_meta_map[$class]['fields'][$name] = $this->prepareFieldDefaultValue($meta);
                $xpdo_meta_map[$class]['fieldMeta'][$name] = $this->prepareFieldMeta($meta);
                if ($result = $this->saveMap($class, $xpdo_meta_map[$class])) {
                    $this->modx->map[$class] = $xpdo_meta_map[$class];
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not load metadata map {$file} for class {$class}");
            }
        }
        return $result;
    }

    /**
     * @param string $class
     * @param string $name
     * @param array $meta
     * @param array $options
     * @return bool
     */
    public function alterFieldMap($class, $name, array $meta, array $options = array())
    {
        return $this->addMetaFileField($class, $name, $meta, $options);
    }

    /**
     * @param string $class
     * @param string $name
     * @param array $options
     * @return bool
     */
    public function removeFieldMap($class, $name, array $options = array())
    {
        $result = false;
        $xpdo_meta_map = array();
        $file = $this->getPathMetaFile($class);
        if ($file) {
            if (include($file)) {
                unset($xpdo_meta_map[$class]['fields'][$name]);
                unset($xpdo_meta_map[$class]['fieldMeta'][$name]);
                if ($result = $this->saveMap($class, $xpdo_meta_map[$class])) {
                    $this->modx->map[$class] = $xpdo_meta_map[$class];
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not load metadata map {$file} for class {$class}");
            }
        }
        return $result;
    }

    /**
     * @param string $class
     * @param array $map
     * @return bool
     */
    protected function saveMap($class, array $map)
    {
        $result = false;
        $file = $this->getPathMetaFile($class);
        if ($file) {
            $out = var_export($map, true);
            $content = "<?php\n\$xpdo_meta_map['{$class}']={$out};";
            $result = $this->modx->cacheManager->writeFile($file, $content) ? true : false;
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not load metadata map {$file} for class {$class}");
        }
        return $result;
    }


    /**
     * @param array $meta
     * @return array
     */
    public function prepareFieldMeta(array $meta)
    {
        $result = array();
        $result['dbtype'] = empty($meta['dbtype']) ? 'text' : strtolower($meta['dbtype']);
        $default = $this->prepareFieldDefaultValue($meta);
        $defaultPrecision = $this->getDefaultPrecision($result['dbtype']);
        if (empty($meta['phptype'])) {
            $result['phptype'] = $this->modx->driver->getPhpType($meta['dbtype']);
        } else {
            $result['phptype'] = $meta['phptype'];
        }
        if (!empty($meta['precision'])) {
            $result['precision'] = $meta['precision'];
        } else if ($defaultPrecision) {
            $result['precision'] = $defaultPrecision;
        }
        if ($default !== null) {
            $result['default'] = $default;
        }
        $result['null'] = !isset ($meta['null']) ? true : ($meta['null'] === 'true' || !empty($meta['null']));
        return $result;
    }

    /**
     * @param array $meta
     * @return mixed|null
     */
    public function prepareFieldDefaultValue(array $meta)
    {
        $defaultVal = null;
        $lobs = array('TEXT', 'BLOB');
        $dbtype = strtoupper($meta['dbtype']);
        $datetimeStrings = array('timestamp', 'datetime');
        $lobsPattern = '/(' . implode('|', $lobs) . ')/';
        $defaultType = $this->modx->driver->getPhpType($dbtype);
        if (isset ($meta['default']) && !preg_match($lobsPattern, $dbtype)) {
            $defaultVal = $meta['default'];
            switch ($defaultType) {
                case 'integer':
                case 'boolean':
                case 'bit':
                    $defaultVal = (integer)$defaultVal;
                    break;
                case 'float':
                case 'numeric':
                    $defaultVal = (float)$defaultVal;
                    break;
                default:
                    break;
            }
            if (($defaultVal === null || strtoupper($defaultVal) === 'NULL') || (in_array($this->modx->driver->getPhpType($dbtype), $datetimeStrings) && $defaultVal === 'CURRENT_TIMESTAMP')) {
                $defaultVal = null;
            }
        }
        return $defaultVal;
    }

    /**
     * @param string $dbtype
     * @return string
     */
    public function getDefaultPrecision($dbtype)
    {

        switch ($dbtype) {
            case 'tinyint':
                $precision = '4';
                break;
            case 'smallint':
                $precision = '6';
                break;
            case 'mediumint':
                $precision = '9';
                break;
            case 'int':
                $precision = '11';
                break;
            case 'bigint':
                $precision = '20';
                break;
            case 'decimal':
                $precision = '10.0';
                break;
            case 'float':
                $precision = '';
                break;
            case 'double':
                $precision = '';
                break;
            default:
                $precision = '';
        }
        return $precision;
    }

    /**
     * @param $class
     * @return bool|string
     */
    public function getPathMetaFile($class)
    {
        $package = $this->modx->getPackage($class);
        if ($package && $this->modx->packages[$package]) {
            $fqn = strtolower($class);
            $dbtype = $this->modx->config['dbtype'];
            $path = $this->modx->packages[$package]['path'];
            $file = "{$path}{$package}/{$dbtype}/{$fqn}.map.inc.php";
            if (file_exists($file)) {
                return $file;
            } else {
                $this->modx->log(modX::LOG_LEVEL_WARN, "Could not find map file {$file} for class {$class}");
            }
        }
        return false;
    }


//    public function renameField($class, $oldName, $newName, array $options = array())
//    {
//        $result = false;
//        if ($this->modx->getConnection(array(modX::OPT_CONN_MUTABLE => true))) {
//            $className = $this->modx->loadClass($class);
//            if ($className) {
//                $options['rename'] = true;
//                $options['newName'] = $newName;
//                $meta = $this->modx->getFieldMeta($className, true);
//                if (is_array($meta) && array_key_exists($oldName, $meta)) {
//                    $colDef = $this->getColumnDef($className, $oldName, $meta[$oldName], $options);
//                    $sql = "ALTER TABLE {$this->modx->getTableName($className)} CHANGE {$colDef}";
//                    if ($this->modx->exec($sql) !== false) {
//                        $result = true;
//                    } else {
//                        $this->modx->log(modX::LOG_LEVEL_ERROR, "Error rename field {$class}->{$oldName} to {$class}->{$newName}: " . print_r($this->modx->errorInfo(), true), '', __METHOD__, __FILE__, __LINE__);
//                    }
//                } else {
//                    $this->xpdo->log(modX::LOG_LEVEL_ERROR, "Error altering field {$class}->{$oldName}: No metadata defined");
//                }
//            }
//        } else {
//            $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not get writable connection", '', __METHOD__, __FILE__, __LINE__);
//        }
//        return $result;
//    }
//
//
//    /**
//     * @param string $class
//     * @param string $name
//     * @param array $meta
//     * @param array $options
//     * @return bool
//     */
//    public function addIndex($class, $name, array $meta, array $options = array())
//    {
//        $result = false;
//        if ($this->modx->getConnection(array(modX::OPT_CONN_MUTABLE => true))) {
//            $className = $this->modx->loadClass($class);
//            if ($className) {
//                $idxDef = $this->getIndexDef($className, $name, $meta);
//                if (!empty($idxDef)) {
//                    $sql = "ALTER TABLE {$this->modx->getTableName($className)} ADD {$idxDef}";
//                    if ($this->modx->exec($sql) !== false) {
//                        $result = true;
//                    } else {
//                        $this->modx->log(modX::LOG_LEVEL_ERROR, "Error adding index {$name} to {$class}: " . print_r($this->modx->errorInfo(), true), '', __METHOD__, __FILE__, __LINE__);
//                    }
//                } else {
//                    $this->modx->log(modX::LOG_LEVEL_ERROR, "Error adding index {$name} to {$class}: Could not get index definition");
//                }
//
//            }
//        } else {
//            $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not get writable connection", '', __METHOD__, __FILE__, __LINE__);
//        }
//        return $result;
//    }
//
//    /**
//     * @param $class
//     * @param $name
//     * @param array $options
//     * @return bool
//     */
//    public function removeIndex($class, $name, array $options = array())
//    {
//        $result = false;
//        if ($this->modx->getConnection(array(modX::OPT_CONN_MUTABLE => true))) {
//            $className = $this->modx->loadClass($class);
//            if ($className) {
//                $sql = "ALTER TABLE {$this->modx->getTableName($className)} DROP INDEX {$this->modx->escape($name)}";
//                if ($this->modx->exec($sql) !== false) {
//                    $result = true;
//                } else {
//                    $this->modx->log(modX::LOG_LEVEL_ERROR, "Error removing index {$name} from {$class}: " . print_r($this->modx->errorInfo(), true), '', __METHOD__, __FILE__, __LINE__);
//                }
//            }
//        } else {
//            $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not get writable connection", '', __METHOD__, __FILE__, __LINE__);
//        }
//        return $result;
//    }
//
//
//    /**
//     * @param string $class
//     * @param string $name
//     * @param array $meta
//     * @param array $options
//     * @return string
//     */
//    protected function getColumnDef($class, $name, array $meta, array $options = array())
//    {
//        $pk = $this->modx->getPK($class);
//        $pktype = $this->modx->getPKType($class);
//        $dbtype = strtoupper($meta['dbtype']);
//        $lobs = array('TEXT', 'BLOB');
//        $lobsPattern = '/(' . implode('|', $lobs) . ')/';
//        $datetimeStrings = array('timestamp', 'datetime');
//        $precision = isset ($meta['precision']) ? '(' . $meta['precision'] . ')' : '';
//        $notNull = !isset ($meta['null']) ? false : ($meta['null'] === 'false' || empty($meta['null']));
//        $null = $notNull ? ' NOT NULL' : ' NULL';
//        $extra = '';
//        if (isset($meta['index']) && $meta['index'] == 'pk' && !is_array($pk) && $pktype == 'integer' && isset ($meta['generated']) && $meta['generated'] == 'native') {
//            $extra = ' AUTO_INCREMENT';
//        }
//        if (empty ($extra) && isset ($meta['extra'])) {
//            $extra = ' ' . $meta['extra'];
//        }
//        $default = '';
//        if (isset ($meta['default']) && !preg_match($lobsPattern, $dbtype)) {
//            $defaultVal = $meta['default'];
//            if (($defaultVal === null || strtoupper($defaultVal) === 'NULL') || (in_array($this->modx->driver->getPhpType($dbtype), $datetimeStrings) && $defaultVal === 'CURRENT_TIMESTAMP')) {
//                $default = ' DEFAULT ' . $defaultVal;
//            } else {
//                $default = ' DEFAULT \'' . $defaultVal . '\'';
//            }
//        }
//        $attributes = (isset ($meta['attributes'])) ? ' ' . $meta['attributes'] : '';
//        if ($this->modx->getOption('rename', $options, false)) {
//            $newName = $this->modx->getOption('newName', $options, '');
//            $result = $this->modx->escape($name) . ' ' . $this->modx->escape($newName) . ' ' . $dbtype . $precision . $null . $default;
//        } else {
//            if (strpos(strtolower($attributes), 'unsigned') !== false) {
//                $result = $this->modx->escape($name) . ' ' . $dbtype . $precision . $attributes . $null . $default . $extra;
//            } else {
//                $result = $this->modx->escape($name) . ' ' . $dbtype . $precision . $null . $default . $attributes . $extra;
//            }
//        }
//        return $result;
//    }
//
//    /**
//     * @param string $class
//     * @param string $name
//     * @param array $meta
//     * @param array $options
//     * @return string
//     */
//    protected function getIndexDef($class, $name, $meta, array $options = array())
//    {
//        $result = '';
//        if (isset($meta['type']) && $meta['type'] == 'FULLTEXT') {
//            $indexType = 'FULLTEXT';
//        } else if (!empty($meta['primary'])) {
//            $indexType = 'PRIMARY KEY';
//        } else if (!empty($meta['unique'])) {
//            $indexType = 'UNIQUE KEY';
//        } else {
//            $indexType = 'INDEX';
//        }
//        $index = $meta['columns'];
//        if (is_array($index)) {
//            $indexset = array();
//            foreach ($index as $indexmember => $indexmemberdetails) {
//                $indexMemberDetails = $this->modx->escape($indexmember);
//                if (isset($indexmemberdetails['length']) && !empty($indexmemberdetails['length'])) {
//                    $indexMemberDetails .= " ({$indexmemberdetails['length']})";
//                }
//                $indexset[] = $indexMemberDetails;
//            }
//            $indexset = implode(',', $indexset);
//            if (!empty($indexset)) {
//                switch ($indexType) {
//                    case 'PRIMARY KEY':
//                        $result = "{$indexType} ({$indexset})";
//                        break;
//                    default:
//                        $result = "{$indexType} {$this->modx->escape($name)} ({$indexset})";
//                        break;
//                }
//            }
//        }
//        return $result;
//    }

}