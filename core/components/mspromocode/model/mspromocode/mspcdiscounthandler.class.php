<?php

interface mspcDiscountInterface
{
    public function initialize($ctx = 'web');

    public function getDiscountAmount();

    public function setDiscountForCart($coupon_id);

    public function setDiscountForProduct($coupon_id, $key, $p);

    public function removeDiscountFromCart();

    public function refreshDiscountForCart($coupon_id);

    public function getDiscount($id, $product_id = 0);

    public function getDiscountCategories($ids = array(), $coupon_id);

    public function getDiscountPrice(array $product, array $cart, $key);
}

class mspcDiscountHandler implements mspcDiscountInterface
{
    /* @var modX $modx */
    public $modx;
    /* @var msPromoCode $mspc */
    public $mspc;
    /* @var array $current */
    public $current = array();
    /* @var float $cart_total_cost */
    public $cart_total_cost = 0;

    /**
     * @param msPromoCode $mspc   [description]
     * @param array       $config [description]
     */
    public function __construct(msPromoCode &$mspc, array $config = array())
    {
        $this->mspc = &$mspc;
        $this->modx = &$mspc->modx;

        // Выгружаем из сессии массив со скидками на товары
        $this->current = $_SESSION['mspc']['discount_amount'] ?: array();
    }

    /** @inheritdoc} */
    public function initialize($ctx = 'web')
    {
        //$this->mspc->setError('mspcDiscountHandler', true); // отладка подключения

        // if ($this->mspc->coupon->getCurrentCoupon()) {
        //     // $this->current = array();

        //     $this->refreshDiscountForCart($this->mspc->coupon->current['id']);
        // } else {
        //     $this->removeDiscountFromCart();
        //     $this->mspc->coupon->removeCurrentCoupon();
        // }

        return true;
    }

    /**
     * Получает общую скидку на корзину.
     * @return int
     */
    public function getDiscountAmount()
    {
        $amount = 0;

        if (!empty($this->current)) {
            foreach ($this->current as $key => $item) {
                if (isset($item['discount']) && isset($item['count'])) {
                    $amount += $item['discount'] * $item['count'];
                }
            }
        }

        $response = $this->mspc->invokeEvent('mspcOnGetDiscountAmount', array(
            'mspc' => $this->mspc,
            'amount' => $amount,
        ));
        if ($response['success'] && !empty($response['data']['amount'])) {
            $amount = $response['data']['amount'];
        }

        return $amount;
    }

    /**
     * Устанавливает скидку для корзины.
     *
     * @param int  $coupon_id [description]
     * @param bool $refresh   [description]
     *
     * @return bool
     */
    public function setDiscountForCart($coupon, $refresh = false)
    {
        if ($coupon) {
            $this->mspc->cleanSuccess();
            $this->mspc->cleanWarning();
            $this->mspc->cleanError();
            $this->mspc->setSuccess($this->modx->lexicon('mspromocode_ok_code_apply'), true);
            $this->current = $_SESSION['mspc']['discount_amount'] = array();
            $calculated = $this->mspc->maxma->calculatePurchase($coupon);
//            $this->modx->log(1,'setDiscountForCart response '.var_export($calculated,1));
            $i = 0;
//            $this->modx->log(1,'setDiscountForCart old cart '.var_export($this->mspc->cart,1));
            foreach ($this->mspc->cart as $key => &$product) {
                $calcDiscount = $calculated['calculationResult']['rows'][$i];
                if($calcDiscount['id'] !== $key)
                    continue;
                $discount = $calcDiscount['discounts']['promocode'] / $product['count'];
//                $this->modx->log(1,'setDiscountForCart discount '.var_export($discount,1));
//                $this->modx->log(1,'setDiscountForCart qty '.var_export($product['count'],1));

                // Пишем в старую корзину, чтобы потом всё вернуть при отвязке купона
                $_SESSION['mspc']['cart'][$key] = $product;
                $product['options']['old_price'] = $product['old_price'] = max($product['price'],$product['old_price']);
                $product['price'] = $product['price'] - $discount;
//                $this->modx->log(1,'setDiscountForCart price after discount '.var_export($product['price'],1));
//                $this->modx->log(1,'setDiscountForCart old_price after discount '.var_export($product['old_price'],1));

                //$this->setDiscountForProduct($coupon_id, $key, $product, $refresh);
                $this->current[$key] = $_SESSION['mspc']['discount_amount'][$key] = array(
                    'discount' => $discount,
                    'count' => $product['count'],
                );
                $i++;
            }
//            $this->modx->log(1,'setDiscountForCart cart '.var_export($_SESSION['mspc']['cart'],1));
            if (!$refresh) {
                $this->mspc->ms2->cart->set($this->mspc->cart);
            }
        }

        return true;
    }

