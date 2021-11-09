var simpleQueue = function (config) {
	config = config || {};
	simpleQueue.superclass.constructor.call(this, config);
};
Ext.extend(simpleQueue, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('simplequeue', simpleQueue);

simpleQueue = new simpleQueue();