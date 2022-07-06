<?php
/**
 * The base class for maxma.
 */

class stikSms {

    /* @var modX $modx */
    public $modx;
    public $namespace = 'stik';
    public $config = [];

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct (modX &$modx, array $config = []) {
        $this->modx =& $modx;
        $this->namespace = $this->modx->getOption('namespace', $config, 'stik');
        $corePath = $this->modx->getOption('core_path') . 'components/stik/';
        $assetsPath = $this->modx->getOption('assets_path') . 'components/stik/';
        $assetsUrl = $this->modx->getOption('assets_url') . 'components/stik/';
        $this->config = array_merge([
            'corePath' => $corePath,
            'assetsPath' => $assetsPath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            
            'assetsUrl' => $assetsUrl,
            'actionUrl' => $assetsUrl . 'action.php',
            'connectorUrl' => $assetsUrl . 'connector.php',
        ], $config);

        $this->modx->addPackage('stik', $this->config['modelPath']);
    }

    public function preparePhone($phone = '') {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public function findUser($phone) {
        $userProfile = $this->modx->getObject('modUserProfile', ['mobilephone' => $phone]);
        if ($userProfile) return $userProfile->getOne('User');
        return false;
    }
    
    public function register(int $phone) {
        $user = $this->modx->newObject('modUser');
        $user->set('username', $phone);
        $user->set('password', $user->generatePassword());
        $user->save();
        
        $profile = $this->modx->newObject('modUserProfile');
        $profile->set('fullname', $phone);
        $profile->set('email', $phone.'@'.$this->modx->getOption('http_host'));
        $profile->set('mobilephone', $phone);
        // сохраняем id пользователя перешедшего по специальной ссылке в AMO
        $amoUserid = $_SESSION['amo_userid'];
        if ($amoUserid) {
            $profile->set('amo_userid', $amoUserid);
            $contact = $this->modx->newObject('amoCRMUser', ['user' => $profile->get('internalKey'), 'user_id' => $amoUserid]);
            $contact->save();
        }
        
        if (is_object($user)) {
            $group = $this->modx->getObject('modUserGroup', ['name' => 'Users']);
            $groupMember = $this->modx->newObject('modUserGroupMember');
            $groupMember->set('user_group', $group->get('id'));
            $groupMember->set('role', 1); // Member
            $user->addMany($groupMember);
        }
        
        $user->addOne($profile);
        
        $profile->save();
        $user->save();
        
        return $user;
    }
    
    public function authenticate(modUser $user, string $ctx) {
        if ($this->modx->getObject('modContext', $ctx) && !$this->modx->user->isAuthenticated($ctx)) {
            $user->addSessionContext($ctx);
            return true;
        } else {
            return false;
        }
    }
}