    /**
     * Устанавливает скидку на товар.
     *
     * @param      $coupon_id
     * @param      $key
     * @param      $product
     * @param bool $refresh [description]
     *
     * @return bool
     */
    public function setDiscountForProduct($coupon_id, $key, $product, $refresh = false)
    {
        if ($coupon_id && !empty($key) && !empty($product)) {
            $coupon = $this->mspc->coupon->getCurrentCoupon();
            $cart = $this->mspc->cart;

            // Вычисляем скидку на товар
            $discount = false;
            if ($coupon['allcart']) {
                // Скидка на всю корзину
                if (!empty($coupon['discount']) && $coupon['discount'] != '0%') {
                    if (!strstr($coupon['discount'], '%')) {
                        // Получаем значения корзины
                        if (!empty($this->mspc->cart) && empty($this->cart_total_cost)) {
                            foreach ($this->mspc->cart as $v) {
                                $this->cart_total_cost += (float)$v['price'] * (float)$v['count'];
                            }
                        }
                        if ($this->cart_total_cost) {
                            // Если стоимость корзины меньше фиксированной скидки купона
                            // Чтобы не было минусовой цены корзины
                            if ($this->cart_total_cost < $coupon['discount']) {
                                $discount = 0;
                            } else {
                                $discount = ($this->cart_total_cost - $coupon['discount']) / $this->cart_total_cost * 100;
                            }
                        }
                    } else {
                        $discount = $coupon['discount'];
                    }
                }
            } else {
                // Скидка к каждому товару непосредственно
                $discount = $this->getDiscount($coupon_id, $product['id']);
            }

            if ($discount !== false) {
                // Чекаем привязку товара к купону
                if ($coupon['allcart'] || $this->mspc->cache['products']['bound'][$coupon_id][$product['id']]) {
                    if ($coupon['allcart']) {
                        $this->mspc->cart[$key]['price'] = $product['price'] / 100 * floatval($discount);
                        if (strstr($coupon['discount'], '%')) {
                            $this->mspc->cart[$key]['price'] = $product['price'] - $this->mspc->cart[$key]['price'];
                        } else {
                            // // Если это последний элемент в корзине
                            // $decimal_remains = 0;
                            // if ($this->mspc->getArrayLastKey($this->mspc->cart) === $key) {
                            //     if (!empty($this->mspc->cart)) {
                            //         foreach ($this->mspc->cart as $k => &$v) {
                            //             // if ($k === $key) {
                            //             //     continue;
                            //             // }
                            //             $tmp = $v['price'];
                            //             $v['price'] = $this->roundDown($v['price']);
                            //             $decimal_remains += ($tmp - $v['price']) * $v['count'];
                            //         }
                            //         unset($v, $tmp);
                            //     }
                            // }
                            // if (!empty($decimal_remains)) {
                            //     $this->mspc->cart[$key]['price'] += $decimal_remains / $product['count'];
                            // }
                        }
                    } else {
                        $this->mspc->cart[$key]['price'] = $this->getDiscountPrice($product, $cart, $key);
                    }
                    $this->mspc->cart[$key]['old_price'] = $product['price'];

                    // Пишем в старую корзину, чтобы потом всё вернуть при отвязке купона
                    $_SESSION['mspc']['cart'][$key] = array(
                        'discount' => $discount,
                        'id' => $product['id'],
                        'price' => $product['price'],
                        'options' => $product['options'],
                        'ctx' => $product['ctx'],
                    );

                    $this->current[$key] = $_SESSION['mspc']['discount_amount'][$key] = array(
                        'discount' => $product['price'] - $this->mspc->cart[$key]['price'],
                        'count' => $product['count'],
                    );
                }
            }
        }

        return true;
    }

