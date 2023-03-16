<?php

class stik
{
    /** @var modX $modx */
    public $modx;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $corePath = MODX_CORE_PATH . 'components/stik/';
        $assetsUrl = MODX_ASSETS_URL . 'components/stik/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',

            'connectorUrl' => $assetsUrl . 'connector.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
        ], $config);

        $this->modx->addPackage('stik', $this->config['modelPath']);
        $this->modx->lexicon->load('stik:default');
    }
    
    public function sendEmail($email, $subject, $chunk, $params = '')
    {
        $language = $params['language'];
        if (!$language && isset($params['user_id']) && $params['user_id']) {
            $setting = $this->modx->getObject('modUserSetting', [
                'user' => $params['user_id'],
                'key' => 'cultureKey',
            ]);
            if ($setting) {
                $language = $setting->get('value');
            }
        }
        if (!$language) $language = 'ru';
        $this->modx->switchContext('web');
        $this->modx->setOption('cultureKey', $language);
        $this->modx->lexicon->load($language . ':polylang:site');
        $this->modx->lexicon->load($language . ':minishop2:default');
        
        // для правильной генерации ссылок
        $polylang = $this->modx->getService('polylang', 'Polylang');
        $tools = $polylang->getTools();
        $PolylangLanguage = $this->modx->getObject('PolylangLanguage', array(
            'active' => 1,
            'culture_key' => $language
        ));
        $tools->setLanguage($PolylangLanguage);
        
        $pdo = $this->modx->getService('pdoTools');
        /** @var modPHPMailer $mail */
        $mail = $this->modx->getService('mail', 'mail.modPHPMailer');
        $mail->setHTML(true);
        
        if (empty($email)) $email = $this->modx->getOption('ms2_email_manager', null, $this->modx->getOption('emailsender'));

        $emails = array_map('trim', explode(',', $email));
        foreach ($emails as $email) {
            if (preg_match('#.*?@.*#', $email)) {
                $mail->address('to', trim($email));
            }
        }
        $mail->set(modMail::MAIL_SUBJECT, trim($subject));
        $mail->set(modMail::MAIL_BODY, $pdo->getChunk($chunk, $params));
        $mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        if (!$mail->send()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                'An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo
            );
            $mail->reset();
            return false;
        }
        $mail->reset();
        return true;
    }
}
