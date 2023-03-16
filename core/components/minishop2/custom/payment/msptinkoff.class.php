<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

if (!class_exists('msPaymentInterface')) {
    require_once dirname(__FILE__, 3) . '/handlers/mspaymenthandler.class.php';
}


class mspTinkoff extends msPaymentHandler implements msPaymentInterface
{

    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var array $config */
    public $config;

    public $namespace = 'msptinkoff';
    public $isNewApi = false;

    public $version = '1.0.12-beta';


    /** @inheritdoc} */
    public function __call($n, array $p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }


    /** @inheritdoc} */
    function __construct(xPDOObject $object, $config = [])
    {
        parent::__construct($object, $config);

        $siteUrl = $this->modx->getOption('site_url');
        $assetsUrl = $this->modx->getOption('minishop2.assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/minishop2/');
        $paymentUrl = $siteUrl . substr($assetsUrl, 1) . 'payment/msptinkoff.php';

        $this->config = array_merge([
            'paymentUrl'  => $paymentUrl,
            'terminalKey' => trim($this->modx->getOption('ms2_payment_tinkoff_terminalKey', null, '', true)),
            'secretKey'   => trim($this->modx->getOption('ms2_payment_tinkoff_secretKey', null, '', true)),
            'failId'      => $this->modx->getOption('ms2_payment_tinkoff_failId', null, '1', true),
            'currency'    => trim($this->modx->getOption('ms2_payment_tinkoff_currency', null, '643', true)),
            'checkoutUrl' => trim($this->modx->getOption('ms2_payment_tinkoff_checkoutUrl', null,
                'https://securepay.tinkoff.ru/rest/', true)),

        ], $config);

        $this->isNewApi = (strpos($this->config['checkoutUrl'], '/v2') != false) ? true : false;
        $this->checkStat();
    }


    public function getOption($key, $config = [], $default = null, $skipEmpty = false)
    {
        return $this->modx->getOption("ms2_payment_tinkoff_{$key}", $config, $default, $skipEmpty);
    }


    /** @inheritdoc} */
    public function send(msOrder $order)
    {
        $link = $this->getPaymentLink($order);
        $this->changeOrderStatus($order,5);
        return $this->success('', ['redirect' => $link]);
    }


    public function getPaymentTax($tax = 'none')
    {
        $tax = $this->getOption('tax', null, $tax, true);
        $tax = mb_strtolower(trim($tax), 'UTF-8');
        switch ($tax) {
            case 'vat0':
            case 'vat10':
            case 'vat18':
            case 'vat20':
            case 'vat110':
            case 'vat118':
            case 'vat120':
                break;
            default:
                $tax = 'none';
                break;
        }

        return $tax;
    }


    public function getPaymentTaxation($taxation = 'osn')
    {
        $taxation = $this->getOption('taxation', null, $taxation, true);
        $taxation = mb_strtolower(trim($taxation), 'UTF-8');
        switch ($taxation) {
            case 'usn_income':
            case 'usn_income_outcome':
            case 'envd':
            case 'esn':
            case 'patent':
                break;
            default:
                $taxation = 'osn';
                break;
        }

        return $taxation;
    }


    protected function getPaymentReceiptFormat(array $params = [], array $form = [])
    {
        return $this->getOption('receipt_format', null);
    }


    public function getProductValue($key = '', array $product = [])
    {
        $value = $this->getOption('receipt_' . $key, null);

        /*******************************************/
        $response = $this->ms2->invokeEvent('mspTinkoffOnGetProductValue', [
            'key'     => $key,
            'product' => $product,
            'value'   => $value,
        ]);
        if (empty($response['success'])) {
            return $value;
        }
        $data = isset($response['data']) ? $response['data'] : [];
        $value = isset($data['value']) ? $data['value'] : null;

        /*******************************************/

        return $value;
    }


