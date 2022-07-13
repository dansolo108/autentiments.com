<?php
// Откликаться будет ТОЛЬКО на ajax запросы
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

// Сниппет будет обрабатывать не один вид запросов, поэтому работать будем по запрашиваемому действию
// Если в массиве POST нет действия - выход
if (empty($_POST['action'])) {return;}

// А если есть - работаем
$result = [
    'success'=>false,
    'msg'=>'Упс... что-то пошло не так.'
    ];
switch ($_POST['action']) {
    case 'getPageInfo':
        if(is_array($_POST['id'])){
            $result['info'] = [];
            foreach($_POST['id'] as $id){
                $result['info'][] = json_decode($modx->runSnippet('getJSONPageInfo',['input'=>$id]),1);
            }
            if(count($result['info']) > 0){
                $result['success'] = true;
            }
        }
        else{
            $result['info'] = json_decode($modx->runSnippet('getJSONPageInfo',['input'=>$_POST['id']]),1);
            if(!empty($result['info'])){
                $result['success'] = true;
            }
        }

        break;
	case 'activateCoupon':
	    if($modx->user->isAuthenticated('web')){
	        $code = $_POST['values'][0]['value'];
	        $result['code'] = $code;
	        $coupon = $modx->getObject('stikCoupon',['code'=>$code]);
	        if($coupon && !$coupon->get('used')){
	            $maxma = $modx->getService('maxma', 'maxma', $modx->getOption('core_path').'components/stik/model/', []);
	            $phone = $modx->user->getOne('Profile')->get('phone');
	            $balance = $maxma->getClientBalanceByPhone($phone);
	            $balance += $coupon->get('amount');
	            $data = $maxma->adjustClientBalance($balance,$phone);
	            $coupon->set('used',1);
	            $coupon->set('used_user_id',$modx->user->get('id'));
	            $coupon->save();
    	        $result['amount'] = $coupon->get('amount');
    	        $result['balance'] = $data['clientBonuses']['available'];
    	        $result['data'] = $data;
	            $result['success'] = true;
	            $result['msg'] = 'Зачислено '.$result['amount'].' бонусов';
	        }
	        else{
	            $result['msg'] = 'купон не найден';
	        }
	    }
	    else{
	        $result['msg'] = 'вы не авторизированы';
	    }
        break;
}

// Если у нас есть, что отдать на запрос - отдаем и прерываем работу парсера MODX
if (!empty($result)) {
	die(json_encode($result));
}
return;