Ext.onReady(function () {
    Ext.override(miniShop2.panel.Product, {
        originals: {
            getProductFields: miniShop2.panel.Product.prototype.getProductFields,
            setup: miniShop2.panel.Product.prototype.setup,
        },
        setup: function () {
            if (!parseInt(MsMC.config.showInProduct)) return;
            this.originals.setup.call(this);
            var currency = Ext.getCmp('modx-resource-currency-id');
            currency.getStore().on('load', this.updatePrices, this);
        },
        getProductFields: function (config) {
            var original = this.originals.getProductFields.call(this, config),
                items = original.items[0].items[0].items;
            if (!parseInt(MsMC.config.showInProduct)) return original;
            items.push(this.getCurrencyFields(config));
            return original;
        },
        getCurrencyFields: function (config) {
            return {
                title: _('ms2_product_msmc')
                , xtype: 'fieldset'
                , cls: 'msmc-fieldset'
                , style: 'padding-top: 5px'
                , collapsible: true
                , stateId: 'ms2-product-msmc'
                , stateful: true
                , stateEvents: ['collapse', 'expand']
                , items: [{
                    xtype: 'msmc-combo-set-currency'
                    , fieldLabel: _('ms2_product_currency_set')
                    , description: '<b>[[*currency_set_id]]</b>'
                    , anchor: '100%'
                    , name: 'currency_set_id'
                    , value: config.record.currency_set_id || 1
                    , listeners: {
                        select: {
                            fn: this.onChangeSetCurrency, scope: this
                        }
                    }
                }, {
                    xtype: 'label',
                    html: _('ms2_product_currency_set_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msmc-combo-set-member'
                    , fieldLabel: _('ms2_product_currency')
                    , id: 'modx-resource-currency-id'
                    , description: '<b>[[*currency_id]]</b>'
                    , anchor: '100%'
                    , name: 'currency_id'
                    , exclude: MsMC.config.baseCurrency.id
                    , value: config.record.currency_id || 0
                    , sid: config.record.currency_set_id || 1
                    , listeners: {
                        select: {
                            fn: this.updatePrices, scope: this
                        }
                    }
                }, {
                    xtype: 'label',
                    html: _('ms2_product_currency_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'numberfield'
                    , id: 'modx-resource-msmc_price'
                    , decimalPrecision: MsMC.config.baseCurrency.precision
                    , fieldLabel: _('ms2_product_msmc_price')
                    , description: '<b>[[*msmc_price]]</b><br>' + _('ms2_product_msmc_price_help')
                    , name: 'msmc_price'
                    , value: config.record.msmc_price || 0
                    , allowBlank: true
                    , anchor: '100%'
                    , enableKeyEvents: true
                    , listeners: {
                        keyup: {
                            fn: function (f, e) {
                                this.updatePrice(f.getValue(), 'price', true);
                            }, scope: this
                        }
                        , blur: {
                            fn: function (f, e) {
                              //  this.updatePrice(f.getValue(), 'price', true);
                            }, scope: this
                        }
                    }
                }, {
                    xtype: 'label',
                    id: 'msmc-price-desc',
                    html: '',
                    cls: 'desc-under'
                }, {
                    xtype: 'numberfield'
                    , id: 'modx-resource-msmc_old_price'
                    , decimalPrecision: MsMC.config.baseCurrency.precision
                    , fieldLabel: _('ms2_product_msmc_old_price')
                    , description: '<b>[[*msmc_old_price]]</b><br>' + _('ms2_product_msmc_old_price_help')
                    , name: 'msmc_old_price'
                    , value: config.record.msmc_old_price || 0
                    , allowBlank: true
                    , anchor: '100%'
                    , enableKeyEvents: true
                    , listeners: {
                        keyup: {
                            fn: function (f, e) {
                                this.updatePrice(f.getValue(), 'old_price', true);
                            }, scope: this
                        }
                        , blur: {
                            fn: function (f, e) {
                                //this.updatePrice(f.getValue(), 'old_price', true);
                            }, scope: this
                        }
                    }
                }, {
                    xtype: 'label',
                    id: 'msmc-old_price-desc',
                    html: '',
                    cls: 'desc-under'
                }]
            };
        },
        updatePrices: function () {
            var price = Ext.getCmp('modx-resource-price')
                , oldPrice = Ext.getCmp('modx-resource-old_price')
                , currency = Ext.getCmp('modx-resource-currency-id');
            this.updatePrice(Ext.getCmp('modx-resource-msmc_price').getValue(), 'price');
            this.updatePrice(Ext.getCmp('modx-resource-msmc_old_price').getValue(), 'old_price');

            if (currency.getValue() == 0) {
                if (price) price.setReadOnly(false);
                if (oldPrice) oldPrice.setReadOnly(false);
            } else {
                if (price) price.setReadOnly(true);
                if (oldPrice) oldPrice.setReadOnly(true);
            }
        },
        updatePrice: function (val, target, overwrite) {
            var price = ''
                , symbol = ''
                , currency = Ext.getCmp('modx-resource-currency-id').getSelectedRecord();
            if (currency && parseInt(currency.data.cid)) {
                var price = parseFloat(val) * parseFloat(currency.data.val),
                    field = Ext.getCmp('modx-resource-' + target),
                    label = Ext.getCmp('msmc-' + target + '-desc');
                if (field) {
                    price = MsMC.utils.roundNumeric(price, MsMC.config.baseCurrency.precision);
                    symbol = MsMC.config.baseCurrency.symbol_right;
                    if (overwrite) field.setValue(price);
                }
            }
            if (label) label.getEl().update(price + ' ' + symbol);

        },
        onChangeSetCurrency: function (combo, a, b) {
            var currency = Ext.getCmp('modx-resource-currency-id');
            currency.clearValue();
            currency.reload(combo.getValue());
        }
    });

});