<?php

class PolylangTranslateProcessor extends modProcessor
{
    public $languageTopics = array('polylang:translator');
    /** @var Polylang $polylang */
    public $polylang = null;
    /** @var PolylangTools $tools */
    public $tools = null;
    /** @var PolylangTranslator $translator */
    public $translator = null;
    /** @var modResource $object */
    public $object = null;
    public $arrayDelimiter = '|';


    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        $this->tools = $this->polylang->getTools();
        return parent::initialize();
    }

    public function process()
    {
        $id = $this->getProperty('rid', 0);
        $text = $this->getProperty('text', null);
        if ($id && $text === null) {
            $this->object = $this->modx->getObject('modResource', $id);
            if (!$this->object) {
                return $this->failure($this->modx->lexicon('polylang_translator_err_nf_resource', array('id' => $id)));
            }
        }
        /** @var  PolylangTranslator $translator */
        $this->translator = $this->polylang->getTranslator();
        if (!$this->translator->isInitialized()) {
            return $this->failure($this->modx->lexicon('polylang_translator_err_initialization'));
        }
        return $this->translate();
    }

    /**
     * @return array|string
     */
    public function translate()
    {
        $defaultLanguage = $this->tools->getDefaultLanguage();
        $dataSourceLanguage = $this->modx->getOption('polylang_translate_data_source_language');
        $dataSourceLanguage = $this->tools->fromJSON($dataSourceLanguage, array());
        $text = $this->getProperty('text', '');
        $to = $this->getProperty('to');
        $from = $this->getProperty('from', $defaultLanguage);

        if (empty($to)) {
            return $this->failure($this->modx->lexicon('polylang_translator_err_ns_language'));
        }
        if ($text) {
            $translate = $this->translator->translate($text, $from, $to);
            if ($translate === false) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Error translate text:'{$text}' from {$from} to {$to}");
            } else {
                $items = array('translate' => $translate);
            }
        } else {
            $items = $this->getProperty('items');
            if (is_string($items)) {
                $items = $this->polylang->getTools()->fromJSON($this->getProperty('items'));
            }
            if (empty($items)) {
                return $this->failure($this->modx->lexicon('polylang_translator_err_incorrect_data'));
            }
            if ($dataSourceLanguage && !empty($dataSourceLanguage[$to])) {
                $from = $dataSourceLanguage[$to];
                $data = $this->getDataFromLocalization($from);
            } else {
                $data = $this->getDataFromResource();
            }
            foreach ($items as &$item) {
                $text = '';
                $key = $item['key'];
                switch ($item['source']) {
                    case 'PolylangProductOption':
                        $key .= '.value';
                    default:
                        if (isset($data[$key]) && !empty($data[$key])) {
                            $text = $data[$key];
                        }
                        break;
                }

                if (!empty($text)) {
                    $isArray = false;
                    if (is_array($text)) {
                        $isArray = true;
                        $text = $this->tools->cleanAndImplode($text, $this->arrayDelimiter);
                    }
                    $translate = $this->translator->translate($text, $from, $to);
                    if ($translate === false) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "Error translate text:'{$text}' from {$from} to {$to}");
                    } else {
                        if ($isArray) {
                            $translate = $this->tools->explodeAndClean($translate, $this->arrayDelimiter);
                        }
                        $item['text'] = $translate;
                    }
                }
            }
        }
        return $this->success('', $items);
    }

    /**
     * @return array
     */
    public function getDataFromResource()
    {
        $data = $this->object->toArray();
        $tvs = $this->object->getTemplateVars();
        if ($tvs) {
            foreach ($tvs as $tv) {
                $val = $tv->renderOutput($this->object->get('id'));
                $data[$tv->get('name')] = $val;
            }
        }
        return $data;
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

return 'PolylangTranslateProcessor';