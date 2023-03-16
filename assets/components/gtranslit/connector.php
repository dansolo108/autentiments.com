<?php
	// Ищем MODX
	// Подключаем MODX
	if (!isset($modx)) {
		$base_path = __DIR__;
		// Ищем MODX
		// устанавливаем лимит на всякий случай
		$_i = 0;
		while (!file_exists($base_path . "/config.core.php") and $_i < 50) {
			$base_path = dirname($base_path);
		}
		if (file_exists($base_path . "/index.php")) {
			require_once $base_path . "/config.core.php";
			require_once MODX_CORE_PATH . "config/" . MODX_CONFIG_KEY . ".inc.php";
			require_once MODX_CONNECTORS_PATH . "index.php";
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

	// Указываем путь к папке с процессорами и заставляем MODX работать
	$modx->addPackage("gtranslit", MODX_CORE_PATH . "components/gtranslit/model/");
	$modx->lexicon->load("gtranslit:default");
	$modx->request->handleRequest(
		[
			"processors_path" => MODX_CORE_PATH . "components/gtranslit/processors/",
			"location"        => "",
		]
	);