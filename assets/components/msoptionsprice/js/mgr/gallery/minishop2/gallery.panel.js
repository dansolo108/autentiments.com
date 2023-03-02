msoptionsprice.panel.Gallery = function (config) {
	config = config || {};

	this.panel = MODx.load({
		border: false,
		xtype: 'msoptionsprice-gallery-images-panel',
		cls: 'modx-pb-view-ct msoptionsprice-gallery-images-panel',
		product_id: config.resource.id,
		mid: config.record.id,
		resource: config.resource,
		pageSize: config.pageSize
	});

	Ext.apply(config, {
		border: false,
		baseCls: 'x-panel',
		items: [{
			border: false,
			style: {padding: '5px'},
			layout: 'anchor',
			items: this.panel
		}]
	});
	msoptionsprice.panel.Gallery.superclass.constructor.call(this, config);

	this.on('afterrender', function () {
		var gallery = this;
		window.setTimeout(function () {
			gallery.initialize();
		}, 100);
	});

};
Ext.extend(msoptionsprice.panel.Gallery, MODx.Panel, {

	initialize: function () {
		this.panel.view.getStore().reload();
	}

});
Ext.reg('msoptionsprice-panel-gallery', msoptionsprice.panel.Gallery);