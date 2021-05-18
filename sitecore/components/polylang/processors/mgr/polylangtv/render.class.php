<?php

class PolylangPolylangTvRenderProcessor extends modProcessor
{
    public $classKey = 'PolylangContent';
    public $languageTopics = array('polylang:default');
    /** @var Polylang $polylang */
    public $polylang = null;

    public function initialize()
    {
        $this->polylang = $this->modx->getService('polylang', 'Polylang');
        return parent::initialize();
    }

    public function process()
    {
        $contentId = $this->getProperty('id', 0);
        $resourceId = $this->getProperty('rid', 0);
        $render = $this->getProperty('render', true);
        $showTranslateBtn = $this->modx->getOption('polylang_show_translate_btn');
        $data = array();
        $tvMap = array();
        $hidden = array();
        $tvCounts = array();
        $categories = array();
        $finalCategories = array();
        $rteFields = array();

        $controller = new PolylangFormController($this->modx);
        $this->modx->controller = &$controller;
        $this->modx->getService('smarty', 'smarty.modSmarty');
        $controller->loadTemplatesPath();

        $q = $this->modx->newQuery('modCategory');
        $q->sortby($this->modx->escape('rank'), 'ASC');
        $q->sortby($this->modx->escape('category'), 'ASC');
        $cats = $this->modx->getCollection('modCategory', $q);

        /** @var modCategory $cat */
        foreach ($cats as $cat) {
            $categories[$cat->get('id')] = $cat->toArray();
            $categories[$cat->get('id')]['tvs'] = array();
            $categories[$cat->get('id')]['tvCount'] = 0;
        }

        $categories[0] = array(
            'id' => 0,
            'category' => ucfirst($this->modx->lexicon('uncategorized')),
            'tvs' => array(),
            'tvCount' => 0,
        );

        $content = $this->modx->getObject('PolylangContent', array('id' => $contentId));
        if ($content) {
            $data = $content->toArray();
        } else {
            $content = $this->modx->newObject('PolylangContent');
            $content->set('content_id', $resourceId);
        }
        $cultureKey = $content->get('culture_key');
        $resource = $content->getOne('Resource');

        $q = $this->modx->call('PolylangContent', 'prepareTVQuery', array(&$content));
        $q->leftJoin('modCategory', 'Category');
        $q->leftJoin('PolylangTvTmplvars', 'PolylangTvTmplvars', '`PolylangTvTmplvars`.`tmplvarid` = `modTemplateVar`.`id` AND `PolylangTvTmplvars`.`culture_key` = "' . $cultureKey . '"');
        $q->select($this->modx->getSelectColumns('modTemplateVar', 'modTemplateVar'));
        $q->select($this->modx->getSelectColumns('PolylangTvTmplvars', 'PolylangTvTmplvars', 'pltv_'));
        $q->select($this->modx->getSelectColumns('modCategory', 'Category', 'cat_', array('category')));
        $q->select($this->modx->getSelectColumns('modTemplateVarTemplate', 'tvtpl', '', array('rank')));
        $q->sortby('cat_category,tvtpl.rank,modTemplateVar.rank', 'ASC');
        $tvs = $this->modx->getCollection('modTemplateVar', $q);
        $tvIds = array();
        /** @var modTemplateVar $tv */
        foreach ($tvs as $tv) {
            if (!$tv->checkResourceGroupAccess()) {
                continue;
            }

            if ($tv->get('pltv_values')) {
                $tv->set('elements', $tv->get('pltv_values'));
            }

            if ($tv->get('default_text')) {
                $tv->set('default_text', $tv->get('default_text'));
            }

            $v = '';
            $tv->set('inherited', false);
            $cat = (int)$tv->get('category');

            $tv->_fieldMeta['id']['phptype'] = 'string';
            $tvIds['polylang_' . $tv->get('name')] = $tv->get('id');
            $tv->set('id', 'polylang_' . $tv->get('name'));

            $default = $tv->processBindings($tv->get('default_text'), $resource->get('id'));
            if (strpos($tv->get('default_text'), '@INHERIT') > -1 && (strcmp($default, $tv->get('value')) === 0 || $tv->get('value') === null)) {
                $tv->set('inherited', true);
            }

            if ($tv->get('value') === null) {
                $v = $default;
                $tv->set('value', $v);
            }

            if ($data && isset($data[$tv->get('name')])) {
                $v = $data[$tv->get('name')];
                $tv->set('value', $v);
            }

            // TODO join text editor
            if ($tv->get('type') == 'richtext') {
                $rteFields[] = $tv->get('id');
            }

            $inputForm = $tv->renderInput($resource, array('value' => $v));
            if (empty($inputForm)) continue;

            $tv->set('formElement', $inputForm);
            if ($tv->get('type') != 'hidden') {
                if (!isset($categories[$cat]['tvs']) || !is_array($categories[$cat]['tvs'])) {
                    $categories[$cat]['tvs'] = array();
                    $categories[$cat]['tvCount'] = 0;
                }

                /* add to tv/category map */
                $tvMap[$tv->get('id')] = $tv->category;

                /* add TV to category array */
                $categories[$cat]['tvs'][] = $tv;
                if ($tv->get('type') != 'hidden') {
                    $categories[$cat]['tvCount']++;
                }
            } else {
                $hidden[] = $tv;
            }
        }

        /** @var modCategory $category */
        foreach ($categories as $n => $category) {
            if (is_array($category)) {
                $category['hidden'] = empty($category['tvCount']) ? true : false;
                $ct = isset($category['tvs']) ? count($category['tvs']) : 0;
                if ($ct > 0) {
                    $finalCategories[$category['id']] = $category;
                    $tvCounts[$n] = $ct;
                }
            }
        }

        if ($render) {
            $controller->setPlaceholder('tvIds', $tvIds);
            $controller->setPlaceholder('tvcount', count($tvs));
            $controller->setPlaceholder('categories', $finalCategories);
            $controller->setPlaceholder('showTranslateBtn', $showTranslateBtn);
            // $controller->setPlaceholder('tvCounts', $this->modx->toJSON($tvCounts));
            // $controller->setPlaceholder('tvMap', $this->modx->toJSON($tvMap));
            // $controller->setPlaceholder('hidden', $hidden);
            return $controller->process(array());
        } else {
            return $this->success('', array(
                'categories' => $finalCategories,
                'tvIds' => $tvIds,
                'tvcount' => count($tvs),
            ));
        }
    }


}

return 'PolylangPolylangTvRenderProcessor';