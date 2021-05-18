msProductRemains.page.Remains = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'mspr-panel-remains'
			,renderTo: 'mspr-panel-remains-div'
		}]
	});
	msProductRemains.page.Remains.superclass.constructor.call(this, config);
};
Ext.extend(msProductRemains.page.Remains, MODx.Component);
Ext.reg('mspr-page-remains', msProductRemains.page.Remains);

msProductRemains.panel.Remains = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,baseCls: 'modx-formpanel'
		,items: [{
			html: '<h2>msProductRemains :: '+_('mspr_remains')+'</h2>'
			,border: false
			,cls: 'modx-page-header container'
		},{
			xtype: 'modx-tabs'
			,bodyStyle: 'padding: 10px'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,activeItem: 0
			,hideMode: 'offsets'
			,items: [{
				title: _('mspr_remains')
				,items: [{
					html: _('mspr_remains_intro')
					,border: false
					,bodyCssClass: 'panel-desc'
					,bodyStyle: 'margin-bottom: 10px'
				},{
					xtype: 'mspr-grid-remains'
					,preventRender: true
				}]
			}]
		}]
	});
	msProductRemains.panel.Remains.superclass.constructor.call(this, config);
};
Ext.extend(msProductRemains.panel.Remains, MODx.Panel);
Ext.reg('mspr-panel-remains', msProductRemains.panel.Remains);