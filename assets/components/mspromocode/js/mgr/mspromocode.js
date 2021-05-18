var msPromoCode = function (config) {
    config = config || {};
    msPromoCode.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, form: {}, panel: {}, formpanel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('mspromocode', msPromoCode);

msPromoCode = new msPromoCode();