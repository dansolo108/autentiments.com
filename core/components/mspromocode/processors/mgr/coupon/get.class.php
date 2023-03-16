<?php

class mspcCouponGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'mspcCoupon';
    public $classKey = 'mspcCoupon';
    public $languageTopics = array('mspromocode:default');
    //public $permission = 'view';

    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject.
     *
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        return parent::process();
    }

    public function cleanup()
    {
        $array = $this->object->toArray('', true);

        if (empty($array['action']) && !empty($array['action_id'])) {
            $action = $this->object->Action->toArray('', true);
            $array['action'] = $action['name'];
        }

        // если купон для акции и он активирован
        $array['activated'] = false;
        if (!empty($array['action_id']) && $array['count'] == 0 && !$array['active']) {
            $array['activated'] = true;
        }

        return $this->success('', $array);
    }
}

return 'mspcCouponGetProcessor';
