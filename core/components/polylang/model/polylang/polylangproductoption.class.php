<?php

abstract class PolylangOptionType
{

    /** @var msOption $option */
    public $option;
    /** @var xPDO $xpdo */
    public $xpdo;
    /** @var array $config */
    public $config = array();
    public static $script = null;
    public static $xtype = null;

    /**
     * msOptionType constructor.
     * @param msOption $option
     * @param array $config
     */
    public function __construct(msOption $option, array $config = array())
    {
        $this->option =& $option;
        $this->xpdo =& $option->xpdo;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param $criteria
     * @return mixed|null
     * @TODO Maybe vulnerable
     */
    public function getValue($criteria)
    {
        /** @var msProductOption $value */
        $value = $this->xpdo->getObject('PolylangProductOption', $criteria);
        return ($value) ? $value->get('value') : null;
    }

    /**
     * @param $criteria
     * @return mixed|null
     */
    public function getRowValue($criteria)
    {
        return $this->getValue($criteria);
    }

    /**
     * @param $field
     * @return mixed
     */
    public abstract function getField($field);
}

class PolylangProductOption extends xPDOObject
{

    /**
     * @param xPDO $xpdo
     * @param msOption $option
     * @param int $contentId
     * @param string $cultureKey
     * @return mixed|null
     */
    public static function _getValue(xPDO &$xpdo, msOption &$option, $contentId, $cultureKey)
    {

        /** @var Polylang $polylang */
        $polylang = $xpdo->getService('polylang', 'Polylang');
        /** @var PolylangOptionType $type */
        $type = $polylang->getTools()->getOptionType($option);
        if ($type) {
            $criteria = array(
                'key' => $option->get('key'),
                'content_id' => $contentId,
                'culture_key' => $cultureKey,
            );
            return $type->getValue($criteria);
        }
        return null;
    }


    /**
     * @param msOption $option
     * @return mixed|null
     * @TODO Maybe vulnerable
     */
    public function getValue(msOption &$option)
    {
        return $this->xpdo->call('PolylangProductOption', '_getValue', array(
            &$this->xpdo,
            $option,
            $this->get('content_id'),
            $this->get('culture_key'),
        ));
    }


}