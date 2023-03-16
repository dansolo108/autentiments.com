<?php

if (!class_exists('msOrderInterface')) {
    require_once MODX_CORE_PATH . 'components/minishop2/handlers/msorderhandler.class.php';
}

class msmcOrder extends msOrderHandler implements msOrderInterface
{


    /**
     * Switch order status
     *
     * @param integer $order_id The id of msOrder
     * @param integer $status_id The id of msOrderStatus
     *
     * @return boolean|string
     */
    public function changeOrderStatus($order_id, $status_id)
    {
        /** @var msOrder $order */
        if (!$order = $this->modx->getObject('msOrder', array('id' => $order_id), false)) {
            return $this->modx->lexicon('ms2_err_order_nf');
        }
        $ctx = $order->get('context');
        $this->modx->switchContext($ctx);
        $this->ms2->initialize($ctx);
        $error = '';
        /** @var msOrderStatus $status */
        if (!$status = $this->modx->getObject('msOrderStatus', array('id' => $status_id, 'active' => 1))) {
            $error = 'ms2_err_status_nf';
        } /** @var msOrderStatus $old_status */
        else {
            if ($old_status = $this->modx->getObject('msOrderStatus',
                array('id' => $order->get('status'), 'active' => 1))
            ) {
                if ($old_status->get('final')) {
                    $error = 'ms2_err_status_final';
                } else {
                    if ($old_status->get('fixed')) {
                        if ($status->get('rank') <= $old_status->get('rank')) {
                            $error = 'ms2_err_status_fixed';
                        }
                    }
                }
            }
        }
        if ($order->get('status') == $status_id) {
            $error = 'ms2_err_status_same';
        }

        if (!empty($error)) {
            return $this->modx->lexicon($error);
        }

        $response = $this->ms2->invokeEvent('msOnBeforeChangeOrderStatus', array(
            'order' => $order,
            'status' => $order->get('status'),
        ));
        if (!$response['success']) {
            return $response['message'];
        }

        $order->set('status', $status_id);

        if ($order->save()) {
            $this->ms2->orderLog($order->get('id'), 'status', $status_id);
            $response = $this->ms2->invokeEvent('msOnChangeOrderStatus', array(
                'order' => $order,
                'status' => $status_id,
            ));
            if (!$response['success']) {
                return $response['message'];
            }

            $lang = $this->modx->getOption('cultureKey', null, 'en', true);
            if ($tmp = $this->modx->getObject('modUserSetting', array('key' => 'cultureKey', 'user' => $order->get('user_id')))) {
                $lang = $tmp->get('value');
            } else if ($tmp = $this->modx->getObject('modContextSetting', array('key' => 'cultureKey', 'context_key' => $order->get('context')))) {
                $lang = $tmp->get('value');
            }
            $this->modx->setOption('cultureKey', $lang);
            $this->modx->lexicon->load($lang . ':minishop2:default', $lang . ':minishop2:cart');

            $pls = $order->toArray();
            $pls['cost'] = $this->ms2->formatPrice($pls['cost']);
            $pls['cart_cost'] = $this->ms2->formatPrice($pls['cart_cost']);
            $pls['delivery_cost'] = $this->ms2->formatPrice($pls['delivery_cost']);
            $pls['weight'] = $this->ms2->formatWeight($pls['weight']);
            $pls['payment_link'] = '';
            if ($payment = $order->getOne('Payment')) {
                if ($class = $payment->get('class')) {
                    $this->ms2->loadCustomClasses('payment');
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
                $subject = $this->ms2->pdoTools->getChunk('@INLINE ' . $status->get('subject_manager'), $pls);
                $tpl = '';
                if ($chunk = $this->modx->getObject('modChunk', array('id' => $status->get('body_manager')))) {
                    $tpl = $chunk->get('name');
                }
                $body = $this->modx->runSnippet('msMultiCurrencyGetOrder', array_merge($pls, array('tpl' => $tpl)));
                $emails = array_map('trim', explode(',',
                        $this->modx->getOption('ms2_email_manager', null, $this->modx->getOption('emailsender')))
                );
                if (!empty($subject)) {
                    foreach ($emails as $email) {
                        if (preg_match('#.*?@.*#', $email)) {
                            $this->ms2->sendEmail($email, $subject, $body);
                        }
                    }
                }
            }

            if ($status->get('email_user')) {
                if ($profile = $this->modx->getObject('modUserProfile', array('internalKey' => $pls['user_id']))) {
                    $subject = $this->ms2->pdoTools->getChunk('@INLINE ' . $status->get('subject_user'), $pls);
                    $tpl = '';
                    if ($chunk = $this->modx->getObject('modChunk', array('id' => $status->get('body_user')))) {
                        $tpl = $chunk->get('name');
                    }
                    $body = $this->modx->runSnippet('msMultiCurrencyGetOrder', array_merge($pls, array('tpl' => $tpl)));
                    $email = $profile->get('email');
                    if (!empty($subject) && preg_match('#.*?@.*#', $email)) {
                        $this->ms2->sendEmail($email, $subject, $body);
                    }
                }
            }
        }

        return true;
    }
}