    protected function getPaymentReceipt(msOrder $order)
    {
        $order_cart_cost = $order->get('cart_cost');
        $order_delivery_cost = $order->get('delivery_cost');
        $order_cost = $order->get('cost');

        $products = [];
        /** @var msOrderProduct $product */
        foreach ($order->getMany('Products') as $product) {
            $products[] = $product->toArray();
        }

        if (empty($products)) {
            $products = [
                'name'  => $this->modx->getOption('product_name', null, 'Продукт', true),
                'price' => $order_cart_cost,
                'count' => 1,
            ];
        }
        // add delivery
        if (!empty($order_delivery_cost)) {
            $products[] = [
                'name'     => $this->modx->getOption('delivery_name', null, 'Доставка', true),
                'price'    => $order_delivery_cost,
                'count'    => 1,
                'delivery' => 1,
            ];
        }

        $amount = 0;
        foreach ($products as $product) {
            $quantity = round($product['count'], 2);
            $price = round($product['price'], 2);
            $amount += $price * $quantity;
        }

        $diff = $amount - $order_cart_cost - $order_delivery_cost;
        if (abs($diff) >= 0.001) {
            $coff = $diff / $amount;
            foreach ($products as $i => $product) {
                $products[$i]['price'] = $product['price'] - $product['price'] * $coff;
            }
        }

        $tax = $this->getPaymentTax();
        $taxation = $this->getPaymentTaxation();
        $format = $this->getPaymentReceiptFormat();

        $items = [];
        foreach ($products as $product) {
            $quantity = (int)$product['count'];
            $price = (int)($product['price'] * 100);
            $amount = (int)($price * $quantity);
            $name = mb_substr($product['name'], 0, 64, 'UTF-8');

            if (!empty($amount)) {
                $final_amount += $price * $quantity;
                $item = [
                    'Name'     => $name,
                    'Price'    => $price,
                    'Quantity' => $quantity,
                    'Amount'   => $amount,
                    'Tax'      => $tax,
                ];

                // TODO ФФД 1.05
                if (in_array($format, ['1.05', '1.1'])) {
                    if ($payment_mode = $this->getProductValue('payment_mode', $product)) {
                        $item['PaymentMethod'] = mb_substr($payment_mode, 0, 64, 'UTF-8');
                    }
                    if ($payment_subject = $this->getProductValue('payment_subject', $product)) {
                        $item['PaymentObject'] = mb_substr($payment_subject, 0, 64, 'UTF-8');
                    }
                }

                $items[] = $item;
            }
        }
        
        // добавляем оставшуюся разницу к доставке
        if (!empty($final_amount)) {
            $final_diff = ($order_cost * 100) - $final_amount;
            if ($final_diff) {
                $items_last_key = array_key_last($items);
                $items[$items_last_key]['Price'] = $items[$items_last_key]['Price'] + $final_diff;
                $items[$items_last_key]['Amount'] = $items[$items_last_key]['Amount'] + $final_diff;
            }
        }

        $receipt['Items'] = $items;

        if ($profile = $order->getOne('UserProfile')) {
            $email = trim($profile->get('email'));
            $phone = trim($profile->get('phone'));

            $receipt['Email'] = $email;
            if (!empty($phone)) {
                $receipt['Phone'] = $phone;
            }
        }
        $receipt['Taxation'] = $taxation;

        return $receipt;
    }