    /**
     * Удаляет скидку из корзины.
     *
     * @param bool $refresh [description]
     *
     * @return bool
     */
    public function removeDiscountFromCart($refresh = false)
    {
        // Освежаем корзину
        $this->mspc->cart = $this->mspc->getFullCart();

        // Ставим старые цены (до скидки)
        foreach ($this->mspc->cart as $key => $p) {
            if (!empty($_SESSION['mspc']['cart'][$key])) {
                $this->mspc->cart[$key]['price'] = $_SESSION['mspc']['cart'][$key]['price'];
                //$this->modx->log(1,'removeDiscountFromCart old_price'.var_export($_SESSION['mspc']['cart'][$key]['old_price'],1));
                //$this->modx->log(1,'removeDiscountFromCart price'.var_export($_SESSION['mspc']['cart'][$key]['price'],1));

                $this->mspc->cart[$key]['old_price'] = $_SESSION['mspc']['cart'][$key]['old_price'];
            }
        }

        if (!$refresh) {
            // $this->modx->log(1, '$this->mspc->cart '.print_r($this->mspc->cart, 1));
            // $this->modx->log(1, '$this->mspc->ms2->cart->get() '.print_r($this->mspc->ms2->cart->get(), 1));
            $this->mspc->ms2->cart->set($this->mspc->cart);

            $this->current = $_SESSION['mspc']['discount_amount'] = array();
        }
        $_SESSION['mspc']['cart'] = array();

        return true;
    }

    /**
     * Обновляет скидку для корзины.
     *
     * @param string $coupon ID купона, который будет применён для корзины
     *
     * @return bool
     */
    public function refreshDiscountForCart($coupon)
    {
        if ($coupon) {
            $this->removeDiscountFromCart(true);
            $this->setDiscountForCart($coupon, true);

            $this->mspc->ms2->cart->set($this->mspc->cart);
        }

        return true;
    }

    /**
     * Получает скидку на товар ($product_id) по купону ($id).
     *
     * @param $id
     * @param $product_id
     *
     * @return mixed Сумма скидки на товар
     */
    public function getDiscount($id, $product_id = 0)
    {
        if (empty($id)) {
            return false;
        }

        if (!empty($product_id) && isset($this->mspc->cache['products']['discount'][$id][$product_id])) {
            return $this->mspc->cache['products']['discount'][$id][$product_id];
        } elseif (empty($product_id) && isset($this->mspc->cache['coupons']['discount'][$id])) {
            return $this->mspc->cache['coupons']['discount'][$id];
        }

        $coupon = $this->mspc->coupon->getCouponByID($id);
        $action = $this->mspc->action->getActionByCouponID($id);
        if (empty($action) && $coupon['action_id']) {
            return;
        }
        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($coupon,1));
        // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($action,1));

        if (!empty($coupon)) {
            // Проверяем, является ли купон "Только для товаров без old_price"
            if (!$coupon['allcart'] && $coupon['oldprice'] && $this->modx->getCount('msProductData', array(
                    'id' => $product_id,
                    '(old_price > 0 AND old_price != price)',
                ))) {
                return false;
            }

            $discount = $coupon['discount'];
            // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($discount,1));

            // >> Если купон действует на весь магазин
            $count_res_coupon = $this->modx->getCount('mspcResource', array('coupon_id' => $id));
            if ((!$count_res_coupon && empty($action)) ||
                (!$count_res_coupon && !empty($action) && !$this->modx->getCount('mspcResource', array('action_id' => $action['id'])))) {
                // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r('купон действует на весь магазин',1));
                if (!empty($product_id)) {
                    $this->mspc->cache['products']['bound'][$id][$product_id] = true;
                    $this->mspc->cache['products']['discount'][$id][$product_id] = $discount;
                    // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r('product_id'.$product_id.' = '.$discount,1));
                }
                // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r('coupon_id'.$id.' = '.$discount,1));

                $this->mspc->cache['coupons']['discount'][$id] = $discount;

                return $discount;
            }
            unset($count_res_coupon);
            // << Если купон действует на весь магазин

            if (empty($product_id)) {
                $this->mspc->cache['coupons']['discount'][$id] = $discount;

                return $discount;
            } else {
                $bound = $this->mspc->cache['products']['bound'][$id][$product_id] = false;

                // Проверяем, не исключён ли товар из промо-кода

                // >> Для начала узнаем, привязан ли товар к этому купону
                if (!empty($action)) {
                    $tmp_where = array('action_id' => $action['id']);
                } else {
                    $tmp_where = array('coupon_id' => $id);
                }

                $q = $this->modx->newQuery('mspcResource', array_merge($tmp_where, array(
                    'resource_id' => $product_id,
                    'type' => 'product',
                )));
                $q->select('discount');
                // $q->prepare(); $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($q->toSQL(),1));

                if ($q->prepare() && $q->stmt->execute()) {
                    $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
                    // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($row,1));

                    if (!empty($row)) {
                        $bound = $this->mspc->cache['products']['bound'][$id][$product_id] = true;

                        // Если для продукта скидка указана - возвращаем её
                        if ($row['discount'] != '') {
                            $discount = $this->mspc->cache['products']['discount'][$id][$product_id] = $row['discount'];

                            return $discount;
                        }
                    }
                    unset($row);
                }
                // << Для начала узнаем, привязан ли товар к этому купону

                // >> Получаем ids категорий
                // $this->modx->log(MODX::LOG_LEVEL_ERROR, 'getDiscount $product_id '.print_r($product_id, 1));
                $cat_ids = array(
                    'primary' => array(),
                    'secondary' => array(),
                );
                $cat_ids['primary'] = array_diff($this->modx->getParentIds($product_id, 10), array(0));
                // $this->modx->log(MODX::LOG_LEVEL_ERROR, 'getDiscount $cat_ids[primary] '.print_r($cat_ids['primary'], 1));

                $q = $this->modx->newQuery('msCategoryMember', array('product_id' => $product_id));
                $q->select('category_id as id');
                if ($q->prepare() && $q->stmt->execute()) {
                    foreach ($q->stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $cat_ids['secondary'][] = $row['id'];
                    }
                    unset($q, $rows, $row);
                }
                // << Получаем ids категорий

                // >> Выбираем скидку из доступных категорий товара
                if ($cats = $this->getDiscountCategories($cat_ids, $id)) {
                    foreach ($cats as $row) {
                        $bound = $this->mspc->cache['products']['bound'][$id][$product_id] = true;

                        if ($row['discount'] != '') {
                            $discount = $this->mspc->cache['products']['discount'][$id][$product_id] = $row['discount'];

                            break;
                        }
                    }
                }
                // << Выбираем скидку из доступных категорий товара

                if ($bound) {
                    return $discount;
                }
            }
        } else {
            return false;
        }

