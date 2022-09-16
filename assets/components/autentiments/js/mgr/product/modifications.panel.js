var autentimentsPanel = function(config) {
	config = config || {};
	autentimentsPanel.superclass.constructor.call(this,config);
};
Ext.extend(autentimentsPanel,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config:{},view:{},plugin:{}
});
Ext.reg('autentimentspanel',autentimentsPanel);
autentimentsPanel = new autentimentsPanel();
autentimentsPanel.PanelSpacer = { html: '<br />' ,border: false, cls: 'mspr-panel-spacer' };