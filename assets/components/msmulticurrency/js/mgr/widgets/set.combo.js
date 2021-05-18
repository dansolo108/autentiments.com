MsMC.combo.SetCurrency = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name'
        , valueField: 'id'
        , fields: ['id', 'name']
        , name: 'sid'
        , hiddenName: config.name || 'sid'
        , editable: true
        , pageSize: 5
        , url: MsMC.config.connectorUrl
        , baseParams: {
            action: 'mgr/multicurrencyset/getList',
            combo: true
        }
    });
    MsMC.combo.SetCurrency.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.combo.SetCurrency, MODx.combo.ComboBox);
Ext.reg('msmc-combo-set-currency', MsMC.combo.SetCurrency);
