<?php

class mspcCouponGenerateProcessor extends modObjectProcessor
{
    public $objectType = 'mspcCoupon';
    public $classKey = 'mspcCoupon';
    public $languageTopics = array('mspromocode:default');

    //public $permission = 'save';

    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $mask = $this->getProperty('mask');
        $count = (int)$this->getProperty('count');
        $action_id = (int)$this->getProperty('action_id');
        // $action_ref = (int) $this->getProperty('action_ref');

        if (empty($mask) || empty($action_id)) {
            return $this->failure($this->modx->lexicon('mspromocode_err_all_fields'));
        }
        if (!$action_obj = $this->modx->getObject('mspcAction', $action_id)) {
            return $this->failure($this->modx->lexicon('mspromocode_err_nf'));
        }
        $action = $action_obj->toArray('', true);

        if ($action['ref']) {
            $users = array();
            $modUsers = $this->modx->getCollection('modUser');
            foreach ($modUsers as $modUser) {
                if (!$this->modx->getCount('mspcCoupon', array(
                    'action_id' => $action['id'],
                    'referrer_id' => $modUser->id,
                ))
                ) {
                    $users[] = $modUser->id;
                }
            }
            unset($modUsers);

            $count = count($users);
        }

        if (!$action['ref'] && empty($count)) {
            return $this->failure($this->modx->lexicon('mspromocode_err_field_count'));
        } elseif ($action['ref'] && empty($count)) {
            return $this->failure($this->modx->lexicon('mspromocode_err_ref_field_count'));
        }

        $data = array(
            'created' => 0,
        );
        for ($i = 0; $i < $count; ++$i) {
            $c = 0;
            while ($c < 10 && (!$c || $this->modx->getCount($this->classKey, array('code' => $code)))) {
                $code = $this->modx->mspromocode->genRegExpString($mask);
                ++$c;
            }

            $resp_create = $this->modx->runProcessor('coupon/create', array(
                'action_id' => $action['id'],
                'referrer_id' => $action['ref'] ? $users[$i] : 0,
                'code' => $code,
                'discount' => $action['discount'],
                'begins' => $action['begins'],
                'ends' => $action['ends'],
                'count' => $action['ref'] ? '' : 1,
                'active' => 1,
            ), array('processors_path' => MODX_CORE_PATH . 'components/mspromocode/processors/mgr/'));

            $create = $resp_create->response;
            // $this->modx->log(1, print_r($create,1) );

            $data['created'] = $i + 1;
        }

        return $this->success('', $data);
    }
}

return 'mspcCouponGenerateProcessor';
