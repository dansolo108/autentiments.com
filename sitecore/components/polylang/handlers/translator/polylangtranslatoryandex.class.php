<?php

use Yandex\Translate\Translator;
use Yandex\Translate\Exception;

class PolylangTranslatorYandex extends PolylangTranslator
{
    /** @var Translator|null $translator */
    public $translator = null;

    public function __construct(Polylang &$polylang, $config = array())
    {
        parent::__construct($polylang, $config);
        $key = $this->modx->getOption('polylang_translate_yandex_key', $config, '', true);
        if ($key) {
            $this->translator = new Translator($key);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Not set yandex API key!');
        }
    }

    /**
     * @param string $text
     * @param string $from
     * @param string $to
     * @param array $options
     * @return string|false
     */
    public function translate($text, $from, $to, array $options = array())
    {
        if (!$this->translator) return false;
        try {
            if ($text) {
                $html = $this->modx->getOption('html', $options, true, true);
                $text = (string)$this->translator->translate($text, "{$from}-{$to}", $html);
                if ($this->isPostProcessingTranslation) {
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
