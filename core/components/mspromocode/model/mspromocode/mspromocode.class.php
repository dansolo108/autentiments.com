<?php

class msPromoCode
{
    /* @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var bool $ms24 */
    public $ms24;
    /* @var mspcCouponHandler $coupon */
    public $coupon;
    /* @var mspcActionHandler $action */
    public $action;
    /* @var mspcDiscountHandler $discount */
    public $discount;
    /* @var array $config */
    public $config = array();
    public $initialized = array();
    public $active = false;
    /* @var array $cart */
    public $cart = array();
    protected $error = array();
    protected $warning = array();
    protected $success = array();
    /* @var array $cache */
    public $cache = array(
        'products' => array(
            'discount' => null,
            'bound' => null,
            'price' => null,
        ),
        'categories' => array(
            'discount' => null,
        ),
        'coupons' => array(
            'id' => null,
            'code' => null,
            'discount' => null,
        ),
        'actions' => array(
            'id' => null,
            'coupon' => null,
            'discount' => null,
        ),
        'cart' => array(
            'total' => array(
                'count' => null,
                'price' => null,
            ),
        ),
    );

    /**
     * @param modX  $modx
     * @param array $config
     */
    public function __construct(modX &$modx, array $config = array())
    {
        $this->modx = &$modx;

        $corePath = MODX_CORE_PATH . 'components/mspromocode/';
        $assetsPath = MODX_ASSETS_PATH . 'components/mspromocode/';
        $assetsUrl = MODX_ASSETS_URL . 'components/mspromocode/';

        $this->config = array_merge(array(
            'isAjax' => !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest',
            'assetsUrl' => $assetsUrl,
            'assetsPath' => $assetsPath,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'webconnector' => $assetsUrl . 'web-connector.php',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
        ), $config);

        $ctx = isset($this->config['ctx']) ? $this->config['ctx'] : $this->modx->context->key;

        // $this->modx->log(1, $ctx);

        $this->modx->addPackage('mspromocode', $this->config['modelPath']);
        $this->modx->lexicon->load('mspromocode:default');
        $this->active = $this->modx->getOption('mspromocode_active', $config, false);

        // Загружаем класс ms2
        if (!$this->ms2 = $this->modx->getService('minishop2')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[msPromoCode] Requires installed miniShop2.');

            return false;
        }
        // Загружаем класс maxma
        if (!$this->maxma = $this->modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', [])) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[msPromoCode] Error load maxma.');
            return false;
        }
        $this->ms2->initialize($ctx, array('json_response' => $this->config['isAjax']));
        $this->ms24 = isset($this->ms2->version);
        $this->maxma->ms2 = &$this->ms2;
        if ($ctx != 'mgr') {
            // Пишем корзину в свойство
            $this->cart = $this->getFullCart();
            // $this->modx->log(1, '$this->cart '.print_r($this->cart, 1));
        }

