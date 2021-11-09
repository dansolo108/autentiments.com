Ext.namespace('msProductRemains.utils');

msProductRemains.utils.productLink = function(val,cell,row) {
	if (!val || !row.data['product_id']) {return val;}

	var action = MODx.action ? MODx.action['resource/update'] : 'resource/update';
	var url = 'index.php?a='+action+'&id='+row.data['product_id'];

	return '<a href="' + url + '" target="_blank" class="ms2-link">' + val + '</a>';
};

msProductRemains.utils.jsonArray = function(val,cell,row) {
	if (!val || val == '[]' || val == '[""]') {return '';}

	var vals = Ext.decode(val);
	val = vals.toString();
	
	return val;
};

msProductRemains.utils.bool = function(value) {
	var color, text;
	if (value == 0 || value == false || value == undefined) {
		color = 'green';
		text = _('no');
	}
	else {
		color = 'red';
		text = _('yes');
	}

	return String.format('<span class="{0}">{1}</span>', color, text);
};

msProductRemains.utils.defined = function(value) {
	if ( value === undefined || value === null || value === '' || value === '[]' || value == '[""]' ) {
		return '<span class="red">'+_('no')+'</span>';
	}
	return value;
};
