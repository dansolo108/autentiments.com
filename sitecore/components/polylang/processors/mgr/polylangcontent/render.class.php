<?php

class PolylangPolylangContentRenderProcessor extends modProcessor
{
    public $classKey = 'PolylangContent';
    public $languageTopics = array('polylang:default');
    /** @var  PolylangContent $object */
    public $object = null;
    /** @var Polylang $polylang */
    public $polylang = null;
    /** @var modResource $resource */
    public $resource = null;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function process()
    {
        $mode = 'upd';
        $id = $this->getProperty('id', 0);
        $rid = $this->getProperty('rid', 0);

        $this->object = $this->modx->getObject($this->classKey, $id);
        if (!$this->object) {
            $mode = 'new';
            $this->object = $this->modx->newObject('PolylangContent');
            $this->object->set('content_id', $rid);
        }
        $this->resource = $this->object->getOne('Resource');

        $data = $this->getData();
        $items = $this->polylang->getTools()->render($this->resource, $this->object->get('culture_key'));

        $response = $this->polylang->getTools()->invokeEvent('OnGetPolylangContent', array(
            'id' => $id,
            'mode' => $mode,
            'items' => & $items,
            'data' => & $data,
            'object' => $this->object,
        ));

        if ($response['success']) {
            return $this->success('', array(
                'items' => $response['data']['items'],
                'data' => $response['data']['data'],
                'tvs' => $this->object->getTVKeys() ? true : false,
            ));
        } else {
            return $this->failure($response['message']);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        $keyPrefix = strtolower($this->classKey) . '-';
        $data = array("{$keyPrefix}content_id" => $this->resource->get('id'));
        if ($this->object) {
            $data = $this->object->toArray($keyPrefix);
            $data['id'] = $this->object->get('id');
            $classes = $this->polylang->getTools()->getContentClasses(array($this->classKey));
            if ($classes) {
                foreach ($classes as $key => $class) {
                    $o = $this->modx->getObject($class, array(
                        'content_id' => $this->object->get('content_id'),
                        'culture_key' => $this->object->get('culture_key'),
                    ));
                    if ($o) {
                        $arr = $o->toArray();
                        unset($arr['id'], $arr['content_id'], $arr['culture_key']);
                        $keyPrefix = "{$key}-";
                        $fields = array_keys($this->modx->getFieldMeta($class));
                        foreach ($arr as $k => $v) {
                            if (is_array($v) && in_array($k, $fields)) {
                                $tmp = $arr[$k];
                                $arr[$k] = array();
                                foreach ($tmp as $v2) {
                                    if (!empty($v2)) {
                                        $arr[$keyPrefix . $k][] = array('value' => $v2);
                                    }
                                }
                            } else {
                                $arr[$keyPrefix . $k] = $v;
                            }
                        }
                        $data = array_merge($data, $arr);
                    }
                }
            }
        }
        return $data;
    }
}

return 'PolylangPolylangContentRenderProcessor';