<?php
/**
 * @var modX $modx
 * @var Polylang $polylang
 * @var PolylangTools $tools
 * @var array $scriptProperties
 */

$polylang = $modx->getService('polylang', 'Polylang');
$tools = $polylang->getTools();

switch ($modx->event->name) {
    case 'OnMODXInit':
        $polylang->extendTvModel();
        $polylang->extendMs2OptionModel();
        $tools->prepareModelContent();
        if ($modx->context->get('key') == 'mgr') return;
        $tools->setDefaultSettings();
        if ($language = $tools->detectLanguage(true)) {
            $tools->setLanguage($language);
        }
        $tools->setDefaultCurrencyForLanguage($language);
        break;
    case 'OnTVFormPrerender':
        $polylang->loadControllerTVJsCss($modx->controller);
        break;
    case 'msOnManagerCustomCssJs':
        if ($page != 'settings') return;
        $polylang->loadControllerMs2OptionJsCss($modx->controller);
        break;
    case 'OnDocFormPrerender':
        if (!$id || !$polylang->isWorkingTemplates($modx->controller->resourceArray['template'])) return;
        $polylang->loadControllerJsCss($id, $modx->controller);
        break;
    case 'OnHandleRequest':
        if ($modx->context->get('key') == 'mgr') return;
        if ($language = $tools->detectLanguage()) {
            $visitorlanguage = $tools->detectVisitorLanguage();
            $forcelanguage = $tools->getForceLanguage();
            $tools->setLanguage($language);
            if ($forcelanguage && !$forcelanguage->isCurrent()) {
                $modx->setPlaceholder('polylang_redirect_language', $forcelanguage);
            } else if ($visitorlanguage && !$visitorlanguage->isCurrent()) {
                $modx->setPlaceholder('polylang_redirect_language', $visitorlanguage);
            }
        }
        break;
    case 'OnPageNotFound':
        $containerSuffix = $modx->getOption('container_suffix');
        $alias = $modx->context->getOption('request_param_alias', 'q');
        if (empty($containerSuffix) && isset($_REQUEST[$alias])) {
            $request = trim($_REQUEST[$alias]);
            if ($keys = $tools->getLanguageKeys()) {
                if (in_array($request, $keys)) {
                    $pageId = $modx->getOption('site_start');
                    $modx->sendForward($pageId);
                }
            }
        }
        break;
    case 'OnLoadWebDocument':
        $redirectLanguage = $modx->getPlaceholder('polylang_redirect_language');
        if ($redirectLanguage) {
            $redirectLanguage->redirect($modx->resource);
        }
        if (!$modx->getPlaceholder('polylang_site')) return;
        $options = array(
            'skipTVs' => true,
            'class' => get_class($modx->resource),
            'content_id' => $modx->resource->get('id'),
        );
        $placeholders = array();
        $modx->resource->set('polylang_override', 1);
        $tools->overrideResourceTvs($modx->resource);
        $tools->prepareResourceData(function ($key, $value, $context) use (&$modx, &$placeholders) {
            $original = $modx->resource->get($key);
            $placeholders[$key] = $value;
            $placeholders['polylang_original_' . $key] = $original;
            $modx->resource->set($key, $value);
            $modx->resource->set('polylang_original_' . $key, $original);
        }, $options);
        break;
    case 'msOnBeforeCreateOrder':
        /** @var msOrder $msOrder */
        $properties = $msOrder->get('properties');
        if (!is_array($properties)) $properties = array();
        $cultureKey = $modx->getOption('cultureKey');
        $defaultLanguage = $tools->getDefaultLanguage();
        $properties[$polylang->getNamespace()] = array(
            'cultureKey' => $cultureKey,
            'pagetitle' => array()
        );
        foreach ($msOrder->Products as $product) {
            $pagetitle = $product->get('name');
            if ($cultureKey != $defaultLanguage) {
                $content = $modx->getObject('PolylangContent', array(
                    '`culture_key`' => $cultureKey,
                    '`content_id`' => $product->get('product_id'),
                ));
                if ($content) {
                    $pagetitle = $content->get('pagetitle');
                }
            }
            $properties[$polylang->getNamespace()]['pagetitle'][$product->get('product_id')] = $pagetitle;
        }
        $msOrder->set('properties', $properties);
        break;
    case 'mse2OnBeforeSearchIndex':
        $tools->putSearchIndex($mSearch2, $resource);
        break;
    case 'OnBeforeEmptyTrash':
        if (!empty($scriptProperties['ids']) && is_array($scriptProperties['ids'])) {
            $tools->removeResourceLanguages($scriptProperties['ids']);
        }
        break;

}
return;