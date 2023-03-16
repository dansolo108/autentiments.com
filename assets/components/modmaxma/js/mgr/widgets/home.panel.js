modMaxma.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'modmaxma-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('modmaxma') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('modmaxma_items'),
                layout: 'anchor',
                items: [{
                    html: _('modmaxma_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'modmaxma-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    modMaxma.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(modMaxma.panel.Home, MODx.Panel);
Ext.reg('modmaxma-panel-home', modMaxma.panel.Home);
