if (typeof(ms2DeliveryCost) != 'object') {
	var ms2DeliveryCost = {};
}
ms2DeliveryCost.block = '#deliveries';

jQuery(document).ready(function($) {
	ms2DeliveryCost.required = ms2DeliveryCost.required.split(',');
	ms2DeliveryCost.requiredField = '';

	ms2DeliveryCost.required.forEach(function(item, i) {
		ms2DeliveryCost.requiredField += '[name='+item+'],';
	})
	ms2DeliveryCost.requiredField = ms2DeliveryCost.requiredField.slice(0, -1);

	ms2DeliveryCost.checkRequired = function () {
	    var checkRequiredResult = true;
		this.required.forEach(function(item) {
			var val = $('[name='+item+']').val();
// 			console.log(val);
			if (val.trim() == '') {
				checkRequiredResult = false;
				return ;
			}
		});
		return checkRequiredResult;
	}
	ms2DeliveryCost.reload = function() {
		this.loadEffect();
		var self = this;
		setTimeout(function() {
			$.get('/assets/components/stik/getAjaxDeliveryCost.php', {deliveryGetCost: 'get', language: $('html').attr('lang')}, function(data) {
				$(self.block).html(data);
				// console.log('updated');
				setOrderRates();
				self.loadEffect(1);
			}, 'html');
		}, 2000);
	}
	ms2DeliveryCost.loadEffect = function(show) {
		if (!show) {
            $('.dl-ajax-loader', this.block).addClass('enabled');
            $(this.block).addClass('loading');
			//$(this.block).css('opacity', '0.5');
		} else {
            $('.dl-ajax-loader', this.block).removeClass('enabled');
            $(this.block).removeClass('loading');
			//$(this.block).css('opacity', '1');
		}
	}
	ms2DeliveryCost.init = function() {
		if (this.checkRequired()) {
			this.reload();
		}
	}
	$(ms2DeliveryCost.requiredField).change(function(event) {
		ms2DeliveryCost.init();
	});
	ms2DeliveryCost.init();
	miniShop2.Callbacks.add('Cart.change.response.success', 'ms2DeliveryCost', function(response) {
		ms2DeliveryCost.reload();
	});
});