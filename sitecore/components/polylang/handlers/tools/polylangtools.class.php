<?php

class PolylangTools
{

    /** @var modX $modx */
    public $modx = null;
    /** @var Polylang $polylang */
    public $polylang = null;
    /** @var PolylangDbHelper $dbHelper */
    public $dbHelper = null;
    /** @var pdoTools $pdoTools */
    public $pdoTools = null;
    /** @var miniShop2 $ms2 */
    public $ms2 = null;
    /** @var string $sessionKey */
    public $sessionKey = 'polylang';
    /** @var array $config */
    public $config = array();
    /** @var bool $debug */
    public $debug = false;
    /** @var array $optionTypes */
    protected $optionTypes = array();

    public function __construct(Polylang &$polylang, $config = array())
    {
        $this->polylang = &$polylang;
        $this->modx = &$polylang->modx;
        $this->config = array_merge($this->config, $config);
        $this->debug = $this->modx->getOption('polylang_debug', null, false, true);

    }

    /**
     * @param string $class
     * @param string $name
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function addField($class, $name, array $data, array $options = array())
    {
        $result = false;
        if ($this->hasField($class, $name)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error add new field '{$name}' in '{$class}'. Field already exists");
            return false;
        }
        $meta = $this->modx->getOption('meta', $data, array(), true);
        if ($meta) {
            $result = $this->getDbHelper()->addField($class, $name, $meta, $options);
            if ($result) {
                /** @var  PolylangField $field */
                $field = $this->modx->newObject('PolylangField');
                $field->fromArray($data);
                $field->set('name', $name);
                $field->set('class_name', $class);
                if (!$result = $field->save()) {
                    $this->getDbHelper()->removeField($class, $name, $options);
                }
            }
        }
        return $result;
    }

    /**
     * @param string $class
     * @param string $name
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function alterField($class, $name, array $data, array $options = array())
    {
        $result = false;
        if ($field = $this->getField($name, $class)) {
            $meta = $this->modx->getOption('meta', $data, array(), true);
            $name = $this->modx->getOption('name', $data, $name);
            if (empty($meta)) {
                $meta = $field->get('meta');
            }
            if ($meta != $field->get('meta')) {
                if (!$this->getDbHelper()->alterField($class, $name, $meta, $options)) {
                    return false;
                }
            }
            if ($name !== $field->get('name')) {
                if (!$this->getDbHelper()->renameField($class, $field->get('name'), $name, $options)) {
                    return false;
                }
            }
            $field->fromArray($data);
            $result = $field->save();
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error alter field. Field '{$name}' not found  in '{$class}'");
        }
        return $result;
    }

    /**
     * @param string $class
     * @param string $name
     * @return bool
     */
    public function removeField($class, $name)
    {
        $result = false;
        if ($field = $this->getField($class, $name)) {
            if ($result = $field->remove()) {
                $result = $this->getDbHelper()->removeField($class, $name);
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error remove field. Field '{$name}' not found  in '{$class}'");
        }
        return $result;
    }

    /**
     * @param string $class
     * @param string $name
     * @return object|null
     */
    public function getField($class, $name)
    {
        return $this->modx->getObject('PolylangField', array('name' => $name, 'class_name' => $class));
    }

    /**
     * @param string $class
     * @param string $name
     * @return bool
     */
    public function hasField($class, $name)
    {
        return $this->modx->getCount('PolylangField', array('name' => $name, 'class_name' => $class)) ? true : false;
    }

    /**
     * @param array $exclude
     * @return array
     */
    public function getContentClasses(array $exclude = array())
    {
        $result = array();
        $list = $this->modx->getOption('polylang_content_classes');
        $list = $this->fromJSON($list, array());
        if ($list) {
            foreach ($list as $key => $target) {
                if (!in_array($key, $exclude)) {
                    $result[strtolower($key)] = $key;
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getContentClassesTarget()
    {
        $list = $this->modx->getOption('polylang_content_classes');
        return $this->fromJSON($list, array());
    }

    /**
     * @param modResource $resource
     * @param string $cultureKey
     * @return array
     */
    public function render(modResource $resource, $cultureKey)
    {
        $result = array();
        if ($resource) {
            foreach ($this->getContentClassesTarget() as $class => $target) {
                if ($resource instanceof $target) {
                    if ($items = $this->renderFields($resource, $class, $cultureKey)) {
                        $class = strtolower($class);
                        $result[] = array(
                            'title' => $this->modx->lexicon('polylang_content_tab_' . $class),
                            'id' => 'polylang-window-polylangcontent-tab-' . $class,
                            'layout' => 'form',
                            'items' => $items,
                            'forceLayout' => true,
                            'deferredRender' => false,
                        );
                    }
                }
            }
            if ($resource instanceof msProduct) {
                $product = $this->modx->call('PolylangProduct', 'getInstance', array(
                    &$this->modx,
                    $resource->get('id'),
                    $cultureKey,
                ));

                if ($product->getOptionKeys()) {
                    $result[] = array(
                        'title' => $this->modx->lexicon('polylang_content_tab_options'),
                        'items' => array(
                            'xtype' => 'modx-vtabs',
                            'autoTabs' => true,
                            'plain' => true,
                            'id' => 'polylang-window-polylangcontent-options-vtabs',
                            'items' => $this->renderOptions($product),
                        ),
                        // 'forceLayout' => true,
                        // 'deferredRender' => false,
                    );
                }
            }
        }
        return $result;
    }

    /**
     * @param PolylangProduct $product
     * @return array
     */
    public function renderOptions(PolylangProduct $product)
    {
        $result = array();
        $categories = array();
        $options = $product->getOptionFields();
        $index = -1;
        foreach ($options as $option) {
            $categoryId = $option['category'];
            $field = $this->prepareOptionField($option);
            if (empty($field)) continue;
            if (!isset($categories[$categoryId])) {
                $categories[$categoryId] = $index++;
                $result[$index] = array(
                    'title' => $option['category_name'] ? $option['category_name'] : $this->modx->lexicon('uncategorized'),
                    'id' => 'polylang-window-polylangcontent-options-tab-' . $categoryId,
                    'layout' => 'form',
                    'labelAlign' => 'top',
                    'category' => $categoryId,
                    'items' => array(),
                    'forceLayout' => true,
                    'deferredRender' => false,
                );
            }
            $result[$index]['items'][] = $field;
        }
        return $result;
    }

    /**
     * @param array $option
     * @return array
     */
    public function prepareOptionField(array $option)
    {
        $field = $option;
        $name = 'polylangproduct_' . $option['key'];
        if (!empty($option['ext_field'])) {
            if (is_string($option['ext_field'])) {
                $option['ext_field'] = str_replace(array('\'', 'xtype'), array('"', '"xtype"'), $option['ext_field']);
                $option['ext_field'] = $this->fromJSON($option['ext_field']);
            }
            $field = $option['ext_field'];
        }
        return array_merge($field, array(
            'name' => $name,
            'fieldKey' => $option['key'],
            'key' => "{$option['key']}",
            'source' => "PolylangProductOption",
            'translate' => "{$option['polylang_translate']}",
            'value' => $option['value'] ? $option['value'] : '',
            'fieldLabel' => $option['caption'] ? $option['caption'] : $this->modx->lexicon('ms2_product_' . $option['key']),
            'description' => "[[+{$option['key']}]]",
            'enableKeyEvents' => true,
            'category' => $option['category'],
            'category_name' => $option['category_name'],
            'allowBlank' => $option['required'],
            'anchor' => '100%',
            'msgTarget' => 'under',
        ));
    }

    /**
     * @param modResource $resource
     * @param string $className
     * @param string $cultureKey
     * @return array
     */
    public function renderFields($resource, $className, $cultureKey)
    {
        $fields = array();
        $classKey = 'PolylangField';
        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey));
        $q->where(array(
            'active' => 1,
            'class_name' => $className
        ));
        $q->sortby('rank', 'ASC');
        if ($q->prepare() && $q->stmt->execute()) {
            while ($data = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $fields[] = $this->renderField($resource, $className, $data, $cultureKey);
            }
        }
        return $fields;
    }

    /**
     * @param modResource $resource
     * @param string $className
     * @param array $data
     * @param string $cultureKey
     * @return array
     */
    public function renderField($resource, $className, array $data, $cultureKey)
    {
        $name = strtolower($className);
        $xtype = $data['xtype'];

        $editorHeight = $this->modx->getOption('polylang_editor_height', null, 350, true);
        $useCodeEditor = $this->modx->getOption('polylang_use_code_editor', null, 1, true);
        $whichElementEditor = $this->modx->getOption('which_element_editor', null, '', true);
        $useResourceEditorStatus = $this->modx->getOption('polylang_use_resource_editor_status', null, 1, true);

        $field = array(
            'xtype' => $xtype,
            'id' => "polylang-{$name}-{$data['name']}",
            'key' => "{$data['name']}",
            'source' => "{$className}",
            'translate' => "{$data['translate']}",
            'fieldLabel' => $data['caption'],
            'description' => $data['description'],
            'name' => "{$name}_{$data['name']}",
            'culture_key' => "{$cultureKey}",
            'allowBlank' => $data['required'] ? false : true,
            'anchor' => '100%',
        );
        if ($xtype == 'polylang-text-editor' &&
            $useCodeEditor &&
            $whichElementEditor &&
            $useResourceEditorStatus &&
            !$resource->get('richtext')
        ) {
            $field['xtype'] = 'polylang-code-editor';
        }

        if (!empty($data['code'])) {
            $code = $this->fromJSON($data['code'], array());
            $field = array_merge($field, $code);
        }
        return $field;
    }

    /**
     * @param msOption $option
     * @return null|PolylangOptionType
     */
    public function getOptionType($option)
    {
        $className = $this->loadOptionType($option->get('type'));

        if (class_exists($className)) {
            return new $className($option);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                'Could not initialize Polylang option type class: "' . $className . '"');

            return null;
        }
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function loadOptionType($type)
    {
        $this->modx->loadClass('PolylangProductOption', $this->config['modelPath'] . 'polylang/');
        $typePath = $this->config['corePath'] . 'processors/mgr/ms2/option/types/' . $type . '.class.php';
        if (array_key_exists($typePath, $this->optionTypes)) {
            $className = $this->optionTypes[$typePath];
        } else {
            /** @noinspection PhpIncludeInspection */
            $className = include_once $typePath;
            // handle already included classes
            if ($className == 1) {
                $o = array();
                $s = explode(' ', str_replace(array('_', '-'), ' ', $type));
                foreach ($s as $k) {
                    $o[] = ucfirst($k);
                }
                $className = 'Polylang' . implode('', $o) . 'Type';
            }
            $this->optionTypes[$typePath] = $className;
        }
        return $className;
    }

    public function setDefaultSettings()
    {
        if (!$this->modx->getOption('polylang_base_host', null, '', true)) {
            $this->modx->setOption("polylang_base_host", MODX_HTTP_HOST);
        }
        if (!$this->modx->getOption('polylang_default_language', null, '', true)) {
            $defaultLanguage = $this->modx->getOption('cultureKey');
            $this->modx->setOption("polylang_default_language", $defaultLanguage);
        }
        if (!$this->modx->getOption('polylang_default_site_url', null, '', true)) {
            $this->modx->setOption("polylang_default_site_url", MODX_SITE_URL);
        }
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        $defaultLanguage = $this->modx->getOption('cultureKey');
        return $this->modx->getOption('polylang_default_language', null, $defaultLanguage, true);
    }

    /**
     * @param bool $force
     * @return PolylangLanguage|null
     */
    public function detectLanguage($force = false)
    {
        $currentLanguage = $this->modx->getOption('cultureKey');
        if ($force || (defined(MODX_API_MODE) && MODX_API_MODE)) {
            $sessionKey = $this->getSessionVarKey('language');
            $savedLanguage = false;
            if (isset($_SESSION[$sessionKey])) {
                $savedLanguage = $_SESSION[$sessionKey];
            } else if (isset($_COOKIE[$sessionKey])) {
                $savedLanguage = $_COOKIE[$sessionKey];
            }

            if ($this->debug) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[detectLanguage] Current language={$currentLanguage}");
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[detectLanguage] Saved language={$savedLanguage}");
            }

            if ($savedLanguage) {
                return $this->modx->getObject('PolylangLanguage', array(
                    'active' => 1,
                    'culture_key' => $savedLanguage
                ));
            }
        } else {

            $schema = $_SERVER['HTTPS'] ? 'https://' : 'http://';
            $url = $schema . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];
            $defaultLanguage = $this->modx->getOption('polylang_default_language');

            if ($this->debug) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[detectLanguage] URL={$url}");
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[detectLanguage] Default language={$defaultLanguage}");
            }
            $this->modx->setOption('cultureKey', $defaultLanguage);
            if ($languages = $this->modx->getCollection('PolylangLanguage', array('culture_key:!=' => $defaultLanguage))) {
                /** @var PolylangLanguage[] $languages */
                $containerSuffix = $this->modx->getOption('container_suffix');
                foreach ($languages as $language) {
                    $siteUrl = $language->getSiteUrl();
                    if (empty($containerSuffix)) {
                        $siteUrl = preg_replace("#/$#", "", $siteUrl);
                    }
                    if (strpos($url, $siteUrl) === 0) {
                        if (!$language->get('active')) {
                            $this->modx->setOption('site_url', $this->modx->getOption("polylang_default_site_url"));
                            $this->modx->sendErrorPage();
                            return null;
                        }
                        return $language;
                    }
                }
                if ($this->isAjaxRequest()) {
                    $defaultLanguage = $currentLanguage;
                }
                return $this->modx->getObject('PolylangLanguage', array('culture_key' => $defaultLanguage));
            }
        }
        return null;
    }

    /**
     * @param PolylangLanguage $language
     */
    public function setLanguage($language)
    {
        $siteUrl = $language->getSiteUrl();
        $cultureKey = $language->get('culture_key');
        $containerSuffix = $this->modx->getOption('container_suffix');
        $scheme = $this->modx->getOption('link_tag_scheme');
        if ($scheme == '-1') {
            $this->modx->setOption('link_tag_scheme', 'abs');
        }
        if (empty($containerSuffix)) {
            //  $siteUrl = preg_replace("#/$#", "", $siteUrl);
        }
        if ($this->debug) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[setLanguage] Culture key={$cultureKey}");
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[setLanguage] Site URL={$siteUrl}");
        }

        $locale = $this->country2locale($cultureKey);
        $this->modx->setOption('site_url', $siteUrl);
        $this->modx->setOption('base_url', $language->getBaseUrl());
        $this->modx->setPlaceholder('+site_url', $siteUrl);
        $this->modx->setPlaceholder('polylang_locale', $locale);
        $this->modx->setOption('locale', $locale . '.utf8');
        setlocale(LC_ALL, $locale . '.utf8');
        $this->modx->cultureKey = $cultureKey;
        $this->modx->setOption('cultureKey', $cultureKey);
        $this->modx->setPlaceholder('+cultureKey', $cultureKey);
        $this->modx->setPlaceholder('polylang_site', !$language->isDefault());
        $this->reloadLexicons($cultureKey);
        $sessionKey = $this->getSessionVarKey('language');
        $_SESSION[$sessionKey] = isset($_SESSION[$sessionKey]) ? $_SESSION[$sessionKey] : array();
        $_SESSION[$sessionKey] = $cultureKey;
        setcookie($sessionKey, $cultureKey, time() + 31556926, '/');
        $cacheKey = $this->modx->getOption('cache_resource_key', null, 'resource');
        $this->modx->setOption('cache_resource_key', $cacheKey . '/' . $cultureKey);
        if (!empty($_SESSION['togglePolylangLanguage'])) {
            unset($_SESSION['togglePolylangLanguage']);
            $this->invokeEvent('OnTogglePolylangLanguage', array(
                'tools' => $this,
                'language' => $language,
            ));
        }
    }

    /**
     * @param PolylangLanguage|null $language
     */
    public function setDefaultCurrencyForLanguage($language = null)
    {
        if ($this->modx->getOption('polylang_set_currency_for_language')) {
            if ($this->hasAddition('msmulticurrency')) {
                if (!$language) {
                    $language = $this->detectLanguage();
                }
                if ($language && $language->get('currency_id')) {
                    $this->modx->setOption('msmulticurrency.selected_currency_default', $language->get('currency_id'));
                }
            }
        }
    }

    /**
     * @param string $cultureKey
     */
    public function reloadLexicons($cultureKey)
    {
        $default = array('polylang:default', 'polylang:site');
        if ($this->hasAddition('minishop2')) {
            $default = array_merge($default, array('minishop2:default', 'minishop2:product', 'minishop2:cart'));
        }
        $lexicons = $this->modx->getOption('polylang_reload_lexicon', null, '', true);
        $lexicons = $this->explodeAndClean($lexicons);
        $lexicons = array_merge($default, $lexicons);
        foreach ($lexicons as $lexicon) {
            $this->modx->lexicon->load("{$cultureKey}:{$lexicon}");
        }
    }

    /**
     * @return PolylangLanguage|null
     */
    public function detectVisitorLanguage()
    {
        $classKey = 'PolylangLanguage';
        $sessionKey = $this->getSessionVarKey('language');
        $defaultLanguage = $this->modx->getOption('cultureKey');
        $detect = $this->modx->getOption('polylang_detect_visitor_language', null, false);
        $polylangDefaultLanguage = $this->modx->getOption('polylang_default_language', null, $defaultLanguage, true);
        $visitorDefaultLanguage = $this->modx->getOption('polylang_visitor_default_language', null, $polylangDefaultLanguage, true);

        if ($detect && !isset($_COOKIE[$sessionKey])) {
            $languages = $this->getVisitorLanguages();
            if (!isset($languages[$visitorDefaultLanguage])) {
                $languages[] = $visitorDefaultLanguage;
            }
            $q = $this->modx->newQuery($classKey);
            $q->where(array(
                'active' => 1,
                'culture_key:IN' => $languages
            ));
            $languages = "'" . implode("','", $languages) . "'";
            $q->sortby("FIELD({$classKey}.culture_key, {$languages})");
            return $this->modx->getObject($classKey, $q);
        }
        return null;
    }

    /**
     * @return PolylangLanguage|null
     */
    public function getForceLanguage()
    {
        $classKey = 'PolylangLanguage';
        $sessionKey = $this->getSessionVarKey('language');
        $forceLanguage = $this->modx->getOption('polylang_force_language', null, false);

        if ($forceLanguage && !isset($_COOKIE[$sessionKey])) {
            $q = $this->modx->newQuery($classKey);
            $q->where(array(
                'active' => 1,
                'culture_key:=' => $forceLanguage
            ));
            return $this->modx->getObject($classKey, $q);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getVisitorLanguages()
    {
        $result = array();
        if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $tmp = array_combine($list[1], $list[2]);
                foreach ($tmp as $key => $v) {
                    $key = strtok($key, '-');
                    if (!isset($result[$key])) {
                        $result[$key] = $v ? $v : 1;
                    }
                }
                arsort($result, SORT_NUMERIC);
            }
        }
        return array_keys($result);
    }

    /**
     * @return bool
     */
    public function isCurrentDefaultLanguage()
    {
        return $this->modx->getOption('cultureKey') == $this->modx->getOption('polylang_default_language');
    }

    /**
     * @return bool
     */
    public function isAjaxRequest()
    {
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param bool $onlyActive
     * @param array $options
     *
     * @return array
     */
    public function getLanguageKeys($onlyActive = true, array $options = array())
    {
        $languageGroup = $this->modx->getOption('languageGroup', $options);
        $cacheKey = $this->getCacheKey('getLanguageKeys' . $onlyActive . $languageGroup);
        $result = $this->modx->cacheManager->get($cacheKey);
        if (!is_array($result)) {
            $result = array();
            $classKey = 'PolylangLanguage';
            $q = $this->modx->newQuery($classKey);
            $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('culture_key')));
            if ($onlyActive) {
                $q->where(array('`active`' => 1));
            }
            if ($languageGroup) {
                $languageGroup = $this->explodeAndClean($languageGroup);
                $q->where(array('`group`:IN' => $languageGroup));
            }
            $q->sortby('`rank_translation`', 'ASC');
            if ($q->prepare() && $q->stmt->execute()) {
                $result = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            $this->modx->cacheManager->set($cacheKey, $result, $this->config['cacheTime']);
        }
        return $result;
    }

    /**
     * @param bool $onlyActive
     * @param array $options
     *
     * @return array
     */
    public function getLanguageUrls($onlyActive = true, array $options = array())
    {
        $withSlash = $this->modx->getOption('withSlash', $options, true);
        $languageGroup = $this->modx->getOption('languageGroup', $options);
        $cacheKey = $this->getCacheKey('getLanguageUrls' . $onlyActive . $languageGroup . $withSlash);
        $result = $this->modx->cacheManager->get($cacheKey);
        if (!is_array($result)) {
            $result = array();
            $classKey = 'PolylangLanguage';
            $q = $this->modx->newQuery($classKey);
            if ($onlyActive) {
                $q->where(array('`active`' => 1));
            }
            if ($languageGroup) {
                $languageGroup = $this->explodeAndClean($languageGroup);
                $q->where(array('`group`:IN' => $languageGroup));
            }
            $q->sortby('rank');

            /** @var  PolylangLanguage [] $languages */
            $languages = $this->modx->getCollection($classKey, $q);
            if ($languages) {
                foreach ($languages as $language) {
                    $result[$language->getSiteUrl($withSlash)] = $language->get('culture_key');
                }
            }
            $this->modx->cacheManager->set($cacheKey, $result, $this->config['cacheTime']);
        }
        return $result;
    }

    /**
     * @param int $resourceId
     * @param bool $onlyActive
     * @return array
     */
    public function getResourceLanguageKeys($resourceId, $onlyActive = true)
    {
        $cacheKey = $this->getCacheKey('getResourceLanguageKeys' . $resourceId . $onlyActive);
        $result = $this->modx->cacheManager->get($cacheKey);
        if (!is_array($result)) {
            $result = array();
            $classKey = 'PolylangContent';
            $q = $this->modx->newQuery($classKey);
            $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('culture_key')));
            $q->where(array('content_id' => $resourceId));
            if ($onlyActive) {
                $q->leftJoin('PolylangLanguage', 'Language', '`Language`.`culture_key` = `PolylangContent`.`culture_key`');
                $q->where(array(
                    '`active`' => 1,
                    '`Language`.`active`' => 1,

                ));
            }
            if ($q->prepare() && $q->stmt->execute()) {
                $result = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            $this->modx->cacheManager->set($cacheKey, $result, $this->config['cacheTime']);
        }
        return $result;
    }

    /**
     * @param mSearch2 $mSearch2
     * @param modResource $resource
     */
    public function putSearchIndex(mSearch2 &$mSearch2, modResource &$resource)
    {
        if (
            $mSearch2->fields &&
            $this->modx->getOption('polylang_mse2_index')
        ) {
            foreach ($this->getContentClassesTarget() as $className => $target) {
                if ($resource instanceof $target) {
                    if ($this->isSubclassContent($className)) {
                        $this->modx->call($className, 'putSearchIndex', array(&$this->modx, &$mSearch2, &$resource));
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "[putSearchIndex] Class '{$className}' is not extend 'PolylangContentMain'!");
                    }
                }
            }
        }
    }

    /**
     * @param string $className
     * @return bool
     */
    public function isSubclassContent($className)
    {
        try {
            $this->modx->loadClass($className);
            $class = new ReflectionClass($className);
            return $class->isSubclassOf('PolylangContentMain');
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
            return false;
        }
    }

    /**
     * @param $var
     * @return string
     */
    public function getSessionVarKey($var)
    {
        $ctx = $this->modx->context->get('key');
        if ($ctx == 'mgr') $ctx = 'web';
        return "{$this->sessionKey}:{$ctx}:{$var}";
    }


    /**
     * @param modResource $resource
     * @param array $options
     */
    public function overrideResourceTvs(modResource &$resource, array $options = array())
    {
        $cultureKey = $this->modx->getOption('cultureKey', $options, '', true);
        $content = $this->modx->getObject('PolylangContent', array(
            '`culture_key`' => $cultureKey,
            '`content_id`' => $resource->get('id'),
        ));
        if (!$content) return;
        if ($tvs = $content->getTVKeys()) {
            foreach ($tvs as $key) {
                $value = $content->get($key);
                if (!empty($value)) {
                    $tv = $resource->get($key);
                    if (is_array($tv)) {
                        $resource->set($tv[0], array(
                            $tv[0],
                            $value,
                            $tv[2],
                            $tv[3],
                            $tv[4],
                        ));
                    } else if (is_string($tv)) {
                        $resource->set($key, $value);
                    }
                    // $this->modx->setPlaceholder($key, $value);
                }
            }
        }
    }

    /**
     * @param callable $callback
     * @param array $options
     */
    public function prepareResourceData(callable $callback, array $options = array())
    {
        $class = $this->modx->getOption('class', $options);
        $class = str_replace('_mysql', '', $class);
        $contentId = $this->modx->getOption('content_id', $options);
        $tvPrefix = $this->modx->getOption('tvPrefix', $options, '', true);
        $skipTVs = $this->modx->getOption('skipTVs', $options, false, true);
        $cultureKey = $this->modx->getOption('cultureKey', $options, '', true);
        $includeTVs = $this->modx->getOption('includeTVs', $options, '', true);
        $skipEmptyValue = $this->modx->getOption('polylang_skip_empty_value', $options, true, true);
        if ($includeTVs && is_string($includeTVs)) {
            $includeTVs = $this->explodeAndClean($includeTVs, ',');
        }
        try {
            $class = new ReflectionClass($class);
            foreach ($this->getContentClassesTarget() as $className => $target) {
                if ($target == 'msProduct' && !$this->hasAddition('minishop2')) continue;
                if (!class_exists($target)) {
                    $this->modx->loadClass($target);
                }
                if ($class->getName() == $target || $class->isSubclassOf($target)) {
                    $tvs = array();
                    $exclude = array('id', 'content_id', 'culture_key');
                    $q = $this->modx->newQuery($className);
                    $q->where(array(
                        '`culture_key`' => $cultureKey,
                        '`content_id`' => $contentId,
                    ));
                    if ($className == 'PolylangContent') {
                        $exclude[] = 'active';
                        $q->where(array(
                            '`active`' => 1,
                        ));
                    }
                    /** @var xPDOObject $object */
                    $object = $this->modx->getObject($className, $q);
                    if ($object) {
                        $data = $object->toArray();
                        $status = $this->getStatusFields($className);
                        if ($className == 'PolylangContent') {
                            $tvs = $object->getTVKeys();
                            if ($skipTVs) {
                                $exclude = array_merge($exclude, $tvs);
                            }
                        }
                        foreach ($data as $key => $value) {
                            if (
                                in_array($key, $exclude) ||
                                ($status && isset($status[$key]) && !$status[$key])
                            ) {
                                continue;
                            }
                            if ($tvs && $includeTVs) {
                                if (in_array($key, $includeTVs)) {
                                    $key = $tvPrefix . $key;
                                }
                            }
                            if (!empty($value) || (empty($value) && !$skipEmptyValue)) {
                                $callback($key, $value, $this);
                            }
                        }
                    } else if ($className == 'PolylangContent') {
                        return;
                    }
                }
            }
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
        }
    }

    public function prepareModelContent()
    {
        $disabledFields = $this->getDisabledContentFields();
        $this->modx->getFieldMeta('PolylangContent');
        if ($disabledFields && !empty($this->modx->map['PolylangContent'])) {
            foreach ($disabledFields as $field) {
                unset($this->modx->map['PolylangContent']['fields'][$field]);
                unset($this->modx->map['PolylangContent']['fieldMeta'][$field]);
            }
        }
    }

    /**
     * @param array $ids
     */
    public function removeResourceLanguages(array $ids)
    {
        if ($ids) {
            $classes = $this->getContentClasses();
            if ($classes) {
                foreach ($classes as $class) {
                    $list = $this->modx->getCollection($class, array('content_id:IN' => $ids));
                    if ($list) {
                        foreach ($list as $item) {
                            $item->remove();
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getDisabledContentFields()
    {
        $cacheKey = $this->getCacheKey('getDisabledFields_PolylangContent');
        $result = $this->modx->cacheManager->get($cacheKey);
        if (!is_array($result)) {
            $result = array();
            $classKey = 'PolylangField';
            $q = $this->modx->newQuery($classKey);
            $q->where(array(
                '`active`' => 0,
                '`class_name`' => 'PolylangContent',
            ));
            $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('name')));
            if ($q->prepare() && $q->stmt->execute()) {
                $result = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            $this->modx->cacheManager->set($cacheKey, $result, $this->config['cacheTime']);
        }

        return $result;
    }


    /**
     * @param string $class
     * @return array
     */
    public function getStatusFields($class)
    {
        $cacheKey = $this->getCacheKey('field' . $class);
        $result = $this->modx->cacheManager->get($cacheKey);
        if (!is_array($result)) {
            $result = array();
            $classKey = 'PolylangField';
            $q = $this->modx->newQuery($classKey);
            $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('name', 'active')));
            $q->where(array(
                '`class_name`' => $class,
            ));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $result[$item['name']] = $item['active'];
                }
                $this->modx->cacheManager->set($cacheKey, $result, $this->config['cacheTime']);
            }

        }
        return $result;
    }

    /**
     * @param array $config
     * @return pdoTools|null
     */
    public function getPdoTools($config = array())
    {
        if (!$this->hasAddition('pdotools')) return null;
        if (class_exists('pdoFetch') && (!isset($this->pdoTools) || !is_object($this->pdoTools))) {
            $this->pdoTools = $this->modx->getService('pdoFetch');
            $this->pdoTools->setConfig($config);
        }
        return empty($this->pdoTools) ? null : $this->pdoTools;


    }

    /**
     * @param array $config
     * @return null|PolylangDbHelper
     */
    public function getDbHelper($config = array())
    {
        if (!$this->dbHelper || !is_object($this->dbHelper)) {
            if ($dbHelperClass = $this->modx->loadClass('tools.' . $this->config['dbHelperHandler'], $this->config['handlersPath'], true, true)) {
                $config = array_merge($this->config, $config);
                $this->dbHelper = new $dbHelperClass($this->modx, $config);
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not load DbHelper class from');
            }
        }
        return $this->dbHelper;
    }


    /**
     * @param string $ctx
     * @param array $config
     * @return  miniShop2|null
     */
    public function getMs2($ctx = '', $config = array())
    {
        if (!$this->hasAddition('minishop2')) return null;
        $ctx = $ctx ? $ctx : $this->modx->context->key;
        if (class_exists('miniShop2') && (!isset($this->ms2) || !is_object($this->ms2))) {
            $this->ms2 = $this->modx->getService('miniShop2');
            $this->ms2->initialize($ctx, $config);
        }

        return empty($this->ms2) ? null : $this->ms2;
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $namespace
     * @param bool $clearCache
     * @return bool
     */
    public function setOption($key, $value, $namespace = '', $clearCache = false)
    {
        if (empty(trim($key))) return false;

        $namespace = $namespace ? $namespace : $this->polylang->getNamespace();
        $key = $namespace . '_' . $key;

        if (!$setting = $this->modx->getObject('modSystemSetting', $key)) {
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('namespace', $namespace);
        }

        $val = is_array($value) ? $this->modx->toJSON($value) : $value;
        $setting->set('value', $val);

        if ($setting->save()) {
            $this->modx->setOption($key, $value);
            if ($clearCache) {
                $this->modx->cacheManager->refresh(array('system_settings' => array()));
            }
            return true;
        }
        return false;
    }


    /**
     * @param $str
     * @param string $default
     * @param bool $skipEmpty
     * @return mixed|string
     */
    public function fromJSON($str, $default = '', $skipEmpty = true)
    {
        $val = $this->modx->fromJSON($str);
        if (($val === '' || $val === null) && $skipEmpty) {
            $val = $default;
        }
        return $val;
    }

    /**
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param $eventName
     * @param array $params
     * @param $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = array(), $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }

        $response = $this->modx->invokeEvent($eventName, $params);
        if (is_array($response) && count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }

        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) && is_array($this->modx->event->returnedValues)) {
            $params = array_merge($params, $this->modx->event->returnedValues);
        }

        return array(
            'success' => empty($message),
            'message' => $message,
            'data' => $params,
        );
    }

    /**
     * @param array|string $options
     * @return string
     */
    public function getCacheKey($options)
    {
        return $this->polylang->getNamespace() . DIRECTORY_SEPARATOR . sha1(is_array($options) ? serialize($options) : $options);
    }

    /**
     * Sanitize the specified path
     *
     * @param string $path The path to clean
     * @return string The sanitized path
     */
    public function normalizePath($path)
    {
        $path = str_replace('./', '/', $path);
        return preg_replace(array("/\.*[\/|\\\]/i", "/[\/|\\\]+/i"), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $path);
    }

    /**
     * @param string $str
     * @return mixed
     */
    public function unquote($str)
    {
        return str_replace(array("'", '"'), '', trim($str));
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @return array
     */
    public function explodeAndClean($str, $delimiter = ',')
    {
        $array = explode($delimiter, $str);
        $array = array_map('trim', $array);
        $array = array_keys(array_flip($array));
        $array = array_filter($array);

        return $array;
    }


    /**
     * @param $array
     * @param string $delimiter
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);
        $array = array_keys(array_flip($array));
        $array = array_filter($array);
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @param array $array
     * @return array
     */
    public function cleanArray(array $array = array())
    {
        $array = array_map('trim', $array);
        $array = array_filter($array);
        $array = array_keys(array_flip($array));

        return $array;
    }

    /**
     * @param $needle
     * @param array $array
     * @param bool $all
     * @return array
     */
    public function removeArrayByValue($needle, $array = array(), $all = true)
    {
        if (!$all) {
            if (FALSE !== $key = array_search($needle, $array)) unset($array[$key]);
            return $array;
        }
        foreach (array_keys($array, $needle) as $key) {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * @param array $arr
     * @return string|null
     */
    public function getArrayFirstKey(array $arr)
    {
        $keys = array_keys($arr);
        return empty($keys) ? null : $keys[0];
    }

    /**
     * @param string $path
     * @param bool $normalize
     * @return mixed|string
     */
    public function preparePath($path = '', $normalize = false)
    {
        $path = str_replace(array(
            '{base_path]',
            '{core_path}',
            '{assets_path}',
            '{assets_url}',
            '{mgr_path}',
            '{+core_path}',
            '{+assets_path}',
            '{+assets_url}',
        ), array(
            $this->modx->getOption('base_path', null, MODX_BASE_PATH),
            $this->modx->getOption('core_path', null, MODX_CORE_PATH),
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH),
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL),
            $this->modx->getOption('mgr_path', null, MODX_MANAGER_PATH),
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/polylang/',
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/polylang/',
            $this->modx->getOption('assets_url', null, MODX_ASSETS_PATH) . 'components/polylang/',
        ), $path);
        return $normalize ? $this->normalizePath($path) : $path;
    }

    /**
     * @param string $addition
     * @return bool
     */
    public function hasAddition($addition = '')
    {
        $addition = strtolower($addition);
        return file_exists(MODX_CORE_PATH . 'components/' . $addition . '/model/' . $addition . '/');
    }

    /**
     * @param int $number
     *
     * @return float
     */
    public function formatNumber($number = 0, $ceil = false)
    {
        $number = str_replace(',', '.', $number);
        $number = (float)$number;

        if ($ceil) {
            $number = ceil($number / 10) * 10;
        }

        return round($number, 3);
    }


    /**
     * @param string|array $message
     * @param int $level
     */
    public function log($message, $level = modX::LOG_LEVEL_ERROR)
    {
        if (is_array($message)) {
            $message = print_r($message, 1);
        }
        $curLevel = $this->modx->getLogLevel();
        $this->modx->setLogLevel($level);
        $this->modx->log($level, $message);
        $this->modx->setLogLevel($curLevel);
    }

    /**
     * @param string|array $message
     */
    public function debug($message)
    {

        if (is_array($message)) {
            $message = print_r($message, 1);
        }
        $this->log($message, modX::LOG_LEVEL_DEBUG);
    }

    /**
     * @param string $code
     * @return string
     */
    public function country2locale($code)
    {
        # http://wiki.openstreetmap.org/wiki/Nominatim/Country_Codes
        $arr = array(
            'ad' => 'ca',
            'ae' => 'ar',
            'af' => 'fa,ps',
            'ag' => 'en',
            'ai' => 'en',
            'al' => 'sq',
            'am' => 'hy',
            'an' => 'nl,en',
            'ao' => 'pt',
            'aq' => 'en',
            'ar' => 'es',
            'as' => 'en,sm',
            'at' => 'de',
            'au' => 'en',
            'aw' => 'nl,pap',
            'ax' => 'sv',
            'az' => 'az',
            'ba' => 'bs,hr,sr',
            'bb' => 'en',
            'bd' => 'bn',
            'be' => 'nl,fr,de',
            'bf' => 'fr',
            'bg' => 'bg',
            'bh' => 'ar',
            'bi' => 'fr',
            'bj' => 'fr',
            'bl' => 'fr',
            'bm' => 'en',
            'bn' => 'ms',
            'bo' => 'es,qu,ay',
            'br' => 'pt',
            'bq' => 'nl,en',
            'bs' => 'en',
            'bt' => 'dz',
            'bv' => 'no',
            'bw' => 'en,tn',
            'by' => 'be,ru',
            'bz' => 'en',
            'ca' => 'en,fr',
            'cc' => 'en',
            'cd' => 'fr',
            'cf' => 'fr',
            'cg' => 'fr',
            'ch' => 'de,fr,it,rm',
            'ci' => 'fr',
            'ck' => 'en,rar',
            'cl' => 'es',
            'cm' => 'fr,en',
            'cn' => 'zh',
            'co' => 'es',
            'cr' => 'es',
            'cu' => 'es',
            'cv' => 'pt',
            'cw' => 'nl',
            'cx' => 'en',
            'cy' => 'el,tr',
            'cz' => 'cs',
            'de' => 'de',
            'dj' => 'fr,ar,so',
            'dk' => 'da',
            'dm' => 'en',
            'do' => 'es',
            'dz' => 'ar',
            'ec' => 'es',
            'ee' => 'et',
            'eg' => 'ar',
            'eh' => 'ar,es,fr',
            'er' => 'ti,ar,en',
            'es' => 'es,ast,ca,eu,gl',
            'et' => 'am,om',
            'fi' => 'fi,sv,se',
            'fj' => 'en',
            'fk' => 'en',
            'fm' => 'en',
            'fo' => 'fo',
            'fr' => 'fr',
            'ga' => 'fr',
            'gb' => 'en,ga,cy,gd,kw',
            'gd' => 'en',
            'ge' => 'ka',
            'gf' => 'fr',
            'gg' => 'en',
            'gh' => 'en',
            'gi' => 'en',
            'gl' => 'kl,da',
            'gm' => 'en',
            'gn' => 'fr',
            'gp' => 'fr',
            'gq' => 'es,fr,pt',
            'gr' => 'el',
            'gs' => 'en',
            'gt' => 'es',
            'gu' => 'en,ch',
            'gw' => 'pt',
            'gy' => 'en',
            'hk' => 'zh,en',
            'hm' => 'en',
            'hn' => 'es',
            'hr' => 'hr',
            'ht' => 'fr,ht',
            'hu' => 'hu',
            'id' => 'id',
            'ie' => 'en,ga',
            'il' => 'he',
            'im' => 'en',
            'in' => 'hi,en',
            'io' => 'en',
            'iq' => 'ar,ku',
            'ir' => 'fa',
            'is' => 'is',
            'it' => 'it,de,fr',
            'je' => 'en',
            'jm' => 'en',
            'jo' => 'ar',
            'jp' => 'ja',
            'ke' => 'sw,en',
            'kg' => 'ky,ru',
            'kh' => 'km',
            'ki' => 'en',
            'km' => 'ar,fr',
            'kn' => 'en',
            'kp' => 'ko',
            'kr' => 'ko,en',
            'kw' => 'ar',
            'ky' => 'en',
            'kz' => 'kk,ru',
            'la' => 'lo',
            'lb' => 'ar,fr',
            'lc' => 'en',
            'li' => 'de',
            'lk' => 'si,ta',
            'lr' => 'en',
            'ls' => 'en,st',
            'lt' => 'lt',
            'lu' => 'lb,fr,de',
            'lv' => 'lv',
            'ly' => 'ar',
            'ma' => 'ar',
            'mc' => 'fr',
            'md' => 'ru,uk,ro',
            'me' => 'srp,sq,bs,hr,sr',
            'mf' => 'fr',
            'mg' => 'mg,fr',
            'mh' => 'en,mh',
            'mk' => 'mk',
            'ml' => 'fr',
            'mm' => 'my',
            'mn' => 'mn',
            'mo' => 'zh,en,pt',
            'mp' => 'ch',
            'mq' => 'fr',
            'mr' => 'ar,fr',
            'ms' => 'en',
            'mt' => 'mt,en',
            'mu' => 'mfe,fr,en',
            'mv' => 'dv',
            'mw' => 'en,ny',
            'mx' => 'es',
            'my' => 'ms,zh,en',
            'mz' => 'pt',
            'na' => 'en,sf,de',
            'nc' => 'fr',
            'ne' => 'fr',
            'nf' => 'en,pih',
            'ng' => 'en',
            'ni' => 'es',
            'nl' => 'nl',
            'no' => 'nb,nn,no,se',
            'np' => 'ne',
            'nr' => 'na,en',
            'nu' => 'niu,en',
            'nz' => 'en,mi',
            'om' => 'ar',
            'pa' => 'es',
            'pe' => 'es',
            'pf' => 'fr',
            'pg' => 'en,tpi,ho',
            'ph' => 'en,tl',
            'pk' => 'en,ur',
            'pl' => 'pl',
            'pm' => 'fr',
            'pn' => 'en,pih',
            'pr' => 'es,en',
            'ps' => 'ar,he',
            'pt' => 'pt',
            'pw' => 'en,pau,ja,sov,tox',
            'py' => 'es,gn',
            'qa' => 'ar',
            're' => 'fr',
            'ro' => 'ro',
            'rs' => 'sr',
            'ru' => 'ru',
            'rw' => 'rw,fr,en',
            'sa' => 'ar',
            'sb' => 'en',
            'sc' => 'fr,en,crs',
            'sd' => 'ar,en',
            'se' => 'sv',
            'sg' => 'en,ms,zh,ta',
            'sh' => 'en',
            'si' => 'sl',
            'sj' => 'no',
            'sk' => 'sk',
            'sl' => 'en',
            'sm' => 'it',
            'sn' => 'fr',
            'so' => 'so,ar',
            'sr' => 'nl',
            'st' => 'pt',
            'ss' => 'en',
            'sv' => 'es',
            'sx' => 'nl,en',
            'sy' => 'ar',
            'sz' => 'en,ss',
            'tc' => 'en',
            'td' => 'fr,ar',
            'tf' => 'fr',
            'tg' => 'fr',
            'th' => 'th',
            'tj' => 'tg,ru',
            'tk' => 'tkl,en,sm',
            'tl' => 'pt,tet',
            'tm' => 'tk',
            'tn' => 'ar',
            'to' => 'en',
            'tr' => 'tr',
            'tt' => 'en',
            'tv' => 'en',
            'tw' => 'zh',
            'tz' => 'sw,en',
            'ua' => 'uk',
            'ug' => 'en,sw',
            'um' => 'en',
            'us' => 'en,es',
            'uy' => 'es',
            'uz' => 'uz,kaa',
            'va' => 'it',
            'vc' => 'en',
            've' => 'es',
            'vg' => 'en',
            'vi' => 'en',
            'vn' => 'vi',
            'vu' => 'bi,en,fr',
            'wf' => 'fr',
            'ws' => 'sm,en',
            'ye' => 'ar',
            'yt' => 'fr',
            'za' => 'zu,xh,af,st,tn,en',
            'zm' => 'en',
            'zw' => 'en,sn,nd'
        );
        #----
        $code = strtolower($code);
        if ($code == 'eu') {
            return 'en_GB';
        } elseif ($code == 'ap') { # Asia Pacific
            return 'en_US';
        } elseif ($code == 'cs') {
            return 'sr_RS';
        }
        #----
        if ($code == 'uk') {
            $code = 'gb';
        }
        if (array_key_exists($code, $arr)) {
            if (strpos($arr[$code], ',') !== false) {
                $new = explode(',', $arr[$code]);
                $loc = array();
                foreach ($new as $key => $val) {
                    $loc[] = $val . '_' . strtoupper($code);
                }
                return implode(',', $loc); # string; comma-separated values 'en_GB,ga_GB,cy_GB,gd_GB,kw_GB'
            } else {
                return $arr[$code] . '_' . strtoupper($code); # string 'en_US'
            }
        }
        return 'en_US';
    }
}
