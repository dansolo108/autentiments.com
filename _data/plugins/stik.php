id: 17
source: 1
name: stik
category: stik
properties: 'a:0:{}'

-----

switch ($modx->event->name) {
    
    case 'OnMODXInit':
        // SQL:
        // ALTER TABLE `modx_user_attributes` ADD `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `fullname`, ADD `surname` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `name`, ADD `building` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `surname`, ADD `room` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `building`;
        // ALTER TABLE `modx_user_attributes` ADD `corpus` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `room`;
        // ALTER TABLE `modx_user_attributes` ADD `join_loyalty` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `mobilephone`;
        // ALTER TABLE `modx_user_attributes` ADD `amo_userid` INT(10) UNSIGNED NOT NULL AFTER `website`;
        $modx->loadClass('modUserProfile');
        
        $modx->map['modUserProfile']['fields']['name'] = '';
        $modx->map['modUserProfile']['fields']['surname'] = '';
        $modx->map['modUserProfile']['fields']['building'] = null;
        $modx->map['modUserProfile']['fields']['room'] = null;
        $modx->map['modUserProfile']['fields']['corpus'] = null;
        $modx->map['modUserProfile']['fields']['entrance'] = null;
        $modx->map['modUserProfile']['fields']['join_loyalty'] = 0;
        $modx->map['modUserProfile']['fields']['amo_userid'] = null;
        
        $modx->map['modUserProfile']['fieldMeta']['name'] = array(
            'dbtype' => 'varchar',
            'phptype' => 'string',
            'precision' => 100,
            'null' => false,
            'default' => '',
        );
        $modx->map['modUserProfile']['fieldMeta']['surname'] = array(
            'dbtype' => 'varchar',
            'phptype' => 'string',
            'precision' => 100,
            'null' => false,
            'default' => '',
        );
        $modx->map['modUserProfile']['fieldMeta']['building'] = array(
            'dbtype' => 'varchar',
            'phptype' => 'string',
            'precision' => 10,
            'null' => true,
            'default' => null,
        );
        $modx->map['modUserProfile']['fieldMeta']['room'] = array(
            'dbtype' => 'varchar',
            'phptype' => 'string',
            'precision' => 10,
            'null' => true,
            'default' => null,
        );
        $modx->map['modUserProfile']['fieldMeta']['corpus'] = array(
            'dbtype' => 'varchar',
            'phptype' => 'string',
            'precision' => 10,
            'null' => true,
            'default' => null,
        );
        $modx->map['modUserProfile']['fieldMeta']['entrance'] = array(
            'dbtype' => 'int',
            'precision' => 2,
            'phptype' => 'integer',
            'null' => true,
            'default' => null,
        );
        $modx->map['modUserProfile']['fieldMeta']['join_loyalty'] = array(
            'dbtype' => 'tinyint',
            'precision' => '1',
            'attributes' => 'unsigned',
            'phptype' => 'boolean',
            'default' => 0,
        );
        $modx->map['modUserProfile']['fieldMeta']['amo_userid'] = array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'attributes' => 'unsigned',
            'null' => false,
        );
        
        // polyLang
        $modx->loadClass('PolylangProduct');
        $modx->map['PolylangProduct']['fields']['material'] = NULL;
        $modx->map['PolylangProduct']['fieldMeta']['material'] = array(
            'dbtype' => 'text',
            'phptype' => 'json',
            'null' => true,
        );
        
        break;
        
    case "OnUserFormPrerender":
        if (!isset($user) || $user->get('id') < 1) {
            return;
        }

        if (!$modx->getCount('modPlugin', array('name' => 'AjaxManager', 'disabled' => false))) {
            $data['name'] = htmlspecialchars($user->Profile->name);
            $data['surname'] = htmlspecialchars($user->Profile->surname);
            $data['building'] = htmlspecialchars($user->Profile->building);
            $data['room'] = htmlspecialchars($user->Profile->room);
            $data['corpus'] = htmlspecialchars($user->Profile->corpus);
            $data['entrance'] = htmlspecialchars($user->Profile->entrance);
            $data['amo_userid'] = (int)$user->Profile->amo_userid;
            $data['join_loyalty'] = $user->Profile->join_loyalty ? 'true' : 'false';

            $modx->controller->addHtml("
                <script type='text/javascript'>
                    Ext.ComponentMgr.onAvailable('modx-user-tabs', function() {
                        this.on('beforerender', function() {
                            // Получаем колонки первой вкладки
                            var leftCol = this.items.items[0].items.items[0].items.items[0];

                            // Добавляем новое поле в левую колонку 4ым по счёту полем (перед полем 'Email')
                            leftCol.items.insert(3, 'modx-user-name', new Ext.form.TextField({
                                id: 'modx-user-name',
                                name: 'name',
                                fieldLabel: 'Имя',
                                xtype: 'textfield',
                                anchor: '100%',
                                maxLength: 100,
                                value: '{$data['name']}',
                            }));
                            leftCol.items.insert(4, 'modx-user-surname', new Ext.form.TextField({
                                id: 'modx-user-surname',
                                name: 'surname',
                                fieldLabel: 'Фамилия',
                                xtype: 'textfield',
                                anchor: '100%',
                                maxLength: 100,
                                value: '{$data['surname']}',
                            }));
                            leftCol.items.insert(8, 'modx-user-join-loyalty', new Ext.form.Checkbox({
                                id: 'modx-user-join-loyalty',
                                name: 'join_loyalty',
                                hideLabel: true,
                                boxLabel: 'Программа лояльности',
                                description: 'Участвует в программе лояльности',
                                xtype: 'xcheckbox',
                                inputValue: 1,
                                listeners: {
                                    beforerender: function(that) {
                                        that.hiddenField = new Ext.Element(document.createElement('input')).set({
                                            type: 'hidden',
                                            name: that.name,
                                            value: 0,
                                        });
                                    },
                                    afterrender: function(that) {
                                        that.el.insertHtml('beforeBegin', that.hiddenField.dom.outerHTML);
                                    },
                                },
                                checked: {$data['join_loyalty']},
                            }));
                            leftCol.items.insert(11, 'modx-user-room', new Ext.form.TextField({
                                id: 'modx-user-room',
                                name: 'room',
                                fieldLabel: 'Квартира',
                                xtype: 'textfield',
                                anchor: '50%',
                                maxLength: 10,
                                value: '{$data['room']}',
                            }));
                            leftCol.items.insert(11, 'modx-user-corpus', new Ext.form.TextField({
                                id: 'modx-user-corpus',
                                name: 'corpus',
                                fieldLabel: 'Корпус',
                                xtype: 'textfield',
                                anchor: '50%',
                                maxLength: 10,
                                value: '{$data['corpus']}',
                            }));
                            leftCol.items.insert(11, 'modx-user-entrance', new Ext.form.TextField({
                                id: 'modx-user-entrance',
                                name: 'entrance',
                                fieldLabel: 'Подъезд',
                                xtype: 'textfield',
                                anchor: '50%',
                                maxLength: 10,
                                value: '{$data['entrance']}',
                            }));
                            leftCol.items.insert(11, 'modx-user-amo-userid', new Ext.form.NumberField({
                                id: 'modx-user-amo-userid',
                                name: 'amo_userid',
                                fieldLabel: 'ID в amoCRM',
                                xtype: 'numberfield',
                                anchor: '50%',
                                maxLength: 10,
                                value: '{$data['amo_userid']}',
                            }));
                        });
                    });
                </script>
            ");
        }
        break;
        
    case 'msOnManagerCustomCssJs':
        if ($page == 'orders') {
            $modx->controller->addLastJavascript(MODX_ASSETS_URL.'components/minishop2CustomFields/msorderaddress.js');
            $modx->controller->addHtml("
                    <script type='text/javascript'>
                        Ext.ComponentMgr.onAvailable('minishop2-window-order-update', function(){
                            if (miniShop2.config['order_address_fields'].in_array('properties')){
                                if (this.record.properties){
                                    if (this.record.properties['delivery_date'] !== '') {
                                        this.fields.items[2].items.push(
                                            {
                                                xtype: 'displayfield',
                                                name: 'properties_delivery_date',
                                                fieldLabel: 'Дата доставки',
                                                anchor: '100%',
                                                style: 'border:1px solid #efefef;width:95%;padding:5px;',
                                                html: this.record.properties['delivery_date']
                                            }
                                        );
                                    }
                                    if (this.record.properties['pickup_date'] !== '') {
                                        this.fields.items[2].items.push(
                                            {
                                                xtype: 'displayfield',
                                                name: 'properties_pickup_date',
                                                fieldLabel: 'Дата самовывоза',
                                                anchor: '100%',
                                                style: 'border:1px solid #efefef;width:95%;padding:5px;',
                                                html: this.record.properties['pickup_date']
                                            }
                                        );
                                    }
                                }
                                if (this.record.addr_properties){
                                    this.fields.items[2].items.push(
                                        {
                                            xtype: 'displayfield',
                                            name: 'addr_properties_have_discount_card',
                                            fieldLabel: _('ms2_properties_have_discount_card'),
                                            anchor: '100%',
                                            style: 'border:1px solid #efefef;width:95%;padding:5px;',
                                            html: this.record.addr_properties['have_discount_card'] ? 'Да' : 'Нет'
                                        }
                                    );
                                    this.fields.items[2].items.push(
                                        {
                                            xtype: 'displayfield',
                                            name: 'addr_properties_without_manager_calling',
                                            fieldLabel: _('ms2_properties_without_manager_calling'),
                                            anchor: '100%',
                                            style: 'border:1px solid #efefef;width:95%;padding:5px;',
                                            html: this.record.addr_properties['without_manager_calling'] ? 'Да' : 'Нет'
                                        }
                                    );
                                    this.fields.items[2].items.push(
                                        {
                                            xtype: 'displayfield',
                                            name: 'addr_properties_point',
                                            fieldLabel: _('ms2_properties_point'),
                                            anchor: '100%',
                                            style: 'border:1px solid #efefef;width:95%;padding:5px;',
                                            html: this.record.addr_properties['point']
                                        }
                                    );
                                }        
                            }
                        });                
                    </script>");
        }
    break;

    case 'msOnGetStatusCart':
        $values = & $modx->event->returnedValues;
        $values['status'] = $status;
        $values['status']['real_total_cost'] = $status['total_cost'] + $status['total_discount'];
    break;

    case 'msOnBeforeCreateOrder':
        // Сохраняем receiver
        if (isset($_POST['name']) && isset($_POST['surname'])){
            $receiver = $_POST['name'] . ' ' . $_POST['surname'];
            $msOrder->set('receiver', $receiver);
        }
        // сохраняем в properties адреса заказа чекбоксы
        $address = $msOrder->getOne('Address');
        $addressProperties = $address->get('properties');
        if (!is_array($addressProperties)) {
            $addressProperties = array();
        }
        
        // if (isset($_POST['have_discount_card']) && $_POST['have_discount_card'] == 1){
        //     $addressProperties['have_discount_card'] = 1;
        // }
        // if (isset($_POST['without_manager_calling']) && $_POST['without_manager_calling'] == 1){
        //     $addressProperties['without_manager_calling'] = 1;
        // }
        if (isset($_POST['point']) && $_POST['point']){
            $addressProperties['point'] = htmlentities($_POST['point'], ENT_COMPAT | ENT_HTML401, 'UTF-8');
        }
        
        if (count($addressProperties) > 0){
            $address->set('properties', json_encode($addressProperties));
        }
        
        // сохраняем в properties заказа
        $orderProperties = $msOrder->get('properties');
        if (!is_array($orderProperties)) $orderProperties = array();

        if (isset($_POST['order_rates']) && $_POST['order_rates']){
            $orderProperties['order_rates'] = htmlentities($_POST['order_rates'], ENT_COMPAT | ENT_HTML401, 'UTF-8');
        }
        
        if (isset($_POST['order_discount']) && $_POST['order_discount']){
            $orderProperties['order_discount'] = (int)$_POST['order_discount'];
        }
        
        // Проверяем периходил ли пользователь по специальной ссылке
        $amoUserid = $_SESSION['amo_userid'];
        if (!$amoUserid) {
            if ($user = $modx->getObject('modUser', $msOrder->get('user_id'))) {
                if ($profile = $user->getOne('Profile')) {
                    $amoUserid = $profile->get('amo_userid');
                }
            }
        }
        if ($amoUserid){
            $orderProperties['amo_userid'] = $amoUserid;
        }
        
        if (count($orderProperties) > 0){
            $msOrder->set('properties', $orderProperties);
        }
        break;
        
    case "msOnCreateOrder":
        // При первом заказе
        $total_orders = $modx->getCount('msOrder', ['user_id' => $msOrder->get('user_id')]);
        if ($total_orders == 1) {
            // Сохраняем в профайл пользователя поля из заказа
            $msAddress = $msOrder->getOne('Address');
            $user = $modx->getObject('modUser', $msOrder->get('user_id'));
            $profile = $user->getOne('Profile');
            
            $fullname = $msAddress->get('name') . ' ' . $msAddress->get('surname');
            $profile->set('name', $msAddress->get('name'));
            $profile->set('surname', $msAddress->get('surname'));
            $profile->set('fullname', $fullname);
            // $profile->set('mobilephone', preg_replace("/[^0-9+]/", "", $msAddress->get('phone')));
            $profile->save();
            
            // Авторизуем пользователя
            $user->addSessionContext('web');
        }
        break;
        
    case 'msOnChangeOrderStatus':
        if ($status == 1) {
            $userId = $order->user_id;
            $user = $modx->getObject('modUser', $userId);
            $profile = $user->getOne('Profile');
            $time = time();
            $newUser = 10; // Сколько секунд пользователь считается новым

            if ($user) {
                $username = $user->get('username');
                $createdon = strtotime($user->get('createdon')) + $newUser;

                if ($createdon > $time) {
                    //Генератор пароля. Взят из исходников MODX
                    //Длина пароля
                    $length = 6;

                    $pass = $modx->user->generatePassword($length);

                    //Сохраняем новый пароль
                    $user->set('password', $pass);
                    $user->save();

                    //Шлем письмо
                    $subject = $modx->lexicon('stik_first_order_register_subject');
                    $chunk = 'firstOrderUserRegisterEmail';
                    $params = [
                        'username' => $username,
                        'password' => $pass
                    ];
                    $stik = $modx->getService('stik', 'stik', $modx->getOption('core_path') . 'components/stik/model/',[]);
                    if (($stik instanceof stik)) {
                        $stik->sendEmail($profile->get('email'), $subject, $chunk, $params);
                    } else {
                        $pdo = $modx->getService('pdoFetch');
                        $message = $pdo->getChunk($chunk, $params);
                        $sent = $user->sendEmail($message, array('subject' => $subject));
                    }
                    //Мгновенная авторизация на сайте без набора пароля
                    $user->addSessionContext('web');
                }

            }
        }
        break;
        
    case 'msmcOnGetPrice':
        $returned_values = & $modx->event->returnedValues;
        $values = $modx->event->params;
        $returned_values = $values;
        if ($values['currency']['cid'] != 1) {
            $returned_values['newPrice'] = ceil($newPrice ?: $price);
        }
        break;
        
    case 'OnHandleRequest':
        // $modx->log(1, print_r($_SESSION,1));
        $amoUserid = (int)$_GET['amo'];
        if ($amoUserid) {
            $_SESSION['amo_userid'] = $amoUserid;
            if ($user = $modx->getUser()) {
                if ($profile = $user->getOne('Profile')) {
                    if (!$profile->get('amo_userid')) {
                        $profile->set('amo_userid', $amoUserid);
                        $profile->save();
                        if ($modx->getObject('amoCRMUser', array('user' => $user->get('id'), 'user_id' => $amoUserid))) {
                            $contact = $modx->newObject('amoCRMUser', array('user' => $user->get('id'), 'user_id' => $amoUserid));
                            $contact->save();
                        }
                    }
                }
            }
        }
        break;

    case 'pdoToolsOnFenomInit':
        $fenom->addModifier('round', function ($input) {
            $output = round($input);
            return $output;
        });

        $fenom->addModifier('isajax', function ($input) {
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
            if (!$isAjax) return false;
            return true;
        });
        
        $fenom->addModifier('count', function ($input) {
            if (!is_array($input)) return 0;
            return count($input);
        });
        
        $fenom->addModifier('substr', function ($input, $start = 4) {
            return mb_substr($input, $start);
        });
        
        $fenom->addModifier('paymentAvailable', function ($input, $seconds = 4) {
            $current = strftime("%s");
            return ($current - $input <= $seconds) ? true : false;
        });
        
        $fenom->addModifier('array_merge', function ($arr1, $arr2) {
            if (isset($arr1) && isset($arr2)) {
                return array_merge($arr1, $arr2);
            }
        });
        
        $fenom->addModifier('discount', function ($old_price, $price) {
            if ($old_price && $price) {
                $old_price = str_replace(' ', '', $old_price);
                $price = str_replace(' ', '', $price);
                $discount = (($old_price - $price)/$old_price)*100;
                return round($discount);
            }
            return false;
        });
        
        $fenom->addModifier('priceFormat', function ($input, $no_space = false) use ($modx) {
            $input = str_replace(" ", "", $input);
            if ($input > 0) {
                $pf = $modx->fromJSON($modx->getOption('ms2_price_format', null, '[2, ".", " "]'));
                if (is_array($pf)) {
                    $price = number_format($input, $pf[0], $pf[1], $pf[2]);
                }
                if ($modx->getOption('ms2_price_format_no_zeros', null, true)) {
                    if (preg_match('/\..*$/', $price, $matches)) {
                        $tmp = rtrim($matches[0], '.0');
                        $price = str_replace($matches[0], $tmp, $price);
                    }
                }
                if ($no_space) $price = str_replace(' ', '', $price);
                return $price;
            } else {
                return $input;
            }
        });
        
        $fenom->addModifier('priceFormatZeroes', function ($input) {
            $input = str_replace(" ", "", $input);
            if ($input > 0) {
                $price = number_format($input, 2, ".", "");
                return $price;
            } else {
                return $input;
            }
        });
        
        $fenom->addModifier('isFirstOrder', function ($input) use ($modx) {
            $id = (int) $input;
            if ($id > 0) {
                $msOrder = $modx->getObject('msOrder', $id);
                $total_orders = $modx->getCount('msOrder', ['user_id' => $msOrder->get('user_id')]);
                if ($total_orders == 1) return true;
            }
            return false;
        });
        
        $fenom->addModifier('dateRU', function ($input, $strtotime = false) {
            if (!$input) return '';
            $month_arr = [
                '01' => 'января',
                '02' => 'февраля',
                '03' => 'марта',
                '04' => 'апреля',
                '05' => 'мая',
                '06' => 'июня',
                '07' => 'июля',
                '08' => 'августа',
                '09' => 'сентября',
                '10' => 'октября',
                '11' => 'ноября',
                '12' => 'декабря'
            ];
            
            if ($strtotime) {
                $time = strtotime($input);
            } else {
                $time = $input;
            }
            $month = strftime('%m', $time);
            $month = $month_arr[$month];
            $day = strftime('%d', $time);
            $year = strftime('%Y', $time);
                
            return "$day $month, $year";
        });
        break;
}