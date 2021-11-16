sms.page.Main = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'sms-panel-main',
            renderTo: 'sms-panel-main-div'
        }],
    });
    sms.page.Main.superclass.constructor.call(this, config);
};
Ext.extend(sms.page.Main, MODx.Component, {
});
Ext.reg('sms-page-main', sms.page.Main);