var sms = function (config) {
    config = config || {};
    sms.superclass.constructor.call(this, config);
};
Ext.extend(sms, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('sms', sms);

sms = new sms();