        return false; // если товар не привязан к купону или купона нет/выключен/закончился - скидка 0
    }

    /**
     * Получает массив из списка скидок для категорий ($ids) купона ($coupon_id).
     *
     * @param $ids
     * @param $coupon_id
     *
     * @return array
     */
    public function getDiscountCategories($ids = array(), $coupon_id)
    {
        $rows = array();

        if (isset($ids['primary']) || isset($ids['secondary'])) {
            $ids['primary'] = isset($ids['primary']) ? $ids['primary'] : array();
            $ids['secondary'] = isset($ids['secondary']) ? $ids['secondary'] : array();
            $_ids = array_merge($ids['primary'], $ids['secondary']);
        } else {
            $_ids = !is_array($ids) ? array($ids) : $ids;
        }
        $_ids = array_diff($_ids, array(0));

        if (empty($_ids) || empty($coupon_id)) {
            return $rows;
        }

        // $this->iteration = isset($this->iteration)
        //     ? ++$this->iteration
        //     : 0;
        // $this->modx->log(1, 'getDiscountCategories $ids '.$this->iteration.print_r($ids, 1));

        $exclude = array();
        foreach ($_ids as $i => $id) {
            if (isset($this->mspc->cache['categories']['discount'][$coupon_id][$id])) {
                $rows = array_merge($rows, $this->mspc->cache['categories']['discount'][$coupon_id][$id]);
                $exclude[] = $id;
            }
        }
        $_ids = array_diff($_ids, $exclude); // исключаем найденные в кеше

        // $this->modx->log(1, 'getDiscountCategories $_ids '.$this->iteration.print_r($_ids, 1));
        // $this->modx->log(1, 'getDiscountCategories $rows '.$this->iteration.print_r($rows, 1));

        if (!empty($_ids)) {
            $coupon = $this->mspc->coupon->getCouponByID($coupon_id);
            $action = $this->mspc->action->getActionByCouponID($coupon_id);

            if (!empty($action)) {
                $where = array('action_id' => $action['id']);
            } else {
                $where = array('coupon_id' => $coupon_id);
            }

            $q = $this->modx->newQuery('mspcResource', array_merge($where, array(
                'resource_id:IN' => $_ids,
                'type' => 'category',
            )));
            $q->select('resource_id,discount,power');
            $q->sortby('power', 'DESC');
            $q->sortby('id', 'ASC');
            // $q->prepare(); $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($q->toSQL(),1));

            if ($q->prepare() && $q->stmt->execute()) {
                $_rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
                if (is_array($_rows)) {
                    $rows = array_merge($rows, $_rows);
                }
                // $this->modx->log(1, 'getDiscountCategories $_rows '.$this->iteration.print_r($_rows, 1));
            }
        }

        $this->mspc->cache['categories']['discount'][$coupon_id] = array();
        foreach ($rows as &$row) {
            if ($row['resource_id']) {
                $important = 0;
                foreach (array(1 => 'primary', 2 => 'secondary') as $i => $val) {
                    if (isset($ids[$val]) && in_array($row['resource_id'], $ids[$val])) {
                        $important = $i;
                        break;
                    }
                }
                $row['important'] = $important;

                // $rows[] =
                $this->mspc->cache['categories']['discount'][$coupon_id][$row['resource_id']][] = $row;
            }
        }
        unset($row);
        // $this->modx->log(1, 'getDiscountCategories $rows before_sort '.$this->iteration.print_r($rows, 1));

        // Сортируем по power по убыванию и по discount по возрастанию
        if ($rows) {
            foreach ($rows as $key => $row) {
                $sort_power[$key] = $row['power'];
                $sort_important[$key] = $row['important'];
            }
            array_multisort($sort_power, SORT_DESC, $sort_important, SORT_ASC, $rows);
        }

        // $this->modx->log(1, 'getDiscountCategories $rows after_sort '.$this->iteration.print_r($rows, 1));

        return $rows;
    }

    /**
     * Получает новую цену на товар ($product_id), вычитая скидку купона из старой цены
     *
     * @param array  $product
     * @param array  $cart
     * @param string $key
     *
     * @return float
     */
    public function getDiscountPrice(array $product, array $cart, $key)
    {
        $coupon = $this->mspc->coupon->getCurrentCoupon();
        if (empty($coupon) || empty($product) || empty($key)) {
            return $product['price'] ?: 0;
        }
        $coupon_id = $coupon['id'];
        $product_id = $product['id'];
        $price = $old_price = $product['price'];

        // Пытаемся получить из кеша
        if (isset($this->mspc->cache['products']['price']['new'][$coupon_id][$key][$price])) {
            return $this->mspc->cache['products']['price']['new'][$coupon_id][$key][$price];
        }

        $discount = $this->getDiscount($coupon_id, $product_id);
        $discount_amount = 0;

        if ($this->mspc->cache['products']['bound'][$coupon_id][$product_id]) {
            //
            $response = $this->mspc->invokeEvent('mspcOnBeforeSetProductDiscount', array(
                'mspc' => $this->mspc,
                'coupon' => $this->mspc->coupon->getCurrentCoupon(),
                'product' => $product,
                'cart' => $cart,
                'key' => $key,
                'price' => $price,
                'discount' => $discount,
            ));
            // $this->modx->log(1, 'getDiscountPrice mspcOnBeforeSetProductDiscount ' . print_r($response, 1));

            if ($response['success']) {
                $price = $old_price = (float)$response['data']['price'];
                $discount = $response['data']['discount'];

                // Вычитаем цену со скидкой
                if ($discount !== 0 && $discount != '0%') {
                    if (strstr($discount, '%')) {
                        $price = $old_price - (($old_price / 100) * floatval($discount));
                    } else {
                        $price = $old_price - floatval($discount);
                    }
                    $discount_amount = $old_price - $price;
                }
            }
            // $this->modx->log(MODX::LOG_LEVEL_ERROR, print_r($product_id.' - '.$price,1));

            //
            $response = $this->mspc->invokeEvent('mspcOnSetProductDiscount', array(
                'mspc' => $this->mspc,
                'coupon' => $this->mspc->coupon->getCurrentCoupon(),
                'product' => $product,
                'cart' => $cart,
                'key' => $key,
                'price' => $price,
                'old_price' => $old_price,
                'discount' => $discount,
                'discount_amount' => $discount_amount,
            ));
            // $this->modx->log(1, 'getDiscountPrice mspcOnSetProductDiscount ' . print_r($response, 1));

            if ($response['success']) {
                $price = (float)$response['data']['price'];
            } else {
                $price = $old_price;
            }

            $price = $price < 0 ? 0 : $price;
        }

        $price = $this->roundUp($price, 2);

        $this->mspc->cache['products']['price']['new'][$coupon_id][$key][$price] = $price;

        return $price;
    }

    /**
     * Округляет дробное число в большую сторону.
     *
     * @param     $number
     * @param int $precision
     *
     * @return float
     */
    public function roundUp($number, $precision = 2)
    {
        $number = (float)sprintf('%f', $number);
        $fig = (int)str_pad('1', ++$precision, '0');

        return (ceil($number * $fig) / $fig);
    }

    /**
     * Округляет дробное число в меньшую сторону.
     *
     * @param     $number
     * @param int $precision
     *
     * @return float
     */
    public function roundDown($number, $precision = 2)
    {
        $number = (float)sprintf('%f', $number);
        $fig = (int)str_pad('1', ++$precision, '0');

        return (floor($number * $fig) / $fig);
    }
}
