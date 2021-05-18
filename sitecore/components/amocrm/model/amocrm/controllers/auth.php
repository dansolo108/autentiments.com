<?php

class Auth
{
    /** @var modX $modx */
    private $modx;
    public $token;
    public $authorized = false;
    private $tools;
    private $config = [];

    /**
     * Auth constructor.
     * @param $modx
     * @param $amo
     */
    public function __construct($modx, $tools)
    {
        $this->modx = $modx;
        $this->tools = $tools;

        $this->config = [
            'client_id' => $this->tools->getSetting('amocrm_client_id'),
            'client_secret' => $this->tools->getSetting('amocrm_client_secret'),
            'client_code' => $this->tools->getSetting('amocrm_client_code'),
            'site_url' => $this->tools->getSetting('site_url'),
        ];
    }

    /**
     * Проверка наличия актуального токена
     * @return bool
     */
    public function checkAuth()
    {
        $access_token = $this->checkToken();
        if (!$access_token) {
            $this->modx->log(1, '[amoCRM] Auth error');
            $this->authorized = false;
        } else {
            $this->authorized = true;
            $this->token = $access_token;
        }

        return $this->authorized;
    }

    /**
     * Проверка существования токена
     * @return string
     */
    private function checkToken()
    {
        $this->modx->cacheManager->refresh(['system_settings' => ['amocrm_token_field']]);
        $data = $this->modx->getOption('amocrm_token_field');
        if (!empty($data)) {
            $data = json_decode($data, true);
            $expires = $data['expires_in'];

            if ($expires <= time()) {
                //Время жизни токена истекло - обновляю токен
                $accessToken = $this->refreshToken($data['refresh_token']);
                if ($accessToken) {
                    return $accessToken;
                }
            }

            $refresh_token_lifetime = $data['refresh_token_lifetime'];
            if ($refresh_token_lifetime <= time()) {
                $this->modx->log(1, '[amoCRM] истек срок жизни refresh Токена. Требуется заново создать токен');
            }
            $accessToken = $data['access_token'];
            return $accessToken;
        }

        $accessToken = $this->getToken();
        return $accessToken;
    }

    /**
     * Получение Токена из API AmoCRM
     * @return string
     */
    private function getToken()
    {
        $link = $this->tools->prepareLink('/oauth2/access_token');
        $data = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code' => $this->config['client_code'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->config['site_url']
        ];

        $accessToken = $this->tools->sendCURL($link, $data, 'POST');

        if ($accessToken) {
            $this->saveToken($accessToken);
            $this->clearClientCode();
            return $accessToken['access_token'];
        }

    }

    /**
     * Сохранение данных о токене в системной настройке
     * @param array $accessToken
     */
    private function saveToken($accessToken)
    {
        $accessToken['expires_in'] = time() + $accessToken['expires_in'];
        $lifetime = time() + strtotime('3 month');
        $accessToken['refresh_token_lifetime'] = $lifetime;

        $setting = $this->modx->getObject('modSystemSetting', array('key' => 'amocrm_token_field'));
        $setting->set('value', json_encode($accessToken));
        $setting->save();
    }

    /**
     * Обновление истекшего токена
     * @param $refresh_token
     * @return bool|string
     */
    private function refreshToken($refresh_token)
    {
        $link = $this->tools->prepareLink('/oauth2/access_token');

        $data = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code' => $this->config['client_code'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'redirect_uri' => $this->config['site_url']
        ];

        $accessToken = $this->tools->sendCURL($link, $data, 'POST');

        if ($accessToken) {
            $this->saveToken($accessToken);
            return $accessToken['access_token'];
        }
        return false;
    }

    private function clearClientCode()
    {
        $setting = $this->modx->getObject('modSystemSetting', array('key' => 'amocrm_client_code'));
        $setting->set('value', '');
        $setting->save();
    }
}
