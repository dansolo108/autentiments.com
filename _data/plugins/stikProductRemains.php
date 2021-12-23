id: 18
source: 1
name: stikProductRemains
category: stik
properties: 'a:0:{}'

-----

$stikProductRemains = $modx->getService('stikProductRemains','stikProductRemains', $modx->getOption('core_path').'components/stik/model/', []);
if (!($stikProductRemains instanceof stikProductRemains) || !$stikProductRemains->active) return '';

switch ( $modx->event->name ) {

	case 'msOnCreateOrderProduct':
		$mode = $modx->getOption('mode', $scriptProperties, null);
		$opid = $modx->getOption('id', $scriptProperties, 0);
		if ( !is_numeric($opid) ) break;
		$product = $modx->getObject('msOrderProduct', $opid);
		if ( !is_object($product) ) break;
		$order = $modx->getObject('msOrder', $product->get('order_id'));
		if ( $stikProductRemains->order_status > $order->get('status') ) break;
// 		$stikProductRemains->saveRemains(array_merge($product->get('options')?:array(), array(
// 			'product_id' => $product->get('product_id')
// 			,'count' => -$product->get('count')
// 		)));
		break;

	case 'msOnBeforeCreateOrderProduct':
	case 'msOnBeforeUpdateOrderProduct':
		$op_data = $modx->getOption('data', $scriptProperties, array());
		$product_id = $modx->getOption('product_id', $op_data, 0);
		if ( !is_numeric($product_id) ) break;
		$count = $modx->getOption('count', $op_data, 0);
		$mode = $modx->getOption('mode', $scriptProperties, null);
		if ( $mode === 'upd' ) {
			$res = $modx->getObject('msOrderProduct', $modx->getOption('id', $scriptProperties, 0));
			$count -= $res->get('count');
		}
		$op_options = $modx->getOption('options', $op_data, array());
		$options = array();
		foreach ( explode(',', $stikProductRemains->options) as $opt ) {
			$tmp = $modx->getOption(trim($opt), $op_options, null);
			if ( !empty($tmp) ) $options[trim($opt)] = $tmp;
			if ( !$stikProductRemains->check_options ) continue;
			if ( empty($product) ) $product = $modx->getObject('msProduct', $product_id);
			$propt = $product->get(trim($opt));
			if ( !empty($propt[0]) && ( empty($tmp) || !in_array($tmp, $propt) ) ) {
				$modx->event->output( $modx->lexicon('stikpr_choose_'.trim($opt)) . ' ' );
				$opterror = true;
			}
		}
		if ( $opterror ) break;
		if ( $count > 0 && $stikProductRemains->active_bcstatus ) {
			$remains = $stikProductRemains->getRemains(array_merge($options,array('id'=>$product_id,'strong'=>false)));
			if ( $remains < $count ) {
				if ( empty($product) ) $product = $modx->getObject('msProduct', $product_id);
				$modx->event->output( $modx->lexicon('stikpr_not_enough', array(
					'product' => $product->get('pagetitle'),
					'count' => $count,
					'remains' => $remains
				)));
			}
		}
// 		if ( $mode === 'upd' && $count != 0 )
// 			$stikProductRemains->saveRemains(array_merge($options, array(
// 				'product_id' => $product_id
// 				,'count' => -$count
// 			)));
		break;

	case 'msOnBeforeAddToCart':
		if ( !$stikProductRemains->active_before_add ) break;
		$product = $modx->getOption('product', $scriptProperties);
		if ( !is_object($product) ) break;
		$options = $modx->getOption('options', $scriptProperties, array());
		if ( !is_array($options) ) $options = array();
		elseif ( isset($options['mssetincart_exclude']) ) return;
		if ( $stikProductRemains->check_options ) {
			foreach ( explode(',', $stikProductRemains->options) as $opt ) {
				$propt = $product->get(trim($opt));
				if ( !empty($propt[0]) && ( empty($options[trim($opt)]) || !in_array($options[trim($opt)], $propt) ) ) {
					$modx->event->output( $modx->lexicon('stikpr_choose_'.trim($opt)) . ' ' );
					$opterror = true;
				}
			}
			if ( $opterror ) break;
		}
		$count = $modx->getOption('count', $scriptProperties);
		$remains = $stikProductRemains->getRemains(array_merge($options,array('id'=>$product->get('id'),'strong'=>true)));
        $values = $modx->event->returnedValues;
        // устанавливаем цену оффера
        $offerPrices = $stikProductRemains->getOfferPrices($product->get('id'), $options['color'], $options['size']);
        if ($offerPrices) {
            $product->set('price', $offerPrices['price']);
            $product->set('old_price', $offerPrices['old_price']);
            $options['old_price'] = $offerPrices['old_price'];
        }
        $values['options'] = array_merge($options, ['max_count' => $remains]);
        $modx->event->returnedValues = $values;
		if ( $remains < $count )
			$modx->event->output( $modx->lexicon('stikpr_not_enough', array(
				'product' => $product->get('pagetitle'),
				'count' => $count,
				'remains' => $remains
			)));
		break;

	case 'msOnBeforeChangeInCart':
		if ( !$stikProductRemains->active_before_add ) break;
		$cart = $modx->getOption('cart', $scriptProperties);
		if ( !is_object($cart) ) break;
		$goods = $cart->get();
		$key = $modx->getOption('key', $scriptProperties);
		if ( !is_numeric($goods[$key]['id']) ) break;
        $miniShop2 = $modx->getService('miniShop2');
        $miniShop2->initialize($modx->context->key);
        $order = $miniShop2->order->get();
		$count = $modx->getOption('count', $scriptProperties);
		$options = ( is_array($goods[$key]['options']) ) ? $goods[$key]['options'] : [];
		$params = [
		    'id' => $goods[$key]['id'],
		    'strong' => false
		];
		$remains = $stikProductRemains->getRemains(array_merge($options,$params));
		if ( $remains < $count ) {
			$product = $modx->getObject('msProduct', $goods[$key]['id']);
			$modx->event->output( $modx->lexicon('stikpr_not_enough', array(
				'product' => $product->get('pagetitle'),
				'count' => $count,
				'remains' => $remains
			)));
		}
		break;

	case 'msOnBeforeChangeOrderStatus':
		$status = $modx->request->parameters['POST']['status'];
		$order = $modx->getOption('order', $scriptProperties);
		if ( !is_object($order) ) break;
		if ( $stikProductRemains->order_status == $status && $stikProductRemains->active_bcstatus ) {
			foreach ( $order->getMany('Products') as $product ) {
				$remains = $stikProductRemains->getRemains(array_merge($product->get('options')?:array(),array('id'=>$product->get('product_id'),'strong'=>false)));
				if ( $remains < $product->get('count') ) {
					$res = $modx->getObject('msProduct', $product->get('product_id'));
					return $modx->event->output( $modx->lexicon('stikpr_error_before_order', array('product'=>$res->get('pagetitle'))) );
				}
			}
		} elseif ( $stikProductRemains->orderback_status == $status && $stikProductRemains->order_status <= $order->get('status') ) {
			foreach ( $order->getMany('Products') as $product ) {
				// $stikProductRemains->saveRemains(array_merge($product->get('options')?:array(), array(
				// 	'product_id' => $product->get('product_id')
				// 	,'count' => $product->get('count')
				// )));
			}
		}
		break;

	case 'msOnBeforeRemoveOrder':
		$order_id = $modx->getOption('id', $scriptProperties, 0);
		if ( !is_numeric($order_id) ) break;
		$order = $modx->getObject('msOrder', $order_id);
		if ( !is_object($order) ) break;
		if ( $stikProductRemains->order_status > $order->get('status') || $stikProductRemains->orderback_status == $order->get('status') ) break;
// 		foreach ( $order->getMany('Products') as $product ) {
// 			$stikProductRemains->saveRemains(array_merge($product->get('options')?:array(), array(
// 				'product_id' => $product->get('product_id')
// 				,'count' => $product->get('count')
// 			)));
// 		}
		break;

	case 'msOnBeforeRemoveOrderProduct':
		$opid = $modx->getOption('id', $scriptProperties, 0);
		if ( !is_numeric($opid) ) break;
		$product = $modx->getObject('msOrderProduct', $opid);
		if ( !is_object($product) ) break;
		$order = $modx->getObject('msOrder', $product->get('order_id'));
		if ( !is_object($order) ) break;
		if ( $stikProductRemains->order_status > $order->get('status') || $stikProductRemains->orderback_status == $order->get('status') ) break;
// 		$stikProductRemains->saveRemains(array_merge($product->get('options')?:array(), array(
// 			'product_id' => $product->get('product_id')
// 			,'count' => $product->get('count')
// 		)));
		break;

	case 'msOnChangeOrderStatus':
	    // списываем остатки при оплате
		$status = $modx->getOption('status', $scriptProperties, 0);
		if ($status != 2) break;
		$order = $modx->getOption('order', $scriptProperties);
		if (!is_object($order)) break;
		foreach ($order->getMany('Products') as $product) {
			$stikProductRemains->saveRemains(array_merge($product->get('options') ?: [], [
			    'store_id' => 1,
				'product_id' => $product->get('product_id')
				,'count' => -$product->get('count')
			]));
		}
		break;

	case 'msOnSubmitOrder':
		if ( $stikProductRemains->active_before_order || ( $stikProductRemains->active_bcstatus && $_POST['ms2_action'] === 'order/submit' ) ) {
			$order = $modx->getOption('order', $scriptProperties);
			if ( !is_object($order) ) break;
			foreach ( $order->ms2->cart->get() as $product ) {
				$options = ( is_array($product['options']) ) ? $product['options'] : array();
				$remains = $stikProductRemains->getRemains(array_merge($options,array('id'=>$product['id'],'strong'=>false)));
				if ( $remains < $product['count'] ) {
					$res = $modx->getObject('msProduct', $product['id']);
					$modx->event->output( $modx->lexicon('scpr_error_before_order', array('product'=>$res->get('pagetitle'))) . ' ' );
				}
			}
		}
		break;

	case 'OnDocFormPrerender':
		$mode = $modx->getOption('mode', $scriptProperties);
		if ($mode != 'upd') break;
		$res = $modx->getObject("modResource", $scriptProperties['id']);
		if ( !$res || $res->get('class_key') != 'msProduct' ) break;
		$modx->controller->addLexiconTopic('stik:manager');
		$modx->controller->addJavascript($stikProductRemains->config['jsUrl'].'mgr/stikproductremains.js?v=0.0.1');
		$modx->controller->addLastJavascript($stikProductRemains->config['jsUrl'].'mgr/product/remains.panel.js?v=0.0.1');
		$modx->controller->addLastJavascript($stikProductRemains->config['jsUrl'] . 'mgr/misc/stikpr.utils.js?v=0.0.1');
		$stikProductRemains->loadPlugins();
		$grid_fields = array_map('trim', explode(',', $modx->getOption('stikpr_product_grid_fields', null, 'id,store_id,size,color,remains,hide', true)));
		$modx->controller->addHtml(str_replace('					', '', '
			<script type="text/javascript">
				msProductRemains.config.connector_url = \'' . $stikProductRemains->config['connectorUrl'] . '\';
				msProductRemains.config.product_grid_fields = ' . $modx->toJSON($grid_fields) . ';
				var tabs = [\'minishop2-product-settings-panel\', \'minishop2-product-tabs\'];
				for (var i=0; i<tabs.length; i++) {
					Ext.ComponentMgr.onAvailable(tabs[i], function() {
						this.on(\'beforerender\', function() {
							this.add({
								title: _(\'stikpr_title\')
								,hideMode: \'offsets\'
								,items: [
									{
										html: _(\'stikpr_intro_msg\'),
										cls: \'modx-page-header container\',
										border: false
									},{
										xtype: \'mspr-grid-productremains\',
										cls: (this.id == \'minishop2-product-tabs\' ? \'main-wrapper\' : \'\'),
										style: (this.id == \'minishop2-product-tabs\' ? \'padding-top: 0px;\' : \'\')
									}
								]
							});
						});
					});
				}
			</script>
		'));
		break;

    case 'OnLoadWebDocument':
        // регистрируем переменные для js
        $modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var ms2_frontend_currency = "'.$modx->getPlaceholder('msmc.symbol_right').'",
                stik_order_delivery_not_calculated = "'.$modx->lexicon('stik_order_delivery_not_calculated').'",
                stik_order_delivery_free = "'.$modx->lexicon('stik_order_delivery_free').'",
                stik_order_delivery_impossible_calculate = "'.$modx->lexicon('stik_order_delivery_Impossible_calculate').'",
                stik_order_need_to_accept_terms = "'.$modx->lexicon('stik_order_need_to_accept_terms').'",
                stik_order_fill_required_fields = "'.$modx->lexicon('stik_order_fill_required_fields').'",
                stik_basket_not_enough = "'.$modx->lexicon('stik_basket_not_enough').'",
                stik_declension_bonuses_js = '.$modx->lexicon('stik_declension_bonuses_js').',
                intlTelErrorMap = '.$modx->lexicon('stik_intltel_errors_js').';
        </script>');
        break;

	/*case 'OnDocFormSave':
		if ($resource->class_key !== "msProduct") break;
		$remopt = $stikProductRemains->getProductOptionsCombs(array(
			'product_id' => $id
		));
		foreach ( $remopt as $var ) {
			$vars = $modx->getCount('stikRemains', array(
				'product_id' => $id
				,'options'   => $modx->toJSON($var)
			));
			if ( $vars < 1 ) {
				// $new = $modx->newObject('stikRemains', array(
				// 	'product_id' => $id
				// 	,'options'   => $modx->toJSON($var)
				// 	,'remains'   => $stikProductRemains->default_remains
				// ));
				// $new->save();
			}
		}
		break;*/

	case 'OnHandleRequest':
		$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
		if (empty($_REQUEST['stikpr_action']) || ($isAjax && $modx->event->name != 'OnHandleRequest')) break;
		$action = trim($_REQUEST['stikpr_action']);
		switch ($action) {
			case 'remains/get':
			    $product['id'] = @$_POST['product_id'];
				$product['strong'] = false;
				$options = explode(',', $stikProductRemains->options);
				foreach ( $options as $field ) {
				    $product[trim($field)] = @$_POST[trim($field)];
				    if ( !empty($product[trim($field)]) ) {
				        $product['strong'] = true;
				    }
				}
				$response = $stikProductRemains->getRemains($product);
				break;
			case 'sizes/get':
			    $id = (int)$_POST['product_id'];
			    $color = htmlspecialchars($_POST['selected_color']);
                $response = $modx->runSnippet('getRemainsWithAmount', [
                    'tpl' => 'stik.msOptions.size.n.amount',
                    'id' => $id,
                    'color' => $color,
                ]);
				break;
			case 'price/get':
			    $id = (int)$_POST['product_id'];
			    $color = htmlspecialchars($_POST['selected_color']);
			    $size = htmlspecialchars($_POST['selected_size']);
			    if ($id && $color && $size) {
                    $response = json_encode($stikProductRemains->getOfferPrices($id, $color, $size));
			    }
				break;
		}
		if ($isAjax) {
			@session_write_close();
			echo $response;
			exit();
		}
		break;

}