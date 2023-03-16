<?php
require_once MODX_CORE_PATH . 'components/gtranslit/model/gTranslate.php';
	/** @var array $scriptProperties */
	/** @var modX $modx */
	$options  = array_merge($scriptProperties, $_REQUEST);
	$string   = $modx->getOption('string'    , $options   , '');
	$source   = $modx->getOption('source'    , $options   , 'ru');
	$target   = $modx->getOption('target'    , $options   , 'en');
	$attempts = $modx->getOption('attempts'  , $options   , 5);
	return gTranslate::tr($string, $source, $target, $attempts);