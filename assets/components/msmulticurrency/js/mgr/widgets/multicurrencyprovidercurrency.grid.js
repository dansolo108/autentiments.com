MsMC.grid.MultiCurrencyProviderCurrency = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'msmc-grid-multicurrencyprovider-currency';
    }

    Ext.applyIf(config, {
        url: MsMC.config.connectorUrl
        , baseParams: {
            action: 'mgr/multicurrencyprovider/currency/getList'
            , provider: config.provider
        }
        , pageSize: 15
        , autoExpandColumn: 'code'
    });
    MsMC.grid.MultiCurrencyProviderCurrency.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.grid.MultiCurrencyProviderCurrency, MsMC.grid.Default, {
    getFields: function () {
        return ['code', 'course'];
    }
    , getColumns: function () {
        return [{
            header: _('msmulticurrency.header_provider_code')
            , dataIndex: 'code'
        }, {
            header: _('msmulticurrency.header_provider_course')
            , dataIndex: 'course'
        }];
    }
});
Ext.reg('msmc-grid-multicurrencyprovider-currency', MsMC.grid.MultiCurrencyProviderCurrency);