        // Загружаем собственные классы для работы
        $handlers = array(
            array('action', 'mspcActionHandler', 'mspcActionInterface'),
            array('coupon', 'mspcCouponHandler', 'mspcCouponInterface'),
            array('condition', 'mspcConditionHandler', 'mspcConditionInterface'),
            array('discount', 'mspcDiscountHandler', 'mspcDiscountInterface'),
        );
        foreach ($handlers as $handler) {
            require_once dirname(__FILE__) . '/' . strtolower($handler[1]) . '.class.php';
            $this->{$handler[0]} = new $handler[1]($this, $this->config);
            if (!($this->{$handler[0]} instanceof $handler[2]) || $this->{$handler[0]}->initialize($ctx) !== true) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize msPromoCode handler class: "' . $handler[1] . '"');

                return false;
            }
        }

        // Если компонент выключен - удаляем скидку, отменяем купон
        if (empty($this->active)) {
            $this->discount->removeDiscountFromCart();
            $this->coupon->removeCurrentCoupon();
        }

        // Пишем предупреждения/ошибки из сессии во внутренние свойства класса
        $this->success = $_SESSION['mspc']['success'] ?: $this->success;
        $this->warning = $_SESSION['mspc']['warning'] ?: $this->warning;
        $this->error = $_SESSION['mspc']['error'] ?: $this->error;
    }

    /**
     * [initialize description].
     *
     * @param string $ctx    [description]
     * @param array  $sp     [description]
     * @param array  $params [description]
     *
     * @return bool
     */
    public function initialize($ctx = 'web', array $sp = array(), array $params = array())
    {
        $this->config = array_merge($this->config, $sp);
        $this->config['ctx'] = $ctx;
        if (!empty($this->initialized[$ctx])) {
            return true;
        }

        $this->config['frontendJs'] = isset($this->config['frontendJs'])
            ? $this->config['frontendJs'] : (isset($this->config['frontend_js']) ? $this->config['frontend_js']
                : $this->modx->getOption('mspromocode_frontend_js', null, '[[+jsUrl]]web/default.js'));

        // Параметры для JS
        $messages = array();
        $_params = array(
            'sticky' => 'false',
            'form' => 'mspc_form',
            'discount_amount' => 'mspc_discount_amount',
            'field' => 'mspc_field',
            'disfield' => 'mspc_field-disabled',
            'btn' => 'mspc_btn',
            'msg' => 'mspc_msg',
            'price' => 'span.price',
            'old_price' => 'span.old_price',
            'refresh_old_price' => 'true',
        );
        foreach ($_params as $key => $val) {
            $params[$key] = isset($params[$key]) ? $params[$key] : $val;
        }
        $this->config['params'] = $params;

        switch ($ctx) {
            case 'mgr':
                break;

            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    // Проверка на ошибки
                    if ($this->getError()) {
                        $this->discount->removeDiscountFromCart();
                        $this->coupon->removeCurrentCoupon();
                        $messages['error'] = $this->getError();
                    }
                    $this->cleanError();

                    // Рефреш корзины, проверка на сообщения
                    if ($this->coupon->getCurrentCoupon()) {
                        $this->discount->refreshDiscountForCart($this->coupon->current['id']);

                        $messages['warning'] = $this->getWarning();
                        $messages['success'] = $this->getSuccess();
                    } else {
                        $this->discount->removeDiscountFromCart();
                        $this->coupon->removeCurrentCoupon();
                    }
                    $this->cleanWarning();
                    $this->cleanSuccess();

                    // Подключаем JS на фронтенде
                    $data_js = preg_replace(array('/\t{6}/', '/[ ]{24}/'), '', '
                        if (typeof(msPromoCode) == "undefined") {
                            msPromoCode = {};
                        }
                        mspcConfig = {
                            jsUrl: "' . $this->config['jsUrl'] . 'web/",
                            webconnector: "' . $this->config['webconnector'] . '",
                            ctx: "' . $this->config['ctx'] . '",
                            ms24: ' . ($this->ms24 ? '1' : '0') . ',
                        };
                        if (typeof mspc == "undefined") { mspc = {}; }
                        if (typeof mspc.msg == "undefined") {
                            mspc.msg = ' . $this->modx->toJSON($messages) . ';
                        }
                        if (typeof mspc.param == "undefined") {
                            mspc.param = ' . $this->modx->toJSON($params) . ';
                        }
                        mspc.discount_amount = ' . $this->discount->getDiscountAmount() . ';
                        mspc.coupon_description = "' . htmlspecialchars($this->coupon->current['description']) . '";
                    ');
                    $this->modx->regClientScript("<script>\n" . $data_js . "\n</script>", true);

                    if ($frontend_js = trim($this->config['frontendJs'])) {
                        if (preg_match('/\.js/i', $frontend_js)) {
                            $frontend_js = str_replace(array(
                                '[[+assetsUrl]]',
                                '[[+jsUrl]]',
                            ), array($this->config['assetsUrl'], $this->config['jsUrl']), $frontend_js);
                            $this->modx->regClientScript($frontend_js);
                        }
                    }
                    // $this->modx->regClientScript($this->config['jsUrl'].'web/default.js');

                    if ($this->ms24) {
                        $data_js = "
                            miniShop2.Callbacks.add('Cart.change.response.success', 'msPromoCode', function (response) {
                                msPromoCode.Cart.freshen();
                            });
                            miniShop2.Callbacks.add('Cart.change.response.error', 'msPromoCode', function (response) {
                                msPromoCode.Cart.freshen();
                            });
                            miniShop2.Callbacks.add('Cart.remove.response.success', 'msPromoCode', function (response) {
                                msPromoCode.Cart.freshen();
                            });
                            miniShop2.Callbacks.add('Cart.remove.response.error', 'msPromoCode', function (response) {
                                msPromoCode.Cart.freshen();
                            });
                            miniShop2.Callbacks.add('Cart.submit.response.error', 'msPromoCode', function (response) {
                                if(response['data'].length == 0) {
                                    msPromoCode.Coupon.check();
                                }
                            });
                        ";
                    } else {
                        $data_js = "
                            mspcConfig.cloneObj = function(obj) {
                                if (obj == null || typeof(obj) != 'object') { return obj; }
                                var tmp = new obj.constructor();
                                for (var key in obj) {
                                    tmp[key] = mspcConfig.cloneObj(obj[key]);
                                }
                                return tmp;
                            }
                            mspcConfig.ms2CallbacksClone = mspcConfig.cloneObj(miniShop2.Callbacks);
                            mspcConfig.ms2Callbacks = {
                                Cart: {
                                    change: { response: {
                                        success: function (response) {
                                            if (typeof(mspcConfig.ms2CallbacksClone.Cart.change.response.success) == 'function') {
                                                mspcConfig.ms2CallbacksClone.Cart.change.response.success(response);
                                            }
                                            msPromoCode.Cart.freshen();
                                        },
                                        error: function (response) {
                                            if (typeof(mspcConfig.ms2CallbacksClone.Cart.change.response.error) == 'function') {
                                                mspcConfig.ms2CallbacksClone.Cart.change.response.error(response);
                                            }
                                            msPromoCode.Cart.freshen();
                                        },
                                    }},
                                    remove: { response: {
                                        success: function (response) {
                                            if (typeof(mspcConfig.ms2CallbacksClone.Cart.remove.response.success) == 'function') {
                                                mspcConfig.ms2CallbacksClone.Cart.remove.response.success(response);
                                            }
                                            msPromoCode.Cart.freshen();
                                        },
                                        error: function (response) {
                                            if (typeof(mspcConfig.ms2CallbacksClone.Cart.remove.response.error) == 'function') {
                                                mspcConfig.ms2CallbacksClone.Cart.remove.response.error(response);
                                            }
                                            msPromoCode.Cart.freshen();
                                        },
                                    }},
                                },
                                Order: {
                                    submit: { response: {
                                        error: function (response) {
                                            if (typeof(mspcConfig.ms2CallbacksClone.Order.submit.response.error) == 'function') {
                                                mspcConfig.ms2CallbacksClone.Order.submit.response.error(response);
                                            }
                                            if(response['data'].length == 0) {
                                                msPromoCode.Coupon.check();
                                            }
                                        },
                                    }},
                                },
                            };
                            miniShop2.Callbacks.Cart.change.response = mspcConfig.ms2Callbacks.Cart.change.response;
                            miniShop2.Callbacks.Cart.remove.response = mspcConfig.ms2Callbacks.Cart.remove.response;
                            miniShop2.Callbacks.Order.submit.response.error = mspcConfig.ms2Callbacks.Order.submit.response.error;
                        ";
                    }
                    $data_js = preg_replace(array('/\t{6}/', '/[ ]{24}/'), '', $data_js);
                    $this->modx->regClientScript("<script>\n" . $data_js . "\n</script>", true);
                }

                $this->initialized[$ctx] = true;
                break;
        }

        return true;
    }

    /**
     * Метод.
     * Очищает значения от мусора при добавлении/обновлении объектов.
     *
     * @param $key
     * @param $value
     *
     * @return string
     */
    public function sanitize($key, $value)
    {
        $value = is_string($value) ? trim($value) : $value;

        switch (strtolower(trim($key))) {
            case 'discount':
                $value = preg_replace(array('/[^0-9%,\.]/', '/,/'), array('', '.'), $value);
                if (strstr($value, '%')) {
                    $value = trim(str_replace('%', '', $value)) . '%';
                }
                if ((empty($value) && $value != '') || $value == '%') {
                    $value = '0%';
                }
                break;

            case 'count':
                $value = preg_replace(array('/[^0-9]/'), array(''), $value);
                if (empty($value) && $value != '') {
                    $value = '0';
                }
                break;

            case 'begins':
            case 'ends':
                if (empty($value)) {
                    $value = null; // '0000-00-00 00:00:00';
                }
                break;

            case 'unlimited':
            case 'active':
                $value = !empty($value) && $value != 'false';
                break;
        }

        return $value;
    }

    /**
     * Плагин для генерации ref купона при создании юзера.
     *
     * @param $sp
     */
    public function OnUserSave($sp)
    {
        $mode = !empty($sp['mode']) ? $sp['mode'] : '';
        if ($mode == 'new' && $action = $this->modx->getObject('mspcAction', array('ref' => true))) {
            $response = $this->modx->runProcessor('coupon/generate', array(
                'action_id' => $action->id,
                'action_ref' => true,
                'mask' => $this->modx->getOption('mspromocode_regexp_gen_code'),
            ), array('processors_path' => MODX_CORE_PATH . 'components/mspromocode/processors/mgr/'));
        }
    }

    /**
     * Плагин для бекенда.
     * Добавляет вкладку с купонами на страницу товара.
     *
     * @param $sp
     */
    public function onDocFormPrerender($sp)
    {
        if ($this->modx->getOption('mode', $sp) !== 'upd' || !$this->modx->getCount('msProduct', $sp['id'])) {
            return;
        }

        if ($this->modx->getOption('mspromocode_product_tab_active', null, false)) {
            $this->modx->controller->addLexiconTopic('mspromocode:default');
            $this->modx->regClientCSS($this->config['cssUrl'] . 'mgr/main.css');

            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/mspromocode.js');
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/misc/utils.js');
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/misc/mspromocode.combo.js');

            $data_js = preg_replace(array('/\t{3}/', '/[ ]{12}/'), '', '
                msPromoCode.config.connector_url = "' . $this->config['connectorUrl'] . '";
                msPromoCode.config.regexp_gen_code = "' . $this->modx->getOption('mspromocode_regexp_gen_code') . '";
                msPromoCode.resource_id = ' . $sp['id'] . ';
                msPromoCode.resource_type = "product";
            ');
            $this->modx->regClientStartupScript("<script type='text/javascript'>\n" . $data_js . "\n</script>", true);

            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/inject/resources.grid.tab.js');
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/inject/conditions.grid.tab.js');
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/inject/orders.grid.tab.js');
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/widgets/coupons.grid.js');
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/widgets/coupons.windows.js');
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/widgets/coupons.tab.js');
        }
    }

    /**
     * Плагин для бекенда.
     * Добавляет вкладку с купонами в окно информации о заказе.
     *
     * @param $sp
     */
    public function msOnManagerCustomCssJs($sp)
    {
        $page = !empty($sp['page']) ? $sp['page'] : array();
        if ($page != 'orders') {
            return;
        }

        $this->modx->controller->addLexiconTopic('mspromocode:default');
        $this->modx->regClientCSS($this->config['cssUrl'] . 'mgr/main.css');

        $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/mspromocode.js');
        $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/misc/mspromocode.combo.js');

        $data_js = preg_replace(array('/\t{3}/', '/[ ]{12}/'), '', '
            msPromoCode.config.connector_url = "' . $this->config['connectorUrl'] . '";
            msPromoCode.config.regexp_gen_code = "' . $this->modx->getOption('mspromocode_regexp_gen_code') . '";
        ');
        $this->modx->regClientStartupScript("<script>\n" . $data_js . "\n</script>", true);

        $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/inject/order.tab.js');

        // Расширяем форму в списке заказов miniShop2
        if ($this->modx->getOption('mspromocode_ms2_orders_active')) {
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/extends/ms2.orders.form.js');
            $this->modx->regClientStartupScript($this->config['jsUrl'] . 'mgr/extends/ms2.orders.grid.js');
        }
    }

    /**
     * Плагин.
     * Совершает определённое действие при обращении через коннектор.
     *
     * @param array $sp
     */
    public function OnHandleRequest($sp)
    {
        if (empty($_REQUEST['mspc_action']) ||
            ($this->config['isAjax'] && $this->modx->event->name != 'OnHandleRequest') ||
            (!$this->config['isAjax'] && $this->modx->event->name != 'OnLoadWebDocument')) {
            return;
        }

        $action = trim($_REQUEST['mspc_action']);
        $ctx = isset($_REQUEST['ctx']) ? $_REQUEST['ctx'] : $this->modx->context->key;
        if ($ctx != 'web') {
            $this->modx->switchContext($ctx);
        }

        /* @var miniShop2 $ms2 */
        $ms2 = &$this->ms2;
        if (!($ms2 instanceof miniShop2)) {
            @session_write_close();
            exit('Could not initialize miniShop2');
        }

        // Additional loading context lexicon, for switch context on OnHandleRequest
        $lang = $this->modx->getOption('cultureKey');
        $this->modx->lexicon->load($lang . ':minishop2:default');
        $this->modx->lexicon->load($lang . ':mspromocode:default');

        switch ($action) {
            // Возвращает корзины (mspc и ms2), общую скидку, предупреждения и ошибки
            case 'cart/get':
                if ($this->coupon->getCurrentCoupon()) {
                    $resp['success'] = true;
                    $resp['ms2']['cart'] = $this->cart;
                    $resp['ms2']['status'] = $ms2->cart->status();
                    $resp['mspc']['coupon'] = $this->coupon->current['code'];
                    $resp['mspc']['coupon_description'] = '';
                    $resp['mspc']['discount_amount'] = $this->discount->getDiscountAmount();
                    $resp['mspc']['cart'] = $_SESSION['mspc']['cart'];
                    $resp['mspc']['success'] = $this->getSuccess();
                    $resp['mspc']['warning'] = $this->getWarning();
                    $resp['mspc']['error'] = $this->getError();
                    $this->cleanSuccess();
                    $this->cleanWarning();
                    // $this->cleanError();
                } else {
                    $resp['success'] = false;
                }
                break;

            // Возвращает массив с информацией о купоне
            case 'coupon/get':
                if (!$resp['mspc']['coupon'] = trim($_REQUEST['mspc_coupon'])) {
                    $resp['mspc']['coupon'] = $this->coupon->getCurrentCoupon() ? $this->coupon->current['code'] : '';
                }
                if (!empty($resp['mspc']['coupon'])) {
                    $resp['mspc']['coupon'] = $this->coupon->getCouponByCode($resp['mspc']['coupon']);
                }
                break;

            // Устанавливает купон и возвращает результат операции
            case 'coupon/set':
                $resp['success'] = false;
                $resp['mspc']['coupon'] = trim($_REQUEST['mspc_coupon']);
                if($this->modx->getAuthenticatedUser('web') === null){
                    $this->setError('Для использования промо-кода необходимо авторизоваться на сайте');
                    // Проверка на ошибки
                    $resp['mspc']['error'] = $this->getError();
                    $this->cleanError();
                    break;
                }
                if ($coupon = $this->coupon->setCurrentCoupon($resp['mspc']['coupon']) && $this->modx->getAuthenticatedUser('web')) {
                    $resp['success'] = true;
                    $resp['mspc']['coupon'] = $this->coupon->current['code'];
                    $this->discount->setDiscountForCart($this->coupon->current['code']);

                    if ($this->getError()) {
                        $resp['success'] = false;
                        $resp['mspc']['error'] = $this->getError();
                        $this->discount->removeDiscountFromCart();
                    } else {
                        $resp['ms2']['cart'] = $this->cart;
                        $resp['ms2']['status'] = $ms2->cart->status();
                        $resp['mspc']['success'] = $this->getSuccess();
                        $resp['mspc']['warning'] = $this->getWarning();
                        $resp['mspc']['coupon_description'] = '';
                        $resp['mspc']['discount_amount'] = $this->discount->getDiscountAmount();
                        $resp['mspc']['cart'] = $_SESSION['mspc']['cart'];
                    }
                } else {
                    // Проверка на ошибки
                    $resp['mspc']['error'] = $this->getError();
                    $this->cleanError();
                }

                if ($resp['success']) {
                    $resp['mspc']['btn'] = $this->modx->lexicon('mspromocode_btn_remove');
                } else {
                    $resp['mspc']['btn'] = $this->modx->lexicon('mspromocode_btn_apply');
                }
                break;

            // Удаляет применённый купон
            case 'coupon/remove':
                $this->discount->removeDiscountFromCart();
                $this->coupon->removeCurrentCoupon();

                $resp['success'] = true;
                $resp['ms2']['cart'] = $this->cart;
                $resp['ms2']['status'] = $ms2->cart->status();
                $resp['mspc']['success'] = $this->getSuccess();
                $resp['mspc']['discount_amount'] = 0;
                $resp['mspc']['cart'] = array();
                $resp['mspc']['btn'] = $this->modx->lexicon('mspromocode_btn_apply');
                break;

            default:
                $message = ($_REQUEST['mspc_action'] != $action) ? 'ms2_err_register_globals' : 'ms2_err_unknown';
                $resp = $ms2->error($message);
                break;
        }

        if ($this->config['isAjax']) {
            $resp = is_array($resp) ? $this->modx->toJSON($resp) : $resp;
            @session_write_close();
            exit($resp);
        }
    }

    public function OnLoadWebDocument($sp)
    {
        $this->OnHandleRequest($sp);
    }

    /**
     * Плагин.
     * При отправке заказа.
     * Проверяет, действителен ли купон.
     *
     * @param $sp
     */
    public function msOnSubmitOrder($sp)
    {
        $order = $sp['order'] ?: false;

        if (!$this->coupon->getCurrentCoupon() || !is_object($order)) {
            $this->modx->event->output($this->getError());

            return;
        }
    }

    /**
     * Плагин.
     * После создания заказа.
     * Создадим привязку купона к заказу, уменьшим кол-во оставшихся купонов, если они конечны.
     *
     * @param $sp
     */
    public function msOnCreateOrder($sp)
    {
        $msOrder = $sp['msOrder'] ?: false;
        $order = $sp['order'] ?: false;
        $coupon = $this->coupon->getCurrentCoupon();

        if (empty($coupon) || !is_object($msOrder) || !is_object($order)) {
            return;
        }

        if ($coupon) {
            $mspcOrder = $this->modx->newObject('mspcOrder');
            $mspcOrder->fromArray(array(
                'code' => $coupon['code'],
                'createdon' => date('Y-m-d H:i:s'),
                'discount_amount' => $this->discount->getDiscountAmount(),
            ));
            $mspcOrder->addOne($msOrder);

            if ($mspcOrder->save()) {
                $response = $this->invokeEvent('mspcOnBindCouponToOrder', array(
                    'mspc' => $this,
                    'msOrder' => $msOrder,
                    'mspcOrder' => $mspcOrder,
                    'coupon' => $coupon,
                    'discount_amount' => $this->discount->getDiscountAmount(),
                ));
            }
        }

        $this->coupon->removeCurrentCoupon();
    }

    /**
     * Плагин.
     * После добавления в корзину.
     * Проверяем купон на доступность, рефрешим корзину.
     *
     * @param $sp
     */
    public function msOnAddToCart($sp)
    {
        $key = $sp['key'] ?: false;

        if (!$this->coupon->getCurrentCoupon()) {
            // $this->setError($this->modx->lexicon('mspromocode_err_code_invalid'), true);
            $this->discount->removeDiscountFromCart();
            $this->coupon->removeCurrentCoupon();
        } else {
            $this->discount->refreshDiscountForCart($this->coupon->current['code']);

            if ($this->getError()) {
                $this->setError($this->getError(), true, true);
                $this->discount->removeDiscountFromCart();
                $this->coupon->removeCurrentCoupon();
            } elseif ($this->getWarning()) {
                $this->setWarning($this->getWarning(), true, true);
            }
        }
    }

    /**
     * Плагин.
     * При изменении кол-ва в корзине.
     * Проверяем купон на доступность, рефрешим корзину.
     *
     * @param $sp
     */
    public function msOnChangeInCart($sp)
    {
        $this->msOnAddToCart($sp);
    }

    /**
     * Плагин.
     * После удаления из корзины.
     * Проверяем купон на доступность, рефрешим корзину.
     *
     * @param $sp
     */
    public function msOnRemoveFromCart($sp)
    {
        $this->msOnAddToCart($sp);
    }

    /**
     * Плагин.
     * После очистки корзины.
     * Удаляем товары из нашего массива в сессии.
     *
     * @param $sp
     */
    public function msOnEmptyCart($sp)
    {
        $this->discount->removeDiscountFromCart();
    }

    /**
     * Метод.
     * Получает список полей ($select) товара ($id).
     *
     * @param $id
     * @param $select
     * @param $column
     *
     * @return mixed
     */
    public function getProductData($id, $select = '', $column = false)
    {
        if (empty($id) || empty($select)) {
            return;
        }

        $output = false;
        $q = $this->modx->newQuery('msProductData', array('id' => $id));
        if (!empty($select)) {
            $q->select($select);
        }

        if ($q->prepare() && $q->stmt->execute()) {
            if ($column) {
                $output = $q->stmt->fetchColumn();
            } else {
                $output = $q->stmt->fetch(PDO::FETCH_ASSOC);
            }
            // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($rows,1));
        }

        return $output;
    }

    /**
     * Метод.
     * Получает правильно полностью корзину ms2.
     */
    public function getFullCart()
    {
        return $_SESSION['minishop2']['cart'] ?: array();
    }

    /**
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param       $eventName
     * @param array $params
     * @param       $glue
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
     * Возвращает успешные сообщения.
     *
     * @param string $item      Какой элемент вывести: first/last/all.
     * @param mixed  $separator Если передана строка - склеит все элементы массива на выходе с данным разделителем.
     *                          Если false - вернёт массив значений.
     *
     * @return mixed Текст предупреждения или массив значений.
     */
    public function getSuccess($item = 'last', $separator = false)
    {
        if (!$this->success) {
            return;
        }

        if ($item == 'first') {
            return $this->success[0];
        } elseif ($item == 'last') {
            return end($this->success);
        } else {
            return $separator === false ? $this->success : implode($separator, $this->success);
        }
    }

    /**
     * Возвращает предупреждения.
     *
     * @param string $item      Какой элемент вывести: first/last/all.
     * @param mixed  $separator Если передана строка - склеит все элементы массива на выходе с данным разделителем.
     *                          Если false - вернёт массив значений.
     *
     * @return mixed Текст предупреждения или массив значений.
     */
    public function getWarning($item = 'last', $separator = false)
    {
        if (!$this->warning) {
            return;
        }

        if ($item == 'first') {
            return $this->warning[0];
        } elseif ($item == 'last') {
            return end($this->warning);
        } else {
            return $separator === false ? $this->warning : implode($separator, $this->warning);
        }
    }

    /**
     * Возвращает ошибки.
     *
     * @param string $item      Какой элемент вывести: first/last/all.
     * @param mixed  $separator Если передана строка - склеит все элементы массива на выходе с данным разделителем.
     *                          Если false - вернёт массив значений.
     *
     * @return mixed Текст ошибки или массив значений.
     */
    public function getError($item = 'last', $separator = false)
    {
        if (!$this->error) {
            return;
        }

        if ($item == 'first') {
            return $this->error[0];
        } elseif ($item == 'last') {
            return end($this->error);
        } else {
            return $separator === false ? $this->error : implode($separator, $this->error);
        }
    }

    /**
     * Пишет успешное сообщение в свойство класса и, если надо, в сессию.
     *
     * @param string $message
     * @param bool   $session
     * @param bool   $only_session
     *
     * @return bool|void
     */
    public function setSuccess($message = '', $session = false, $only_session = false)
    {
        if (!$message) {
            return;
        }

        if ($session) {
            $_SESSION['mspc']['success'][] = $message;
        }
        if (!$only_session) {
            $this->success[] = $message;
        }

        return true;
    }

    /**
     * Пишет предупреждение в свойство класса и, если надо, в сессию.
     *
     * @param string $message
     * @param bool   $session
     * @param bool   $only_session
     *
     * @return bool|void
     */
    public function setWarning($message = '', $session = false, $only_session = false)
    {
        if (!$message) {
            return;
        }

        if ($session) {
            $_SESSION['mspc']['warning'][] = $message;
        }
        if (!$only_session) {
            $this->warning[] = $message;
        }

        return true;
    }

    /**
     * Пишет ошибку в свойство класса и, если надо, в сессию.
     *
     * @param string $message
     * @param bool   $session
     * @param bool   $only_session
     *
     * @return bool|void
     */
    public function setError($message = '', $session = false, $only_session = false)
    {
        if (!$message) {
            return;
        }

        if ($session) {
            $_SESSION['mspc']['error'][] = $message;
        }
        if (!$only_session) {
            $this->error[] = $message;
        }

        return true;
    }

    /**
     * Очищает успешные сообщения в свойстве класса и, если надо, в сессии.
     *
     * @param bool $session
     * @param bool $only_session
     *
     * @return bool
     */
    public function cleanSuccess($session = true, $only_session = false)
    {
        if ($session) {
            $_SESSION['mspc']['success'] = array();
        }
        if (!$only_session) {
            $this->success = array();
        }

        return true;
    }

    /**
     * Очищает предупреждения в свойстве класса и, если надо, в сессии.
     *
     * @param bool $session
     * @param bool $only_session
     *
     * @return bool
     */
    public function cleanWarning($session = true, $only_session = false)
    {
        if ($session) {
            $_SESSION['mspc']['warning'] = array();
        }
        if (!$only_session) {
            $this->warning = array();
        }

        return true;
    }

    /**
     * Очищает ошибки в свойстве класса и, если надо, в сессии.
     *
     * @param bool $session
     * @param bool $only_session
     *
     * @return bool
     */
    public function cleanError($session = true, $only_session = false)
    {
        if ($session) {
            $_SESSION['mspc']['error'] = array();
        }
        if (!$only_session) {
            $this->error = array();
        }

        return true;
    }

    /**
     * Метод.
     * Генерирует код на основе подобия регулярки.
     * Например: $this->genRegExpString('prefix-/[A-Z]{4-10}-([a-zA-Z0-9]{4})/');.
     *
     * @param $str
     *
     * @return mixed|void
     */
    public function genRegExpString($str)
    {
        if (empty($str)) {
            return;
        }
        $_str = $str;

        $words = array(
            '0-9' => '0123456789',
            'a-z' => 'abcdefghijklmnopqrstuvwxyz',
            'A-Z' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        );

        preg_match_all('/\/((\(?\[[^\]]+\](\{[0-9-]+\})*?\)?[^\[\(]*?)+)\//', $str, $matches);
        // print_r($matches);

        if (is_array($matches[1]) && count($matches[1]) > 0) {
            $_str = preg_replace_callback('/\(?(\[[^\]]+\])(\{[0-9-]+\})\)?/', function ($match) use ($words) {
                // print_r($match);

                $return = preg_replace_callback('/\[([0-9a-zA-Z-]+)\]\{([0-9]+-[0-9]+|[0-9]+)\}/', function ($match) use ($words) {
                    // print_r($match);

                    $return = $match[0];
                    $symbs = $match[1];

                    preg_match_all('/[0-9a-zA-Z]-[0-9a-zA-Z]/', $symbs, $matches);
                    // print_r($matches);

                    if (is_array($matches[0]) && count($matches[0]) > 0) {
                        $strlen = 1;

                        if (!empty($match[2])) {
                            $len = explode('-', $match[2]);
                            $strlen = (count($len) == 1) ? $len[0] : rand($len[0], $len[1]);
                        }

                        // print_r($symbs);
                        for ($i = 0; $i < count($matches[0]); ++$i) {
                            $symbs = str_replace($matches[0][$i], $words[$matches[0][$i]], $symbs);
                        }
                        // print_r($symbs);

                        $maxpos = strlen($symbs) - 1;
                        $pos = 0;
                        $return = '';

                        for ($i = 0; $i < $strlen; ++$i) {
                            $return .= $symbs[rand(0, $maxpos)];
                        }
                    }

                    return $return;
                }, $match[0]);

                return str_replace(array('(', ')'), '', $return);
            }, $matches[1][0]);

            $_str = str_replace($matches[0][0], $_str, $str);
        }

        return $_str;
    }

    /**
     * Возращает последний ключ в массиве.
     *
     * @param $array
     *
     * @return int|string|null
     */
    public function getArrayLastKey($array)
    {
        $key = null;
        if (is_array($array)) {
            end($array);
            $key = key($array);
        }
        return $key;
    }
}