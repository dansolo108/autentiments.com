var modMaxma = function (config) {
    config = config || {};
    modMaxma.superclass.constructor.call(this, config);
};
Ext.extend(modMaxma, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('modmaxma', modMaxma);

modMaxma = new modMaxma();