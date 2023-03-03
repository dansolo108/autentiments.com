amocrm.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'amocrm-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('amocrm') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('amocrm_items'),
                layout: 'anchor',
                items: [{
                    html: _('amocrm_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'amocrm-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    amocrm.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(amocrm.panel.Home, MODx.Panel);
Ext.reg('amocrm-panel-home', amocrm.panel.Home);
