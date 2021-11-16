<?php

interface smsinterface
{
    public function sendSms($message = '', $phones = '', $options = []);
}

class smsabstract
{
    public $sms = false;
    public $config = [];
    public $url = '';
    public $login = '';
    public $pass = '';
    public $authMode = false;
    public $format = 'json';
    public $curlCode = '';
    public $test = false;
    public $serviceName = '';
    public $sender = '';
    public $charset = '';

    public function __construct(&$sms, array $config = [])
    {
        $this->sms = &$sms;
        $this->config = array_merge($config, []);
        $this->setParameters($config);
    }

    public function setParameters($config = [])
    {
        if (is_array($config) && count($config)) {
            foreach ($config as $k => $v) {
                if (isset($this->$k)) {
                    $this->$k = $v;
                }
            }
        }
    }

    public function send($url = '', $post = [], $options = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        if ($this->authMode) {
            curl_setopt($curl, CURLOPT_USERPWD, $this->login . ':' . $this->pass);
        }
        if (is_array($post) && count($post)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        $response = curl_exec($curl);
        $this->curlCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($this->format == 'json') {
            return json_decode($response, true);
        }
        return $response;
    }

    public function failure($params = [])
    {
        if (!is_array($params)) {
            $params = [];
        }
        return array_merge($params, ['success' => false]);
    }

    public function success($params = [])
    {
        if (!is_array($params)) {
            $params = [];
        }
        return array_merge($params, ['success' => true]);
    }

    public function __get($name)
    {
        return $this->config[$name];
    }

}

class sms
{

    public $modx;
    public $config;
    public $service = false;
    const ERROR_EMPTY_MESSAGE = 'empty_message';
    const ERROR_EMPTY_PHONES = 'empty_phones';
    const ERROR_EMPTY_RESPONSE = 'empty_response';
    const SUCCESS_SEND_SMS = 'send_sms';
    const SUCCESS_SEND_SMS_VIRTUAL = 'send_sms_virtual';
    const EVENTBEFORECODESEND = 'SMSBeforeCodeSend';
    const EVENTAFTERCODESEND = 'SMSAfterCodeSend';
    const EVENTBEFORECODECHECK = 'SMSBeforeCodeCheck';
    const EVENTAFTERCODECHECK = 'SMSAfterCodeCheck';
    const EVENTCODEACTIVATE = 'SMSCodeActivate';
    public $numbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    public $alphabet = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    public $data = [];
    public $mode = '';
    public $values = [];
    public $version = '1.2.2';

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx = &$modx;

        $corePath = $this->modx->getOption('sms_core_path', $config,
            $this->modx->getOption('core_path') . 'components/sms/'
        );
        $assetsUrl = $this->modx->getOption('sms_assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/sms/'
        );
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge([
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
            'mediaSource' => $this->modx->getOption('sms_media_source', null, 1, true),
            'codeLength' => $this->modx->getOption('sms_code_length', null, 4, true),
            'codeMode' => $this->modx->getOption('sms_code_mode', null, 0, true), // 0 - только цифры, 1 - только буквы, 2 - и буквы и цифры
            'codeMessageTpl' => $this->modx->getOption('sms_code_message_tpl', null, 'sms_code', true),
            'smsTimeout' => $this->modx->getOption('sms_timeout', null, 5, true),
            'smsCodeLife' => $this->modx->getOption('sms_code_life', null, 10, true),
            'smsCodeAttempt' => $this->modx->getOption('sms_code_attempt', null, 5, true),
            'smsSendFields' => $this->modx->getOption('sms_send_fields', null, 'id,phone,message,status,count,service,ip,createdon', true),
            'sandbox' => $this->modx->getOption('sms_sandbox', null, false),
        ], $config);

        $this->modx->addPackage('sms', $this->config['modelPath']);
        $this->modx->lexicon->load('sms:default');
    }

