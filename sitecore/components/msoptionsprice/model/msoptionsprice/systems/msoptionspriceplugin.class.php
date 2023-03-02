<?php

abstract class msOptionsPricePlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var msoptionsprice $msoptionsprice */
    protected $msoptionsprice;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct(modX $modx, &$scriptProperties)
    {
        $this->modx = &$modx;
        $this->scriptProperties =& $scriptProperties;

        $fqn = $modx->getOption('msoptionsprice_class', null, 'msoptionsprice.msoptionsprice', true);
        $path = $modx->getOption('msoptionsprice_core_path', null,
            $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msoptionsprice/');
        $this->msoptionsprice = $modx->getService(
            $fqn,
            '',
            $path . 'model/',
            array_merge($this->scriptProperties, array('core_path' => $path))
        );
        if (!$this->msoptionsprice) {
            return;
        }

        $this->msoptionsprice->initialize($this->modx->context->key);
    }

    abstract public function run();

    public function setOption($key, $value) {
        $this->msoptionsprice->setOption($key, $value);
    }
}