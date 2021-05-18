MsMC.panel.MultiCurrency = function (config) {
    config = config || {};
    Ext.apply(config, {
        border: false
        , baseCls: 'modx-formpanel'
        , cls: 'container'
        , items: [{
            html: '<h2>' + _('msmulticurrency.multicurrency_title') + '</h2>'
            , border: false
            , cls: 'modx-page-header'
        }, {
            xtype: 'modx-tabs'
            ,id: 'msmc-tabs'
            , defaults: {border: false, autoHeight: true}
            , border: true
            ,stateEvents: ['tabchange']
            ,getState:function() {return { activeTab:this.items.indexOf(this.getActiveTab())};}
            , items: [{
                title: _('msmulticurrency.tab.multicurrencysetmember')
                ,defaults: { autoHeight: true }
                ,items: [{
                    xtype: 'msmc-grid-multicurrencysetmember'
                    ,cls: 'main-wrapper'
                }]
            },{
                title: _('msmulticurrency.tab.multicurrencyset')
                , items: [{
                    xtype: 'msmc-grid-multicurrencyset'
                    , cls: 'main-wrapper'
                }]
            },{
                title: _('msmulticurrency.tab.multicurrency')
                , items: [{
                    xtype: 'msmc-grid-multicurrency'
                    , cls: 'main-wrapper'
                }]
            },{
                title: _('msmulticurrency.tab.multicurrencyprovider')
                , items: [{
                    xtype: 'msmc-grid-multicurrencyprovider'
                    , cls: 'main-wrapper'
                }]
            }]
        }]
    });
    MsMC.panel.MultiCurrency.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.panel.MultiCurrency, MODx.Panel);
Ext.reg('msmc-panel-multicurrency', MsMC.panel.MultiCurrency);