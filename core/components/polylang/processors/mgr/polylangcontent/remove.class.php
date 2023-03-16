<?php

class PolylangPolylangContentRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'PolylangContent';
    public $languageTopics = array('polylang:default');
    public $beforeRemoveEvent = 'OnBeforeRemovePolylangContent';
    public $afterRemoveEvent = 'OnRemovePolylangContent';
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function afterRemove()
    {
        $cenRemove = parent::afterRemove();
        if ($cenRemove) {
            $classes = $this->polylang->getTools()->getContentClasses(array($this->classKey));
            if ($classes) {
                foreach ($classes as $key => $class) {
                    /** @var xPDOSimpleObject $o */
                    $o = $this->modx->getObject($class, array(
                        'content_id' => $this->object->get('content_id'),
                        'culture_key' => $this->object->get('culture_key'),
                    ));
                    if ($o) {
                        $o->remove();
                    }
                }
            }
        }
        return $cenRemove;
    }

}

return 'PolylangPolylangContentRemoveProcessor';