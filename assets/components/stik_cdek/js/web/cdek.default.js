jQuery(document).ready(function($) {
	var ajaxId = '#cdek2_map_ajax';
	var cdekStatus = $('#ms_cdek2_status');
	var cdek_ids = '2';
	var cdek_arr = cdek_ids.split(',');

	if (checkCDEK()) {
		var deliveryId = $('[name=delivery]:checked').val();
		resetStatus(deliveryId);
	}

	$(document).on('change', '[name=delivery]', function(event) {
		cdekStatus.fadeOut();
		var deliveryId = $(this).val();
		if (checkCDEK()) {
			getStatus();
		}
	});


	$(document).on('change', '[name=city]', function(event) {
		if (checkCDEK()) {
			clearDelivery();
		}
	});
	$(document).on('change', '[name=index]', function(event) {
		if (checkCDEK()) {
			clearDelivery();
		}
	});
	$('.msOrder').keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});
	function clearDelivery() {
		setTimeout(function() {
			deliveryId = $('[name=delivery]:checked').val();
			//$('[name=delivery]').prop('checked', false);
			//miniShop2.Order.add('delivery', '0');
			cdekStatus.fadeOut();

			resetStatus(deliveryId);
		}, 300)
	}

	function getStatus() {
		cityVal = $('[name=city]').val();
		indexVal = $('[name=index]').val();
		if (cityVal && indexVal) {
        // 	miniShop2.Message.info('Сумма доставки была обновлена');
        	if ($(ajaxId).length) {
        		//проверять id
        		map_reload();
        	}
		} else {
			miniShop2.Message.error('Не заполнены необходимые поля для расчета');
		}
	}
	function checkCDEK() {
		var deliveryId = $('[name=delivery]:checked').val();
		if (cdek_arr.indexOf(deliveryId) !== -1) {
			return true;
		}
		if ($(ajaxId).length) {
    		$(ajaxId).fadeOut();
    	}
		return false;
	}
	function resetStatus(deliveryId) {
		setTimeout(function() {
			//$('[name=delivery][value='+deliveryId+']').prop('checked', true);
			//miniShop2.Order.add('delivery', deliveryId);
			getStatus();
		},300)
	}
	$('.msOrder').submit(function(event) {
		if ($('[name=delivery]:checked').length == 0) {
			miniShop2.Message.error('Вы не выбрали способ доставки');
			return false;
		}
	});
});