    protected function getPaymentReceipt_OLD(msOrder $order)
    {
        $tax = $this->getPaymentTax();
        $taxation = $this->getPaymentTaxation();

        $order_amount = 0;
        $receipt = $items = [];
        if ($products = $order->getMany('Products')) {
            /** @var msOrderProduct[] $products */
            foreach ($products as $product) {
                $quantity = intval($product->get('count'));
                $price = intval($product->get('price') * 100);
                $amount = intval($price * $quantity);
                $name = substr($product->get('name'), 0, 64);

                if (!empty($price)) {
                    $items[] = [
                        'Name'     => $name,
                        'Price'    => $price,
                        'Quantity' => $quantity,
                        'Amount'   => $amount,
                        'Tax'      => $tax,
                    ];
                    $order_amount += $amount;
                }
            }
        } else {
            $quantity = 1;
            $price = $amount = intval($order->get('cart_cost') * 100);
            $name = substr($this->getOption('product_name', null, 'Product', true), 0, 64);

            if (!empty($price)) {
                $items[] = [
                    'Name'     => $name,
                    'Price'    => $price,
                    'Quantity' => $quantity,
                    'Amount'   => $amount,
                    'Tax'      => $tax,
                ];
                $order_amount += $amount;
            }
        }

        // add delivery
        $delivery_cost = intval($order->get('delivery_cost') * 100);
        if (!empty($delivery_cost)) {
            $quantity = 1;
            $amount = $price = $delivery_cost;
            $name = substr($this->getOption('delivery_name', null, 'Delivery', true), 0, 64);

            $items[] = [
                'Name'     => $name,
                'Price'    => $price,
                'Quantity' => $quantity,
                'Amount'   => $amount,
                'Tax'      => $tax,
            ];
            $order_amount += $amount;
        }

        // process diff
        $cart_cost = intval($order->get('cart_cost') * 100);
        $diff = $order_amount - $cart_cost - $delivery_cost;
        if (abs($diff) >= 0.001) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2:mspTinkoff] Diff: ' . $diff);
            $items[0]['Price'] = round($items[0]['Price'] + ($diff / $items[0]['Quantity']), 2);
            $items[0]['Amount'] = round($items[0]['Amount'] + $diff, 2);
        }

        $receipt['Items'] = $items;

        if ($profile = $order->getOne('UserProfile')) {
            $email = trim($profile->get('email'));
            $phone = trim($profile->get('phone'));

            $receipt['Email'] = $email;
            if (!empty($phone)) {
                $receipt['Phone'] = $phone;
            }
        }
        $receipt['Taxation'] = $taxation;

        return $receipt;
    }


    public function getRedirectDueDate()
    {
        $RedirectDueDate = false;
        if ($paymentReferenceTerm = trim($this->getOption('paymentReferenceTerm', null, false))) {
            $RedirectDueDate = $this->changeDate(time(), $paymentReferenceTerm);
        }

        return $RedirectDueDate;
    }


    /** @inheritdoc} */
    public function getPaymentLink(msOrder $order)
    {
        $params = [
            'msorder' => $order->get('id'),
        ];
        $context = $order->get('context');
        $failUrl = $this->modx->makeUrl($this->config['failId'], $context, $params, 'full');
        $identifierOrder = $this->getOption('identifierOrder', null, 'id', true);

        $params = [
            'TerminalKey' => $this->config['terminalKey'],
            'OrderId'     => $order->get($identifierOrder),
            'Amount'      => intval($order->get('cost') * 100),
            'CustomerKey' => $order->get('user_id'),
            'Currency'    => $this->config['currency'],
        ];

        $data = [];
        $profile = $order->getOne('UserProfile')->toArray();
        foreach (['email', 'phone'] as $key) {
            $value = substr($profile[$key], 0, 100);
            if (!empty($value)) {
                if (!$this->isNewApi) {
                    $data[] = implode('=', [ucfirst($key), $value]);
                } else {
                    $data[ucfirst($key)] = $value;
                }
            }
        }

        //RedirectDueDate
        if ($RedirectDueDate = $this->getRedirectDueDate()) {
            $params['RedirectDueDate'] = $RedirectDueDate;
        }

        // DATA
        if (!$this->isNewApi) {
            $params['DATA'] = implode('|', $data);

        } else {
            $params['DATA'] = $data;
        }

        // Receipt
        if ($this->isNewApi AND $this->getOption('processReceipt', null, false)) {
            $params['Receipt'] = $this->getPaymentReceipt($order);
        }

        // Token
        $params['Token'] = $this->getToken($params);

        $salt = $order->get('updatedon') . $order->get('cost');
        $response = $this->sendRequest('', $params, $salt);

        if ($this->getOption('showLog', null, false, true)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2:mspTinkoff] Test log.');
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, print_r($params, 1));
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, print_r($response, 1));
        }

        if (is_array($response) AND $response['Success']) {
            return $response['PaymentURL'];
        }

        return $failUrl;
    }


    /** @inheritdoc} */
    public function receive(msOrder $order, $params = [])
    {
        if (!isset($params['TerminalKey'], $params['OrderId'], $params['Token'])
        ) {
            return $this->paymentError('Wrong payment request', $params);
        }
        if ($this->getToken($params) != $params['Token']) {
            return $this->paymentError('Wrong Token', $params);
        }
        if (intval($order->get('cost') * 100) != $params['Amount']) {
            return $this->paymentError('Wrong Amount', $params);
        }

        switch ($params['Status']) {
            case 'CONFIRMED':
                $this->changeOrderStatus($order, $this->modx->getOption('ms2_payment_tinkoff_status_paid', null, 2, true));
                break;
            case 'REJECTED':
                $this->changeOrderStatus($order, $this->modx->getOption('ms2_payment_tinkoff_status_cancel', null, 4, true));
                break;
            case 'DEADLINE_EXPIRED':
                $this->changeOrderStatus($order, $this->modx->getOption('ms2_payment_tinkoff_status_cancel', null, 4, true));
                break;
            case 'CANCELED':
                $this->changeOrderStatus($order, $this->modx->getOption('ms2_payment_tinkoff_status_cancel', null, 4, true));
                break;
            //case 'RECEIPT':
            default:
                break;
        }

        return true;
    }


    protected function changeOrderStatus(msOrder $order, $status)
    {
        if (!$this->ms2) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }

        $this->ms2->changeOrderStatus($order->get('id'), $status);
    }


    /** @inheritdoc} */
    protected function getToken($params = [])
    {
        foreach (['Token', 'DATA', 'Receipt'] as $k) {
            unset($params[$k]);
        }

        foreach ($params as $k => $v) {
            switch ($k) {
                case 'Success':
                    $params[$k] = filter_var($v, FILTER_VALIDATE_BOOLEAN) ? "true" : "false";
                    break;
                default:
                    break;
            }
        }
        $params['Password'] = $this->config['secretKey'];
        ksort($params);
        $token = implode('', array_values($params));
        $token = hash('sha256', $token);

        return $token;
    }


    /** @inheritdoc} */
    public function paymentError($text, $request = [])
    {
        $this->modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2:mspTinkoff] ' . $text);
        $this->modx->log(modX::LOG_LEVEL_ERROR, var_export($request, 1));

        return true;
    }


    /** @inheritdoc} */
    protected function requestCurl($method, array $params = [], $headers = null)
    {

        $url = trim($this->config['checkoutUrl'], '/') . '/' . $method;
        if ($headers == null) {
            $headers = [];
        }

        if (!$this->isNewApi) {
            $headers['Content-type'] = 'application/x-www-form-urlencoded';
            $params = http_build_query($params, '', '&');

        } else {
            $headers['Content-type'] = 'application/json;charset=UTF-8';
            $params = json_encode($params, JSON_UNESCAPED_UNICODE);
        }
        $httpheader = [];
        foreach ($headers as $header => $value) {
            $httpheader[] = "{$header}: {$value}";
        }

        $ch = curl_init();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_URL            => $url,
                CURLOPT_HTTPHEADER     => $httpheader,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_VERBOSE        => 1,
                CURLOPT_POST           => 1,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_HEADER         => 0,
                CURLOPT_POSTFIELDS     => $params,
            ]
        );

        $response = null;
        try {
            $response = curl_exec($ch);

            if ($response) {
                $response = json_decode($response, true);
                if (!$response['Success']) {
                    $this->paymentError($method, $params);
                    $this->paymentError($method, $response);
                }
            } else {
                trigger_error(curl_error($ch));
                $this->paymentError($method, $params);
            }
            curl_close($ch);
        } catch (HttpException $ex) {
            echo $ex;
        }

        return $response;
    }


    /** @inheritdoc} */
    protected function sendRequest($method, array $params = [], $salt = '')
    {
        if (empty($method)) {
            $method = 'Init';
        }

        $cacheKey = 'msptinkoff/' . $method . '/' . sha1(serialize($params) . $salt);
        /** @var modCacheManager $cacheManager */
        $cacheManager = $this->modx->getCacheManager();
        if (!$response = $cacheManager->get($cacheKey)) {
            $response = $this->requestCurl($method, $params);
            if ($response) {
                $cacheManager->set($cacheKey, $response, 3600);
            }
        }

        return $response;
    }


    public function changeDate($date = null, $term = null, $invert = false)
    {
        if (!$date OR !$term) {
            return false;
        }

        $term = strtolower(trim($term));

        $pattern_term_value = $this->getOption('pattern_term_value', null, "/[^0-9]/");
        $pattern_term_unit = $this->getOption('pattern_term_unit', null, "/[^y|m|d|w|h|i]/");
        $term_value = preg_replace($pattern_term_value, '', $term);
        $term_unit = preg_replace($pattern_term_unit, '', $term);

        if (empty($term_value)) {
            $term_value = 0;
        }

        switch ($term_unit) {
            case 'y':
                $interval = "P{$term_value}Y";
                break;
            case 'm':
                $interval = "P{$term_value}M";
                break;
            case 'w':
                $term_value = 7 * $term_value;
                $interval = "P{$term_value}D";
                break;
            case 'd':
                $interval = "P{$term_value}D";
                break;
            case 'h':
                $interval = "PT{$term_value}H";
                break;
            case 'i':
                $interval = "PT{$term_value}M";
                break;
            default:
                return false;
        }

        if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        $date = new DateTime(date('Y-m-d\TH:i:s\Z', $date));
        $interval = new DateInterval($interval);
        if ($invert) {
            $interval->invert = 1;
        }
        $date->add($interval);

        return $date->format('Y-m-d\TH:i:s\Z');
    }


    protected function checkStat()
    {
        $key = strtolower(__CLASS__);
        /** @var modDbRegister $registry */
        $registry = $this->modx->getService('registry', 'registry.modRegistry')->getRegister('user', 'registry.modDbRegister');
        $registry->connect();
        $registry->subscribe('/modstore/' . md5($key));
        if ($res = $registry->read(['poll_limit' => 1, 'remove_read' => false])) {
            return;
        }
        $c = $this->modx->newQuery('transport.modTransportProvider', ['service_url:LIKE' => '%modstore%']);
        $c->select('username,api_key');
        /** @var modRest $rest */
        $rest = $this->modx->getService('modRest', 'rest.modRest', '', [
            'baseUrl'        => 'https://modstore.pro/extras',
            'suppressSuffix' => true,
            'timeout'        => 1,
            'connectTimeout' => 1,
        ]);

        if ($rest) {
            $level = $this->modx->getLogLevel();
            $this->modx->setLogLevel(modX::LOG_LEVEL_FATAL);
            $rest->post('stat', [
                'package'            => $key,
                'version'            => $this->version,
                'keys'               => ($c->prepare() AND $c->stmt->execute()) ? $c->stmt->fetchAll(PDO::FETCH_ASSOC) : [],
                'uuid'               => $this->modx->uuid,
                'database'           => $this->modx->config['dbtype'],
                'revolution_version' => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
                'supports'           => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
                'http_host'          => $this->modx->getOption('http_host'),
                'php_version'        => XPDO_PHP_VERSION,
                'language'           => $this->modx->getOption('manager_language'),
            ]);
            $this->modx->setLogLevel($level);
        }
        $registry->subscribe('/modstore/');
        $registry->send('/modstore/', [md5($key) => true], ['ttl' => 3600 * 24]);
    }

}