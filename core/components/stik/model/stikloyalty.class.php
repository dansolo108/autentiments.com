<?php

class stikLoyalty
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
    }
    
    public function getBonusPercentage($user_id = '')
    {
        $bonusPercent = 0;
		if (empty($user_id)) {
			$user_id = $this->modx->user->id;
		}
        $user = $this->modx->getObject('modUser', $user_id);
        if (!$user) return $bonusPercent;
        $profile = $user->getOne('Profile');
        
        if (!$profile) return $bonusPercent;
        
        if (!$profile->get('mobilephone') || !$profile->get('join_loyalty')) {
            return $bonusPercent;
        }
        
        $userPurchaseAmount = $this->getUserPurchaseAmount($user_id);
        
        // if ($this->zeroLoyaltyExist() === true) {
            $bonusPercent += $this->getLoyaltyDiscount($userPurchaseAmount);
        // }
        
        return $bonusPercent;
    }
    
    public function getInfo($user_id = '')
    {
        $info = [];
		if (empty($user_id)) {
			$user_id = $this->modx->user->id;
		}
        $user = $this->modx->getObject('modUser', $user_id);
        if (!$user) return $info;
        $profile = $user->getOne('Profile');
        if (!$profile || !$profile->get('mobilephone') || !$profile->get('join_loyalty')) {
            return $info;
        }
        if ($user = $this->modx->getObject('modUser', $user_id)) {
            $info['amount'] = $this->getUserPurchaseAmount($user_id);
            $info['id'] = $this->getLoyaltyLevelId($info['amount']);
            $info['discount'] = $this->getLoyaltyDiscount($info['amount']);
            $next = $this->getLoyaltyNextInfo($info['amount']);
            $info['next_discount'] = $next['discount'];
            $info['next_amount'] = $next['amount'];
            $info['next_level'] = $next['level'];
            if ($next['level'] > 0) {
                $info['next_slider_percent'] = $info['amount']/$next['level']*100;
            } else {
                $info['next_slider_percent'] = 0;
            }
        }
        return $info;
    }
    
    public function getLoyaltyLevels()
    {
        $levels = '';
        $levels = $this->modx->cacheManager->get('levels', [xPDO::OPT_CACHE_KEY => 'loyalty']);
        if (empty($levels)) {
            $q = $this->modx->newQuery('stikLoyaltyLevel');
            $q->select('stikLoyaltyLevel.id,stikLoyaltyLevel.amount,stikLoyaltyLevel.discount');
            $q->sortby('amount','ASC');
            if ($q->prepare() && $q->stmt->execute()) {
                $levels = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $this->modx->cacheManager->set('levels', $levels, 3600, [xPDO::OPT_CACHE_KEY => 'loyalty']);
        }
        return $levels;
    }
    
    public function getLoyaltyLevelId($amount)
    {
        $levels = $this->getLoyaltyLevels();
        $id = 0;
        foreach ($levels as $level) {
            if ($amount >= $level['amount']) {
                $id = $level['id'];
            }
        }
        return $id;
    }
    
    public function getLoyaltyDiscount($amount)
    {
        $levels = $this->getLoyaltyLevels();
        $discount = 0;
        foreach ($levels as $level) {
            if ($amount >= $level['amount']) {
                $discount = $level['discount'];
            }
        }
        return $discount;
    }
    
    public function getLoyaltyBonusAccrual($amount, $user_id = '')
    {
        $percent = $this->getBonusPercentage($user_id);
        $accrual = ($amount / 100) * $percent;
        return $accrual;
    }
    
    public function zeroLoyaltyExist()
    {
        $levels = $this->getLoyaltyLevels();
        foreach ($levels as $level) {
            if ($level['amount'] == 0) {
                return true;
            }
        }
        return false;
    }
    
    public function getLoyaltyNextInfo($amount)
    {
        $levels = array_reverse($this->getLoyaltyLevels());
        $next = [];
        foreach ($levels as $level) {
            if ($amount <= $level['amount']) {
                $next = [
                    'discount' => $level['discount'],
                    'level' => $level['amount'],
                    'amount' => $level['amount'] - $amount
                ];
            }
        }
        return $next;
    }
    
    public function getUserPurchaseAmount(int $id)
    {
        if ($object = $this->modx->getObject('msCustomerProfile', $id)) {
            return $object->get('spent');
        }
        return 0;
    }
    
    public function userHasFirstOrderDiscount()
    {
        $path = MODX_CORE_PATH . 'components/mspromocode/model/mspromocode/';
        $mspc = $this->modx->getService('mspromocode', 'msPromoCode', $path, array('ctx' => 'web'));
        if ($this->modx->user->isAuthenticated() && $this->modx->getOption('stik_first_order_discount') > 0 && !$mspc->coupon->getCurrentCoupon()) {
            $total_orders = $this->modx->getCount('msOrder', [
                'user_id' => $this->modx->user->get('id')
            ]);
            if ($total_orders == 0) {
                return true;
            }
        }
        return false;
    }
    
    public function getFirstOrderDiscount(int $cost)
    {
        $firstOrderDiscount = $this->modx->getOption('stik_first_order_discount');
        return $cost - ($cost / 100) * $firstOrderDiscount;
    }
}
