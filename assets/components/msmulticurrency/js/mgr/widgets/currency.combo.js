MsMC.combo.Currency = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name'
        , valueField: 'id'
        , fields: ['id', 'name', 'code', 'symbol_left', 'symbol_right']
        , name: 'cid'
        , hiddenName: config.name || 'cid'
        , editable: true
        , minChars: 2
        , url: MsMC.config.connectorUrl
        , baseParams: {
            action: 'mgr/multicurrency/getList',
            combo: true
        }
        ,tpl: new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item"><span style="font-weight: bold">{name}</span>'
            , '<tpl if="code"> - <span style="font-style:italic">{code}</span></tpl>'
            , '</div></tpl>')
    });
    MsMC.combo.Currency.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.combo.Currency, MODx.combo.ComboBox);
Ext.reg('msmc-combo-currency', MsMC.combo.Currency);
