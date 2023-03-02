Ext.override(miniShop2.window.UpdateOrder, {
    msmcOriginals: {
        getOrderFields: miniShop2.window.UpdateOrder.prototype.getOrderFields,
    },
    getOrderFields: function (config) {
        var fields = this.msmcOriginals.getOrderFields.call(this, config),
            currencyData = this.msmcCurrencyData(config),
            fieldCost = fields[1]['items'][1]['items'][0],
            fieldCartCost = fields[2]['items'][0]['items'][1],
            fieldDeliveryCost = fields[2]['items'][1]['items'][1];
        if (fieldCost && fieldCartCost && currencyData && MsMC.config.currencyField) {
            var currency = MsMC.config.baseCurrencyData[MsMC.config.currencyField];
            if (parseInt(currencyData['cart_user_currency'])) {
                currency = currencyData[MsMC.config.currencyField];
            }
            fieldCost.fieldLabel += ' (' + currency + ')';
            fieldCartCost.fieldLabel += ' (' + currency + ')';
            fieldDeliveryCost.fieldLabel += ' (' + currency + ')';
        }
        return fields;
    },
    msmcCurrencyData: function (config) {
        var data = {};
        if (
            !MsMC.utils.isEmpty(config.record) ||
            !MsMC.utils.isEmpty(config.record.properties) ||
            !MsMC.utils.isEmpty(config.record.properties.msmc)
        ) {
            data = config.record.properties.msmc;
        }
        return data;
    }
});