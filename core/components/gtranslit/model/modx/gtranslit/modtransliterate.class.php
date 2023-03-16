<?php
	require_once MODX_CORE_PATH . 'components/gtranslit/model/gTranslate.php';

	/**
	 * A transliteration service implementation class for MODx Revolution.
	 *
	 * @package    modx
	 * @subpackage gTranslit
	 */
	class modTransliterate extends gTranslate
	{

		/**
		 * Translate a string using a named transliteration table.
		 *
		 * @param string $string The string to transliterate.
		 *
		 * @return string The translated string.
		 */
		public function translate($string = '')
		{
			try {
				if (empty($string)) {
					return '';
				}

				$extension = '';
//			$seed = 'gtraslit';
				$source = $this->modx->getOption('gtranslit.lang.from', NULL, 'ru');
				$target = $this->modx->getOption('gtranslit.lang.to', NULL, 'en');
				$attempts = $this->options['attempts'] ?? 5;
				$c = !(bool)$this->modx->getOption('gtranslit.disable_cache', NULL, FALSE);
				if (preg_match('#\.[0-9a-z]+$#i', $string, $matches)) {
					$extension = $matches[0];
					$string = preg_replace('#' . $extension . '$#', '', $string);
				}
				$trim = $this->modx->getOption('friendly_alias_trim_chars', NULL, '/.-_', TRUE);
				$string = str_replace(str_split($trim), ' ', $string);
				if (!mb_detect_encoding($string, 'ASCII', TRUE)) {
					$string = $this->tr($string, $source, $target, $attempts, $c);
					if (!$string or empty($string)) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, "can`t translate:'$string' [$source=>$target]", '', __METHOD__, __FILE__, __LINE__);
						$string = $this->CommonTranslate($string, $source) . strtolower($extension);
					}
				} else {
					$string = $this->CommonTranslate($string, $source);
				}
				return $string . strtolower($extension);
			} catch (Exception $e) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage(), '', __METHOD__ ?: __FUNCTION__, __FILE__, __LINE__);
				return $this->CommonTranslate($string, $source) . strtolower($extension);
			}
		}

		public function commonTranslate($string, $table)
		{
			$ret = $string;
			$filePath = __DIR__ . '/tables/' . $table . '.php';
			if (is_file($filePath)) {
				$table = include $filePath;
				if (is_array($table) && !empty($table)) {
					$ret = strtr($string, $table);
				}
			}
			return $ret;
		}
	}