    public function initialize()
    {
        if ($this->service) {
            return true;
        }
        $name = $this->modx->getOption('sms_service');
        $file = $this->config['modelPath'] . 'modules/' . $name . '.class.php';
        if (!$name || !file_exists($file)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'SMS: Не удалось загрузить класс sms сообщений "' . $name . '"');
            return false;
        }
        require_once $file;
        $arr = [
            'login' => $this->modx->getOption('sms_login'),
            'pass' => $this->modx->getOption('sms_pass'),
            'sender' => $this->modx->getOption('sms_sender'),
            'charset' => $this->modx->getOption('sms_charset'),
        ];
        if ($extra = json_decode($this->modx->getOption('sms_extra'), true)) {
            $arr = array_merge($arr, $extra);
        }
        $this->service = new smsservice($this, $arr);
        return true;

    }

    public function sendSms($text, $phones, $options = [])
    {
        if ($phones) {
            $phones = $this->clearPhone($phones);
            if ($this->config['sandbox']) {
                $response = ['success' => 1, 'id' => '', 'code' => sms::SUCCESS_SEND_SMS_VIRTUAL, 'response' => []];
            } else {
                $response = $this->service->sendSms($text, $phones, $options);
            }
        } else {
            $response = ['success' => false, 'id' => '', 'code' => sms::ERROR_EMPTY_PHONES, 'response' => []];
        }
        $response['message'] = $this->getCodeInfo($response['code']);
        $this->log($text, $phones, (int)$response['success'], $response['id'], $response['code'], $response['response']);
        return $response;
    }

    public function log($message = '', $phone = '', $send = 0, $sms_id = 0, $code = 0, $response = [])
    {
        if ($code == sms::ERROR_EMPTY_PHONES) {
            return true;
        }
        if (!is_array($response)) {
            $response = [];
        }
        $where = [
            'send' => (int)$send,
            'user_id' => (int)$this->modx->user->id,
            'sms_id' => (string)$sms_id,
            'phone' => $phone,
            'message' => $message,
            'service' => $this->service->serviceName,
            'createdon:>=' => date('Y-m-d H:i:s', time() - 10 * 60),
        ];
        if (!$send && $obj = $this->modx->getObject('extSms', $where)) {
            $obj->set('count', ($obj->get('count') + 1));
            $obj->set('info', $this->getCodeInfo($code));
            return $obj->save();
        }
        $status = $send ? 2 : 1;
        if ($this->config['sandbox']) {
            $status = 3;
            $send = 2;
        }
        $obj = $this->modx->newObject('extSms');
        $obj->fromArray([
            'send' => (int)$send,
            'user_id' => (int)$this->modx->user->id,
            'sms_id' => (string)$sms_id,
            'phone' => $phone,
            'message' => $message,
            'info' => $this->getCodeInfo($code),
            'status' => $status,
            'count' => 1,
            'service' => $this->service->serviceName,
            'properties' => $response,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'client' => $_SERVER['HTTP_USER_AGENT'],
        ]);
        return $obj->save();
    }

    public function clearPhone($phones = '')
    {
        if (!$phones) {
            return false;
        }
        $phones = explode(',', $phones);
        foreach ($phones as $k => $v) {
            $phones[$k] = $this->parsePhone($v);
        }
        return implode(',', $phones);
    }

    public function parsePhone($phone = '')
    {
        if (!$phone) {
            return false;
        }
        preg_match_all('/[0-9]/', $phone, $mathes);
        return implode('', $mathes[0]);
    }

    public function getCodeInfo($code = '')
    {
        $text = '';
        switch ($code) {
            case sms::ERROR_EMPTY_MESSAGE:
            case sms::ERROR_EMPTY_PHONES:
            case sms::ERROR_EMPTY_RESPONSE:
            case sms::SUCCESS_SEND_SMS:
            case sms::SUCCESS_SEND_SMS_VIRTUAL:
                $text = $this->modx->lexicon('sms_send_status_' . $code);
                break;
            default:
                $text = $code;
                break;
        }
        return $text;
    }

    public function getSymbols()
    {
        switch ((int)$this->config['codeMode']) {
            case 1:
                $symbols = $this->alphabet;
                break;
            case 2:
                $symbols = array_merge($this->numbers, $this->alphabet);
                break;
            default:
                $symbols = $this->numbers;
                break;
        }
        return $symbols;
    }

    public function getCode($symbols = [])
    {
        $length = $this->config['codeLength'];
        $countSymbols = count($symbols) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $symbols[rand(0, $countSymbols)];
        }
        return $code;
    }

    public function sendCode($tpl = '', $phone = '', $mode = '')
    {
        $phone = $this->clearPhone($phone);
        if ($this->checkActiveCode($phone, $mode)) {
            return ['success' => false, 'message' => $this->modx->lexicon('sms_send_status_code_dubl', ['time' => $this->config['smsCodeLife']])];
        }
        $tpl = $tpl ? $tpl : $this->config['codeMessageTpl'];
        $symbols = $this->getSymbols();
        $code = $this->getCode($symbols);
        $params = ['tpl' => $tpl, 'code' => $code
            , 'phone' => $phone, 'mode' => $mode, 'values' => $this->values];
        $event = $this->invokeEvent(SMS::EVENTBEFORECODESEND, $params);
        $tpl = $event['tpl'] ? $event['tpl'] : $tpl;
        $data = $event['response'] ? $event['response'] : [];
        if (is_array($data) && count($data)) {
            $data['success'] = false;
            return $data;
        }
        $message = $this->modx->getChunk($tpl, ['code' => $code]);
        $results = $this->sendSms($message, $phone);
        if ($results['success']) {
            $this->saveCode($code, $phone, $mode);
        }
        $results['code_send'] = $code;
        $response = $this->getResponse($results);
        $params = ['response' => $response, 'data' => $results
            , 'code' => $code, 'phone' => $phone, 'mode' => $mode, 'values' => $this->values];
        $event = $this->invokeEvent(SMS::EVENTAFTERCODESEND, $params);
        $response = $event['response'] ? array_merge($response, $event['response']) : $response;
        return $response;
    }

    public function saveCode($code = '', $phone = '', $mode = '')
    {
        $obj = $this->modx->newObject('extSmsCode');
        $obj->fromArray([
            'phone' => $phone,
            'code' => $code,
            'service' => $this->service->serviceName,
            'mode' => $mode,
            'properties' => [],
        ]);
        $obj->save();
        $this->data = $obj->toArray();
    }

    public function checkActiveCode($phone = '', $mode = '')
    {
        return $this->modx->getCount('extSmsCode', [
            'phone' => $phone,
            'mode' => $mode,
            'createdon:>=' => date('Y-m-d H:i:s', time() - ((int)$this->config['smsCodeLife'] * 60)),
            'used' => 0,
        ]);
    }

    public function checkCode($phone = '', $code = '', $mode = '')
    {
        $phone = $this->clearPhone($phone);
        $c = $this->modx->newQuery('extSmsCode');
        $c->where([
            'phone' => $phone,
            'mode' => $mode,
            'createdon:>=' => date('Y-m-d H:i:s', time() - ((int)$this->config['smsCodeLife'] * 60)),
        ]);
        $c->sortby('createdon', 'DESC');
        $c->limit(1);
        if ($obj = $this->modx->getObject('extSmsCode', $c)) {
            $params = ['code' => $code
                , 'phone' => $phone, 'mode' => $mode, 'codeObj' => $obj, 'values' => $this->values];
            $event = $this->invokeEvent(SMS::EVENTBEFORECODECHECK, $params);
            $data = $event['response'] ? $event['response'] : [];
            if (is_array($data) && count($data)) {
                $data['success'] = false;
                return $data;
            }
            if (!$obj->get('used') && $obj->get('attempt') < $this->config['smsCodeAttempt']) {
                if ($obj->get('code') == $code) {
                    $response = ['success' => true, 'message' => ''];
                } else {
                    $obj->set('attempt', ($obj->get('attempt') + 1));
                    $obj->save();
                    $response = ['success' => false, 'message' => $this->modx->lexicon('sms_code_invalid')];
                }
                $params = ['code' => $code, 'phone' => $phone
                    , 'mode' => $mode, 'codeObj' => $obj, 'response' => $response, 'values' => $this->values];
                $event = $this->invokeEvent(SMS::EVENTAFTERCODECHECK, $params);
                $response = $event['response'] ? array_merge($response, $event['response']) : $response;
                return $response;
            }
            if ($obj->get('attempt') >= $this->config['smsCodeAttempt']) {
                $response = ['success' => false, 'message' => $this->modx->lexicon('sms_code_attempt')];
                $params = ['code' => $code, 'phone' => $phone
                    , 'mode' => $mode, 'codeObj' => $obj, 'response' => $response, 'values' => $this->values];
                $event = $this->invokeEvent(SMS::EVENTAFTERCODECHECK, $params);
                $response = $event['response'] ? array_merge($response, $event['response']) : $response;
                return $response;
            }
        }
        return ['success' => false, 'message' => $this->modx->lexicon('sms_code_no_send')];
    }

    public function activateCode($phone = '', $code = '', $mode = '')
    {
        $phone = $this->clearPhone($phone);
        $c = $this->modx->newQuery('extSmsCode');
        $c->where([
            'phone' => $phone,
            'mode' => $mode,
            'code' => $code,
            'createdon:>=' => date('Y-m-d H:i:s', time() - ((int)$this->config['smsCodeLife'] * 60)),
        ]);
        $c->sortby('createdon', 'DESC');
        $c->limit(1);
        if ($obj = $this->modx->getObject('extSmsCode', $c)) {
            $obj->set('used', 1);
            $obj->save();
        }
        $params = ['code' => $code, 'phone' => $phone
            , 'mode' => $mode, 'codeObj' => &$obj, 'values' => $this->values];
        $this->invokeEvent(SMS::EVENTCODEACTIVATE, $params);
        return true;
    }

    public function getResponse($response = [])
    {
        if ($this->mode == 'user' && is_array($response)) {
            $response = ['success' => $response['success'], 'message' => $response['message']];
        }
        return $response;
    }

    public function invokeEvent($eventName, $params)
    {
        $this->modx->invokeEvent($eventName, $params);
        return $this->modx->event->returnedValues;
    }

}
