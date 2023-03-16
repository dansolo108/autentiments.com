<?php

/**
 * Get an msopModification
 */
class msopModificationOrderCheckProcessor extends modProcessor
{
    public $objectType = 'msopModification';
    public $classKey = 'msopModification';
    public $languageTopics = array('msoptionsprice');
    public $permission = '';

    public function initialize()
    {
        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function process()
    {
        $ctx = $this->getProperty('ctx', $this->modx->context->key);

        /** @var msoptionsprice $msoptionsprice */
        $msoptionsprice = $this->modx->getService('msoptionsprice');
        $msoptionsprice->initialize($ctx);

        /** @var miniShop2 $miniShop2 */
        $miniShop2 = $this->modx->getService('miniShop2');
        $miniShop2->initialize($ctx);

        $check = true;
        $cart = $required = array();
        if ($msoptionsprice->getOption('allow_remains')) {

            $items = $miniShop2->cart->get();
            foreach ($items as $key => $item) {
                $count = $this->modx->getOption('count', $item, 0, true);
                $options = $this->modx->getOption('options', $item, array(), true);
                $mid = (int)$this->modx->getOption('modification', $options);
                if (empty($mid)) {
                    continue;
                }
                $mo = $this->modx->getObject('msopModification', array('id' => $mid));
                if (!$mo) {
                    continue;
                }

                $mCount = $mo->get('count');

                $cart[$key] = array(
                    'count' => $mCount,
                    'data'  => $item
                );

                if ($count > $mCount) {
                    $required[$key] = array(
                        'count'   => $mCount,
                        'data'    => $item,
                        'message' => $msoptionsprice->lexicon('err_available_remains',
                            array_merge($item, array('count' => $mCount)))
                    );
                }
            }
        }

        if (!empty($required)) {
            $check = false;
        }

        $data = array(
            'cart'     => $cart,
            'required' => $required
        );

        return $check
            ? $msoptionsprice->success('', $data)
            : $msoptionsprice->failure($msoptionsprice->lexicon('err_available_count'), $data);
    }

}

return 'msopModificationOrderCheckProcessor';