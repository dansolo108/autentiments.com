var amocrm = function (config) {
    config = config || {};
    amocrm.superclass.constructor.call(this, config);
};
Ext.extend(amocrm, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('amocrm', amocrm);

amocrm = new amocrm();