var map;
var	mapID = 'cdek2_map';
var	placemark;
var	pointClass = '.cdek2_map-point';
var ajaxId = '#cdek2_map_ajax';

ymaps.ready(init_map);

function init_map() {
	var pointstart = $(document).find('#'+mapID).data('start');
	if (!pointstart) {
		return false;
	}
	var allPoints = $(document).find('#'+mapID).data('coords').split(',');
	var pointsArr = [];

	allPoints.forEach(function(item) {
		tmp = item.split('|');
		pointsArr.push(tmp.reverse());
	});

    ymaps.ready(function () {
    	getMap(pointstart, pointsArr);
    });
	
	function getMap (center, arr) {
		var center = center.split(',').reverse();
		map = new ymaps.Map(mapID, {
	        center: center, 
	        zoom: 11
	    });

	    arr.forEach(function(item) {
		    placemark = new ymaps.Placemark(item);
		    placemark.events.add('click', function(event) {
		    	var obj = event.get('target');
		    	var tmp = obj.geometry._coordinates;
		    	var str = tmp[1]+','+tmp[0];
		    	var $item = $('.cdek2_map_container').find('[data-coord="'+str+'"]');
		    	$item.click();
		    	$item.get(0).scrollIntoView();
		    });
	        map.geoObjects.add(placemark);
	    });
	}

}
$(document).on('click', pointClass, function(event) {
	var coord = $(this).data('coord').split(',').reverse();
	var name = $(this).data('name');
	miniShop2.Order.add('point', name);
	$('.point-address').text(name);
	miniShop2.Message.success('Заказ будет доставлен на '+name);

	map.setCenter(coord, 14);
	$(pointClass).removeClass('is-active');
	$(this).addClass('is-active');
});

function map_reload() {
	if (map) {
		map.destroy();
		//console.log(map);
	}

	setTimeout(function() {
		$.get('/assets/components/stik_cdek/cdekDeliveryPointsAjax.php', function(data) {
				$(ajaxId).html(data).fadeIn();
				init_map();
				if ($(document).find(ajaxId).find(pointClass).length) {
					miniShop2.Message.success('Пункты самовывоза обновлены');
				}
		}, 'html');
	}, 500);
}