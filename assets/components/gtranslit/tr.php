<?php
	if (!isset($modx)) {
		$base_path = __DIR__;
		// Ищем MODX
		$_i = 0;
		while (!file_exists($base_path . '/config.core.php') and $_i < 50) {
			$base_path = dirname($base_path);
			$_i++;
		}
		if (file_exists($base_path . '/index.php')) {
			ini_set('display_errors', 1);
			ini_set("max_execution_time", 50);
			define('MODX_API_MODE', TRUE);
			require $base_path . '/index.php';
		} else {
			die("modx not found");
		}
	}
	if (!isset($modx)) {
		die("modx not found");
	}
	$string   = trim(strip_tags($modx->getOption('string', $_REQUEST, '')));
	$source   = trim(strip_tags($modx->getOption('source', $_REQUEST, 'ru')));
	$target   = trim(strip_tags($modx->getOption('target', $_REQUEST, 'en')));
	$attempts = trim(strip_tags($modx->getOption('attempts', $_REQUEST, 5)));
	$cache    = (bool)$modx->getOption('cache', $_REQUEST, TRUE);
	if ($string and $source and $target) {
		error_reporting(0);
		require_once MODX_CORE_PATH . 'components/gtranslit/model/gTranslate.php';
		die(gTranslate::tr($string, $source, $target, $attempts, $cache));
	}