msOptionHexColor.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'msoptionhexcolor-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('msoptionhexcolor') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('msoptionhexcolor_items'),
                layout: 'anchor',
                items: [{
                    html: _('msoptionhexcolor_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'msoptionhexcolor-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    msOptionHexColor.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(msOptionHexColor.panel.Home, MODx.Panel);
Ext.reg('msoptionhexcolor-panel-home', msOptionHexColor.panel.Home);
