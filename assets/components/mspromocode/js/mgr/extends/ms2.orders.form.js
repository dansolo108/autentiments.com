msPromoCode.formpanel.ms2Orders = function (config) {
    Ext.apply(config, {});
    msPromoCode.formpanel.ms2Orders.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.formpanel.ms2Orders, Ext.ComponentMgr.types['minishop2-form-orders'], {
    getRightFields: function (config) {
        var fields = msPromoCode.formpanel.ms2Orders.superclass.getRightFields.call(this, config);
        var tmp = fields[0];
        fields.shift();
        fields.unshift(tmp, [
            {
                xtype: 'textfield',
                id: config.id + '-mspromocode',
                name: 'promocode',
                emptyText: _('mspromocode_field_ms2_promocode'),
                // listeners: {
                //     select: {
                //         fn: function () {
                //             this.fireEvent('change')
                //         }, scope: this
                //     }
                // },
            }
        ]);

        return fields;
    },
});
Ext.reg('minishop2-form-orders', msPromoCode.formpanel.ms2Orders);