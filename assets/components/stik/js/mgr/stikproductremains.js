var msProductRemains = function(config) {
	config = config || {};
	msProductRemains.superclass.constructor.call(this,config);
};
Ext.extend(msProductRemains,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config:{},view:{},plugin:{}
});
Ext.reg('msproductremains',msProductRemains);
msProductRemains = new msProductRemains();
msProductRemains.PanelSpacer = { html: '<br />' ,border: false, cls: 'mspr-panel-spacer' };