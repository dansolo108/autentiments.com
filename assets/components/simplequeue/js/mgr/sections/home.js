simpleQueue.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'simplequeue-panel-home', renderTo: 'simplequeue-panel-home-div'
		}]
	});
	simpleQueue.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(simpleQueue.page.Home, MODx.Component);
Ext.reg('simplequeue-page-home', simpleQueue.page.Home);