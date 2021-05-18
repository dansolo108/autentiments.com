<?php

interface PolylangTranslatorInterface
{
    /**
     * @return bool
     */
    public function isInitialized();

    /**
     * @param string $text
     * @param string $from
     * @param string $to
     * @param array $options
     * @return string|false
     */
    public function translate($text, $from, $to, array $options = array());
}

class PolylangTranslator implements PolylangTranslatorInterface
{
    /** @var modX */
    public $modx = null;
    /** @var Polylang */
    public $polylang = null;
    /** @var array */
    public $config = array();
    /** @var bool */
    protected $isPostProcessingTranslation = false;

    public function __construct(Polylang $polylang, $config = array())
    {
        $this->polylang = $polylang;
        $this->modx = $polylang->modx;
        $this->config = array_merge($this->config, $config);
        $this->isPostProcessingTranslation = $this->modx->getOption('polylang_post_processing_translation');
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return true;
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
        return $this->postProcessingTranslation($text);
    }

    /**
     * @param $text
     *
     * @return string
     */
    public function postProcessingTranslation($text)
    {
        if (empty($text)) return $text;

        $patterns = array(
            '/\[\[(\*|\$|\!)(\s+)(.*)\]\]/smi',
            '/&\s+(\w+)\s+=\s+`(.*)`/imu',
            '/`@\s+(FILE|TEMPLATE|INLINE|CODE)\s+(.*)`/imu',
            '/\$\s+\_\s?modx\s?->\s?/imu',
            '/\[\[~\s?\[\[\*\s?id\]\]\]\]/imu',
            '/\[\[([\*|%|~])\s+(\w+)\]\]/imu',
            '/\[\[\+\+\s+(\w+)\]\]/imu',
        );
        $replacements = array(
            '[[$1$3]]',
            '&$1=`$2`',
            '@$1 $2',
            '$modx->',
            '[[~[[*id]]]]',
            '[[$1$2]]',
            '[[++$1]]',
        );
        $text = preg_replace($patterns, $replacements, $text);

        $text = preg_replace_callback(
            '#`[^`]*@FILE[^`]+`#isu',
            function ($match) {
                return preg_replace('#\s+/\s+#isu', '/', $match[0]);
            },
            $text
        );

        $text = preg_replace_callback(
            '#\{[^\{].*[^\}]+\}#isu',
            function ($match) {
                return preg_replace('/(?<=\$)\s+/isu', '', $match[0]);
            },
            $text
        );

        return $text;
    }

}
