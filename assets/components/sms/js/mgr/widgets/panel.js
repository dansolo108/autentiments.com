sms.panel.Main = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('sms_pagetitle') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: this.getTabs(),
        }]
    });
    sms.panel.Main.superclass.constructor.call(this, config);
};
Ext.extend(sms.panel.Main, MODx.Panel, {
    getTabs: function () {
        var tabs = [];
        tabs.push({
            title: _('sms_tab_send'),
            layout: 'anchor',
            items: [{
                html: _('sms_tab_send_intro'),
                cls: 'panel-desc',
            }, {
                xtype: 'sms-grid-send',
                cls: 'main-wrapper',
            }]
        });
        return tabs;
    }
});
Ext.reg('sms-panel-main', sms.panel.Main);