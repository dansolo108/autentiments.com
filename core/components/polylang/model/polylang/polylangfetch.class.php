<?php

if (!class_exists('pdoFetch')) {
    require_once MODX_CORE_PATH . 'components/pdotools/model/pdotools/pdofetch.class.php';
}

class PolylangFetch extends pdoFetch
{
    /** @var Polylang $polylang */
    public $polylang;
    /** @var PolylangTools $tools */
    public $tools;

    /**
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX &$modx, $config = array())
    {
        parent::__construct($modx, $config);
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        $this->tools = $this->polylang->getTools();
    }


    /**
     * PDO replacement for modX::getObject()
     * Returns array instead of object
     *
     * @param $class
     * @param string $where
     * @param array $config
     *
     * @return array
     */
    public function getArray($class, $where = '', $config = array())
    {
        $config['limit'] = 1;
        $rows = $this->getCollection($class, $where, $config);
        $rows = $this->prepareResourceRows($rows, $config);
        return !empty($rows[0]) ? $rows[0] : array();
    }

    /**
     * Prepares fetched rows and process template variables
     *
     * @param array $rows
     *
     * @return array
     */
    public function prepareRows(array $rows = array())
    {
        $rows = parent::prepareRows($rows);
        return $this->prepareResourceRows($rows);
    }

    public function prepareResourceRows(array $rows = array(), $config = array())
    {
        if($this->config['polyLang'] === false){
            return $rows;
        }
        $options = array(
            'class' => $this->config['class'],
            'cultureKey' => $this->modx->getOption('cultureKey'),
            'tvPrefix' => !empty($this->config['tvPrefix']) ? trim($this->config['tvPrefix']) : '',
            'includeTVs' => !empty($this->config['includeTVs']) ? $this->config['includeTVs'] : '',
        );
        foreach ($rows as & $row) {
            if (!isset($row['id']) && !isset($config['id'])) continue;
            $options['content_id'] = $row['id'] ? $row['id'] : $config['id'];
            $row['polylang_override'] = 1;
            $this->tools->prepareResourceData(function ($key, $value, $context) use (&$row) {
                $row['polylang_original_' . $key] = $row[$key];
                $row[$key] = $value;
            }, $options);
        }
        return $rows;
    }
}
