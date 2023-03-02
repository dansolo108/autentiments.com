msoptionsprice.panel.ResourceInject = function (config) {
	config = config || {};

	if (!config.update) {
		config.update = true;
	}

	Ext.apply(config, {
		id: 'msoptionsprice-panel-resource-inject',
		cls: 'msoptionsprice-panel-resource-inject',
		bodyCssClass: 'main-wrapper',
		forceLayout: true,
		deferredRender: false,
		autoHeight: true,
		border: false,
		items: this.getMainItems(config)
	});

	msoptionsprice.panel.ResourceInject.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.panel.ResourceInject, MODx.Panel, {

	getMainItems: function (config) {

		return [{
			xtype: 'panel',
			layout: 'fit',
			items: [{
				xtype: 'msoptionsprice-grid-modification',
				resource: msoptionsprice.config.resource
			}]
		}];
	}

});

Ext.reg('msoptionsprice-panel-resource-inject', msoptionsprice.panel.ResourceInject);

