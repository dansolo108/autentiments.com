MsMC.panel.CurrencyCheckboxGroup = function (config) {
    config = config || {};
    Ext.apply(config, {
        border: false
        , baseCls: 'modx-formpanel'
        , cls: 'container msmc-checkboxgroup-panel'
        , items: []
        , listeners: {
            /*render: {
                fn: this.setup, scope: this
            }*/
        }
    });
    MsMC.panel.CurrencyCheckboxGroup.superclass.constructor.call(this, config);
    this.on('render', this.setup);
};
Ext.extend(MsMC.panel.CurrencyCheckboxGroup, MODx.Panel, {
    setup: function () {
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrency/getlist',
                combo: true
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.removeAll();
                        this.add({
                            html: '<strong>'+_('msmulticurrency.set.label_currencies')+'</strong>'
                        });
                        Ext.each(r.results || [], function (data) {
                            this.addItem(data);
                        }, this);
                    }, scope: this
                },
                failure: {
                    fn: function (e) {
                        MODx.msg.alert(_('error'), e.message);
                    }, scope: this
                },
            }
        });
    },
    addItem: function (data) {
        var col = {
            xtype: 'xcheckbox',
            hideLabel: true,
            name: 'currency[' + data.id + ']',
            inputValue: 1,
            checked: true,
            value: 1,
            boxLabel: data.name,
        };
        this.add(col);
        this.doLayout();
    }
});
Ext.reg('msmc-currency-checkboxgroup', MsMC.panel.CurrencyCheckboxGroup);

