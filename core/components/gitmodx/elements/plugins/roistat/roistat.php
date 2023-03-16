<?php
/** @var $modx gitModx */
if ($modx->event->name == 'msOnBeforeCreateOrder') {
    /** @var msOrderCustom $msOrder */
    $start = microtime(true);

    $cart_status = $order->ms2->cart->status();
    $cart_items = $order->ms2->cart->get();
    $data = $order->get();
    $num =   $msOrder->get('num');
    $products = '';

    $email = $data['email'];
    $phone = $data['phone'];
    $receiver = $data['receiver'];
    $message = $data['comment'];

    $chart_total_cost = $cart_status['total_cost'];

    foreach ($cart_items as $item){

        $product = $modx->getObject('msProduct', $item['product_id']);
        $product_name = $product->get('pagetitle');
        $product_count = $item['count'];
        $product_price = $item['price'];
        $product_total_price = floatval($product_price) * floatval($product_count);
        $products .= $product_name . ' — ' . $product_count . ' x ' . $product_price . ' = ' . strval($product_total_price) . ";\n";

    }

    $roistatSend = [
        'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : null,
        'key'     => 'Zjg0YzEyMWQ2MTM4ZTkzNTczMTFiYTRkZmFlZjY4M2I6MjIxMDQ4',
        'title'   => 'Заказ с сайта ' . $modx->getOption('http_host').' №'.$num,
        'name'    => $receiver,
        'email'   => $email,
        'phone'   => $phone,
        'is_skip_sending' => '1',
        'comment'=> $message.PHP_EOL. $products
    ];

    try {
        file_get_contents('https://cloud.roistat.com/api/proxy/1.0/leads/add?' . http_build_query($roistatSend));
    } catch (Exception $e) {
        $modx->log(xPDO::LOG_LEVEL_ERROR, $e->getMessage());
        return true;
    }
    return true;

}