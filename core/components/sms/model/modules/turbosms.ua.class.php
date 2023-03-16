<?php

class smsservice extends smsabstract implements smsinterface
{

    public function __construct(&$service, array $config)
    {
        $this->url = 'http://turbosms.in.ua/api/wsdl.html';
        $this->serviceName = 'turbosms.ua';
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
        $client = new SoapClient($this->url);
        $auth = $client->Auth([
            'login' => $this->login,
            'password' => $this->pass,
        ]);
        if ($auth->AuthResult != $this->loginText) {
            return $this->failure(['code' => $auth->AuthResult, 'response' => $auth]);
        }
        $response = $client->SendSMS([
            'sender' => $this->sender,
            'destination' => $phones,
            'text' => $message,
        ]);
        $error = $this->checkError($response);
        if ($error !== true) {
            return $this->failure(['code' => $error, 'response' => $response]);
        }
        return $this->success(['id' => $response->SendSMSResult->ResultArray[1], 'response' => $response->SendSMSResult->ResultArray, 'code' => sms::SUCCESS_SEND_SMS]);
    }

    public function checkError($response)
    {
        if (!$response) {
            return sms::ERROR_EMPTY_RESPONSE;
        }
        if ($response->SendSMSResult->ResultArray[0] != $this->sendText) {
            return 'SMS service: ' . $response->SendSMSResult->ResultArray[0];
        }
        return true;
    }
}