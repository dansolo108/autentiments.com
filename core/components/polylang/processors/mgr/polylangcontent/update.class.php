<?php

class PolylangPolylangContentUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'PolylangContent';
    public $languageTopics = array('polylang:default');
    public $beforeSaveEvent = 'OnBeforeSavePolylangContent';
    public $afterSaveEvent = 'OnSavePolylangContent';
    /** @var Polylang $polylang */
    public $polylang;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    /**
     * @return array|string
     */
    public function beforeSet()
    {
        $canSet = parent::beforeSet();
        if ($canSet) {
            $properties = array('foreign_properties' => array());
            $classes = $this->polylang->getTools()->getContentClasses();
            $classes['tvpolylang'] = 'PolylangContent';
            $data = $this->getProperties();
            $contentId = $this->modx->getOption('polylangcontent_content_id', $data);
            $cultureKey = $this->modx->getOption('polylangcontent_culture_key', $data);
            foreach ($data as $key => $value) {
                if (preg_match('/^([a-zA-Z0-9]+)(?=_)/', $key, $match)) {
                    $classKey = $match[1];
                    if (isset($classes[$classKey])) {
                        $class = $classes[$classKey];
                        $prefix = $this->modx->call($class, 'getFieldPrefix', array($classKey));
                        $key = $prefix . str_replace("{$classKey}_", '', $key);
                        if ($class == $this->classKey) {
                            $properties[$key] = $value;
                        } else {
                            if (!isset($properties['foreign_properties'][$class])) {
                                $properties['foreign_properties'][$class] = array(
                                    'content_id' => $contentId,
                                    'culture_key' => $cultureKey,
                                );
                            }
                            $properties['foreign_properties'][$class][$key] = $value;
                        }
                    }
                }
            }
            $this->setProperties($properties);
        }
        return $canSet;
    }

    public function afterSave()
    {
        $canSave = parent::afterSave();
        if ($canSave) {
            $properties = $this->getProperty('foreign_properties', array());
            if ($properties) {
                foreach ($properties as $class => $data) {
                    /** @var xPDOSimpleObject $o */
                    $q = $this->modx->newQuery($class);
                    $q->where(array(
                        'content_id' => $data['content_id'],
                        'culture_key' => $data['culture_key']
                    ));
                    if (!$o = $this->modx->getObject($class, $q)) {
                        $o = $this->modx->newObject($class);
                    }
                    $o->fromArray($data);
                    if (!$o->save()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "Error save data for class {$class}. Data:\n" . print_r($data, 1));
                    }
                }
            }
        }
    }

}

return 'PolylangPolylangContentUpdateProcessor';