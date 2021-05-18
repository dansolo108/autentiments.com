Ext.onReady(function() {
    MODx.load({ xtype: 'msmc-page-multicurrency'});
});

MsMC.page.MultiCurrency = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'msmc-panel-multicurrency'
            ,renderTo: 'msmc-panel-multicurrency-div'
        }]
    });
    MsMC.page.MultiCurrency.superclass.constructor.call(this,config);
};
Ext.extend(MsMC.page.MultiCurrency,MODx.Component);
Ext.reg('msmc-page-multicurrency',MsMC.page.MultiCurrency);

