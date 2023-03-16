<?php

use Google\Cloud\Translate\V2\TranslateClient;

class PolylangTranslatorGoogle extends PolylangTranslator
{
    /** @var Translator|null $translator */
    public $translator = null;

    public function __construct(Polylang &$polylang, $config = array())
    {
        parent::__construct($polylang, $config);
        $key = $this->modx->getOption('polylang_translate_google_key', $config, '', true);
        $referer = MODX_URL_SCHEME . MODX_HTTP_HOST;
        if ($key) {
            $this->translator = new TranslateClient(array(
                'key' => $key,
                'restOptions' => array(
                    'headers' => array(
                        'referer' => $referer
                    )
                )
            ));
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Not set google API key!');
        }
    }

    /**
     * @param string $text
     * @param string $from
     * @param string $to
     * @param array $options
     *
     * @return string|false
     */
    public function translate($text, $from, $to, array $options = array())
    {
        if (!$this->translator) return false;
        try {
            if ($text) {
                if ($from == 'ua') $from = 'uk';
                if ($to == 'ua') $to = 'uk';
                $result = $this->translator->translate($text, array('source' => $from, 'target' => $to));
                $text = isset($result['text']) ? $result['text'] : $text;
                if($this->isPostProcessingTranslation) {
                    $text = $this->postProcessingTranslation($text);
                }
            }
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
            return false;
        }
        return $text;
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->translator !== null;
    }
}
