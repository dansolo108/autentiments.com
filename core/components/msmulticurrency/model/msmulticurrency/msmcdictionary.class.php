<?php
/**
 * Abstracts storage of values of posted fields.
 *
 * @package msmulticurrency
 */

class MsMCDictionary {
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;
    /**
     * A reference to the MsMC instance
     * @var MsMC $msmc
     */
    public $msmc;
    /**
     * A configuration array
     * @var array $config
     */
    public $config = array();
    /**
     * An array of key->name pairs storing the fields passed
     * @var array $fields
     */
    public $fields = array();

    /**
     * @param MsMC $msmc
     * @param array $config
     */
    function __construct(MsMC &$msmc,array $config = array()) {
        $this->modx =& $msmc->modx;
        $this->formit =& $msmc;
        $this->config = array_merge($this->config,$config);
    }

    /**
     * Get the fields from POST
     *
     * @param array $fields A default set of fields to load
     * @return void
     */
    public function gather(array $fields = array()) {
        if (empty($fields)) $fields = array();
        $this->fields = array_merge($fields,$_POST);
        if (!empty($_FILES)) { $this->fields = array_merge($this->fields,$_FILES); }
    }

    /**
     * Set a value
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function set($field,$value, $clean = false) {
        $value = $clean ? htmlspecialchars(strip_tags($value)) : $value;
        $this->fields[$field] = $value;
    }

    /**
     * Get a field value
     * @param string $field
     * @param mixed $default
     * @return mixed
     */
    public function get($field,$default = null) {
        return isset($this->fields[$field]) ? $this->fields[$field] : $default;
    }

    /**
     * Return all field values in an array of key->name pairs
     * @return array
     */
    public function toArray() {
        return $this->fields;
    }

    /**
     * Set a variable number of fields by passing in a key->name pair array
     * @param array $array
     * @return void
     */
    public function fromArray(array $array) {
        foreach ($array as $k => $v) {
            $this->fields[$k] = $v;
        }
    }

    /**
     * Remove a field from the stack
     * @param string $key
     * @return void
     */
    public function remove($key) {
        unset($this->fields[$key]);
    }

    /**
     * @return void
     */
    public function reset() {
        $this->fields = array();
    }
}