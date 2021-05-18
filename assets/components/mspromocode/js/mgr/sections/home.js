msPromoCode.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'mspromocode-panel-home',
			renderTo: 'mspromocode-panel-home-div'
		}]
	});
	msPromoCode.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.page.Home, MODx.Component);
Ext.reg('mspromocode-page-home', msPromoCode.page.Home);