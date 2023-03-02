var msoptionsprice = function (config) {
	config = config || {};
	msoptionsprice.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, tools: {}
});
Ext.reg('msoptionsprice', msoptionsprice);

msoptionsprice = new msoptionsprice();