<?php

class PolylangPolylangContentGenerateProcessor extends modProcessor
{
    public $classKey = 'PolylangContent';
    public $languageTopics = array('polylang:default');
    /** @var Polylang $polylang */
    public $polylang;
    /** @var PolylangTools $tools */
    public $tools = null;
    /** @var modResource $object */
    public $object = null;
    /** @var array $items */
    public $items;
    /** @var bool $overwrite */
    public $overwrite;
    /** @var bool $disallowTranslationCompletedField */
    public $disallowTranslationCompletedField;

    public function initialize()
    {
        $initialized = parent::initialize();
        $id = $this->getProperty('rid', 0);
        $this->overwrite = $this->getProperty('overwrite', false);
        $this->disallowTranslationCompletedField = $this->modx->getOption('polylang_disallow_translation_completed_field');
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        $this->tools = $this->polylang->getTools();
        $this->object = $this->modx->getObject('modResource', $id);
        if (empty($this->object)) {
            $initialized = $this->failure($this->modx->lexicon('polylang_content_err_nf_resource', array('id' => $id)));
        }
        return $initialized;
    }

    public function process()
    {
        $languages = $this->getLanguageKeys(!$this->overwrite);
        $translate = $this->getProperty('translate', false);
        if ($languages) {
            $this->items = $translate ? $this->getTranslateItems() : array();
            foreach ($languages as $language) {
                $data = array();
                if ($translate) {
                    $result = $this->translate($language, $this->items);
                    if (is_string($result)) return $this->failure($result);
                    $data = $this->prepareData($result);
                }
                $content = $this->execute($language, $data);
                if (is_string($content)) return $this->failure($content);
            }
        }
        return $this->success();
    }

    /**
     * @param string $to
     * @param array $items
     * @return array|string
     */
    public function translate($to, array $items)
    {
        if (empty($items)) return array();
        /** @var modProcessorResponse $response */
        $response = $this->polylang->runProcessor('mgr/translator/translate', array(
            'to' => $to,
            'items' => $items,
            'rid' => $this->object->get('id')
        ));
        if ($response->isError()) {
            return $response->getMessage();
        }
        return $response->getObject();
    }

    /**
     * @param $language
     * @param array $data
     * @return string|PolylangContent
     */
    public function execute($language, array $data = array())
    {
        $obj = $this->modx->getObject('PolylangContent', array(
            'content_id' => $this->object->get('id'),
            'culture_key' => $language
        ));
        $params = array_merge($data, array(
            'polylangcontent_content_id' => $this->object->get('id'),
            'polylangcontent_culture_key' => $language,
        ));
        if (!$obj) {
            $response = $this->polylang->runProcessor('mgr/polylangcontent/create', $params);
            if ($response->isError()) {
                return $response->getMessage();
            }
            $obj = $response->getObject();
        } else if ($this->overwrite) {
            if ($this->disallowTranslationCompletedField) {
                $oldData = $this->getDataFromLocalization($language);
                foreach ($data as $key => $val) {
                    if (isset($this->items[$key])) {
                        $field = $this->items[$key]['key'];
                        if (!empty($oldData[$field])) {
                            unset($params[$key]);
                        }
                    }
                }
            }
            if (count($params) <= 2) {
                return;
            }
            $params['id'] = $obj->get('id');
            $response = $this->polylang->runProcessor('mgr/polylangcontent/update', $params);
            if ($response->isError()) {
                return $response->getMessage();
            }
            $obj = $response->getObject();
        }
        return $obj;
    }

    /**
     * @param array $items
     * @return array
     */
    public function prepareData(array $items)
    {
        $result = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                if (empty($item['text'])) continue;
                $result[$item['name']] = $item['text'];
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getTranslateItems()
    {
        $result = array();
        /** @var modProcessorResponse $response */
        $response = $this->polylang->runProcessor('mgr/polylangcontent/render', array('rid' => $this->object->get('id')));
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
        } else if ($data = $response->getObject()) {
            $result = $this->parseFormItems($data['items']);
            if (!empty($data['tvs'])) {
                $response = $this->polylang->runProcessor('mgr/polylangtv/render', array(
                    'rid' => $this->object->get('id'),
                    'render' => false
                ));
                if ($response->isError()) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
                } else if ($data = $response->getObject()) {
                    foreach ($data['categories'] as $item) {
                        $result = array_merge($result, $this->parseFormTvs($item));
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param array $items
     * @return array
     */
    public function parseFormItems(array $items)
    {
        $result = array();
        if ($items) {
            if (isset($items['items'])) {
                $result = array_merge($result, $this->parseFormItems($items['items']));
            } else {
                foreach ($items as $key => $item) {
                    if (isset($item['items'])) {
                        $result = array_merge($result, $this->parseFormItems($item['items']));
                    } else if (is_array($item)) {
                        if (empty($item['translate'])) {
                            continue;
                        }
                        $result[$item['name']] = array(
                            'key' => $item['key'],
                            'name' => $item['name'],
                            'source' => $item['source'],
                        );
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param array $items
     * @return array
     */
    public function parseFormTvs(array $items)
    {
        $result = array();
        if ($items) {
            if (isset($items['tvs'])) {
                $result = array_merge($result, $this->parseFormTvs($items['tvs']));
            } else {
                foreach ($items as $item) {
                    if (empty($item['polylang_translate'])) continue;
                    $name = 'tvpolylang_' . $item['name'];
                    $result[$name] = array(
                        'key' => $item['name'],
                        'name' => $name,
                        'source' => 'PolylangTv',
                    );
                }
            }
        }
        return $result;
    }

    /**
     * @param bool $onlyNoLocalization
     *
     * @return array
     */
    public function getLanguageKeys($onlyNoLocalization = true)
    {
        $defaultLanguage = $this->tools->getDefaultLanguage();
        $keys = $this->tools->getLanguageKeys();
        $exclude = array($defaultLanguage);
        if ($onlyNoLocalization) {
            $exclude = array_merge($exclude, $this->getResourceLanguageKeys());
        }
        return array_diff($keys, $exclude);
    }

    /**
     * @return array
     */
    public function getResourceLanguageKeys()
    {
        $result = array();
        $q = $this->modx->newQuery($this->classKey);

        $q->select($this->modx->getSelectColumns($this->classKey, $this->classKey, '', array('culture_key')));
        $q->where(array('content_id' => $this->object->get('id')));

        if ($q->prepare() && $q->stmt->execute()) {
            $result = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return $result;
    }

    /**
     * @param string $cultureKey
     *
     * @return array
     */
    public function getDataFromLocalization($cultureKey)
    {
        $data = array();
        $options = array(
            'skipTVs' => false,
            'cultureKey' => $cultureKey,
            'class' => get_class($this->object),
            'content_id' => $this->object->get('id'),
        );
        $this->tools->prepareResourceData(function ($key, $value) use (&$data) {
            $data[$key] = $value;
        }, $options);
        return $data;
    }
}

return 'PolylangPolylangContentGenerateProcessor';