<?php
	require_once(MODX_CORE_PATH . 'components/gtranslit/vendor/autoload.php');

	use Dejurin\GoogleTranslateForFree;

	define('GtranslitCachePath', MODX_CORE_PATH . 'components/gtranslit/model/modx/gtranslit/cache.json');

	class gTranslate
	{
		/**
		 * A reference to the modX instance communicating with this service instance.
		 * @var modX
		 */
		public $modx = NULL;
		/**
		 * A collection of options.
		 * @var array
		 */
		public $options = [];

		/**
		 * Constructs a new instance of the modTransliterate class.
		 *
		 * Use modX::getService() to get an instance of the translit service; do not manually construct this class.
		 *
		 * @param modX &$modx    A reference to a modX instance.
		 * @param array $options An array of options for configuring the modTransliterate instance.
		 */
		public function __construct(modX &$modx, array $options = [])
		{
			$this->modx    = &$modx;
			$this->options = $options;
		}

		static public function tr($string = '', $source = 'ru', $target = 'en', $attempts = 5, $cache = TRUE)
		{
			try {
				$st = $source . '-' . $target;
				if (!empty($string)) {
					if (is_string($string)) {
						if ($cache) {
							$Key = md5($string);
							if ($translate = self::getCache($st, $Key)) {
								return $translate;
							}
						}
						$tr        = new GoogleTranslateForFree();
						$translate = $tr->translate($source, $target, $string, $attempts);
						self::setCache($st, $Key, $translate);
						return $translate;

					} elseif (is_array($string)) {
						$translate = [];
						$tr        = new GoogleTranslateForFree();
						foreach ($string as $str) {
							$_translate = NULL;
							$Key        = md5($str);
							if ($cache) {
								$_translate = self::getCache($st, $Key);
							}
							if (!$_translate) {
								$_translate = $tr->translate($source, $target, $str, $attempts);
								self::setCache($st, $Key, $translate);
							}
							$translate[$string] = $_translate;
						}
						return $translate;
					}
				} else {
					return NULL;
				}
			} catch (Exception $e) {
				return $string;
			}
		}

		static public function getCache($st = NULL, $key = NULL)
		{
			$cache = json_decode(file_get_contents(GtranslitCachePath), 1) ?: [];
			if ($st) {
				if (array_key_exists($st, $cache)) {
					if ($key) {
						if (array_key_exists($key, $cache[$st])) {
							return $cache[$st][$key];
						} else {
							return FALSE;
						}
					} else {
						return $cache[$st];
					}
				} else {
					return FALSE;
				}
			} else {
				return $cache;
			}
		}

		static public function setCache($st, $key, $value)
		{
			$cache            = self::getCache();
			$cache[$st][$key] = $value;
			file_put_contents(GtranslitCachePath, json_encode($cache, 256));

		}

		public function rawText($a = '')
		{
			return mb_strtolower(preg_replace('@[^A-zА-я0-9]|[\/_\\\.\,]@u', '', (string)$a));
		}
	}
