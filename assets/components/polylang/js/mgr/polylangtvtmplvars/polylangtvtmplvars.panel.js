Polylang.panel.PolylangTvTmplvars = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false,
        baseCls: 'modx-formpanel',
        cls: 'container',
        items: [{
            html: '<h2>'+_('polylang_polylangtvtmplvars_title')+'</h2>',
            border: false,
            cls: 'modx-page-header'
        }, {
            xtype: 'modx-tabs',
            id: 'polylang-polylangtvtmplvars-tabs',
            defaults: { border: true ,autoHeight: true },
            stateEvents: ['tabchange'],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },
            items: [{
                title: _('polylang_tab_polylangtvtmplvars'),
                defaults: { autoHeight: true },
                items: [{
                 xtype: 'polylang-grid-polylangtvtmplvars',
                 cls: 'main-wrapper',
                 preventRender: true
                 }]
            }]
        }]
    });
    Polylang.panel.PolylangTvTmplvars.superclass.constructor.call(this,config);
};
Ext.extend(Polylang.panel.PolylangTvTmplvars,MODx.Panel);
Ext.reg('polylang-panel-polylangtvtmplvars',Polylang.panel.PolylangTvTmplvars);