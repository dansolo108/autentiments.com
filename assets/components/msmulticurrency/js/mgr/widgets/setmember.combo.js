MsMC.combo.SetMember = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'currency_name'
        , valueField: 'cid'
        , fields: ['cid', 'currency_name', 'currency_code', 'currency_symbol_left', 'currency_symbol_right','val']
        , name: 'currency_id'
        , hiddenName: config.name || 'currency_id'
        , editable: true
        , minChars: 2
        , url: MsMC.config.connectorUrl
        , baseParams: {
            action: 'mgr/multicurrencysetmember/getList',
            exclude: config.exclude || 0,
            sid: config.sid || 1,
            combo: true
        }
        ,tpl: new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item"><span style="font-weight: bold">{currency_name}</span>'
            , '<tpl if="currency_code"> - <span style="font-style:italic">{currency_code}</span></tpl>'
            , '<br />{val}</div></tpl>')
    });
    MsMC.combo.SetMember.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.combo.SetMember, MODx.combo.ComboBox, {
    reload: function (sid) {
        this.baseParams.sid = sid;
        this.getStore().reload({params: this.baseParams});
    }
});
Ext.reg('msmc-combo-set-member', MsMC.combo.SetMember);
