<?php

class smsservice extends smsabstract implements smsinterface
{

    public function __construct(&$service, array $config)
    {
        $this->url = 'http://smsc.ru/sys/';
        $this->serviceName = 'smsc.ru';
        return parent::__construct($service, $config);
    }

    public function sendSms($message = '', $phones = '', $options = [])
    {
        if (!$message) {
            return $this->failure(['code' => sms::ERROR_EMPTY_MESSAGE]);
        }
        if (!$phones) {
            return $this->failure(['code' => sms::ERROR_EMPTY_PHONES]);
        }
        $url = $this->url . 'send.php?login=' . $this->login . '&psw=' . $this->pass . '&phones=' . $phones . '&mes=' . $message . '&charset=' . $this->charset . '&fmt=3&tinyurl=1&sender=' . $this->sender;
        $response = $this->send($url);
        $error = $this->checkError($response);
        if ($error !== true) {
            return $this->failure(['code' => $error, 'response' => $response]);
        }
        return $this->success(['id' => $response['id'], 'response' => $response, 'code' => sms::SUCCESS_SEND_SMS]);
    }

    public function checkError($response = [])
    {
        if (!is_array($response)) {
            return sms::ERROR_EMPTY_RESPONSE;
        }
        if (isset($response['error'])) {
            return 'SMS service: ' . $response['error'];
        }
        return true;
    }
}