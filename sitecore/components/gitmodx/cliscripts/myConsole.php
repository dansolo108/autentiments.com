<?php
define('MODX_API_MODE', true);
require_once dirname(__FILE__, 5) . '/index.php';
/** @var $modx gitModx */
$modx->setLogTarget('ECHO');
$modx->setLogLevel(MODX_LOG_LEVEL_INFO);

/** @var msOrderStatus $status */
$status = $modx->getObject('msOrderStatus',2);
/** @var msOrder $order */
$order = $modx->getObject('msOrder',9999);
/** @var miniShop2 $ms2 */
$ms2 = $modx->getService('minishop2');
$ms2->initialize('web');

$lang = $modx->getOption('cultureKey', null, 'en', true);
if ($tmp = $modx->getObject('modUserSetting', array('key' => 'cultureKey', 'user' => $order->get('user_id')))) {
    $lang = $tmp->get('value');
} else if ($tmp = $modx->getObject('modContextSetting', array('key' => 'cultureKey', 'context_key' => $order->get('context')))) {
    $lang = $tmp->get('value');
}
$modx->setOption('cultureKey', $lang);
$modx->lexicon->load($lang . ':minishop2:default', $lang . ':minishop2:cart');

$pls = $order->toArray();
$pls['cost'] = $ms2->formatPrice($pls['cost']);
$pls['cart_cost'] = $ms2->formatPrice($pls['cart_cost']);
$pls['delivery_cost'] = $ms2->formatPrice($pls['delivery_cost']);
$pls['weight'] = $ms2->formatWeight($pls['weight']);
$pls['payment_link'] = '';
if ($payment = $order->getOne('Payment')) {
    if ($class = $payment->get('class')) {
        $ms2->loadCustomClasses('payment');
        if (class_exists($class)) {
            /** @var msPaymentHandler|PayPal $handler */
            $handler = new $class($order);
            if (method_exists($handler, 'getPaymentLink')) {
                $link = $handler->getPaymentLink($order);
                $pls['payment_link'] = $link;
            }
        }
    }
}

if ($status->get('email_manager')) {
    $subject = $ms2->pdoTools->getChunk('@INLINE ' . $status->get('subject_manager'), $pls);
    $tpl = '';
    if ($chunk = $modx->getObject('modChunk', array('id' => $status->get('body_manager')))) {
        $tpl = $chunk->get('name');
    }
    $body = $modx->runSnippet('msGetOrder', array_merge($pls, array('tpl' => $tpl)));
    $emails = array_map('trim', explode(',',
            $modx->getOption('ms2_email_manager', null, $modx->getOption('emailsender')))
    );
    if (!empty($subject)) {
        foreach ($emails as $email) {
            if (preg_match('#.*?@.*#', $email)) {
                $ms2->sendEmail($email, $subject, $body);
            }
        }
    }
}