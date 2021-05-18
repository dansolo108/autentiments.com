<?php

use Prihod\Translate\Translator;
use Prihod\Translate\Exception;

class PolylangTranslatorPromt extends PolylangTranslator
{
    /** @var Translator|null $translator */
    public $translator = null;
    /** @var string */
    protected $profile;
    /** @var string */
    protected $format;

    public function __construct(Polylang &$polylang, $config = array())
    {
        parent::__construct($polylang, $config);
        $apiConfig = $this->modx->getOption('polylang_translate_promt_config', $config, '{}', true);
        $apiConfig = $this->modx->fromJSON($apiConfig);
        $this->profile = $this->modx->getOption('profile', $apiConfig, '');
        $this->format = $this->modx->getOption('format', $apiConfig, 'text/html');
        if (empty($apiConfig['key'])) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Not set PROMT API key!');
        } else {
            $this->translator = new Translator($apiConfig['key']);
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
                $profile = $this->modx->getOption('profile', $options, $this->profile);
                $format = $this->modx->getOption('format', $options, $this->format);
                $text = (string)$this->translator->translate($text, $from, $to, $profile, $format);
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