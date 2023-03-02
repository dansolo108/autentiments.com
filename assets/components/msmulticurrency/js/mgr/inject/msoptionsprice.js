Ext.onReady(function () {
    Ext.override(msoptionsprice.window.modification, {
        originals: {
            getModification: msoptionsprice.window.modification.prototype.getModification,
        },

        getModification: function (config) {
            this.id = config.id || Ext.id();
            var original = this.originals.getModification.call(this, config);
            msoptionsprice.config.window_modification_fields.push('currency_set_id', 'currency_id', 'msmc_price', 'msmc_old_price');
            original.splice(2, 0, this.getCurrencyFields(config));
            if (config.record.id == 0) {
                config.record.currency_set_id = MsMC.config.baseCurrencySetId;
                config.record.currency_id = 0;
                config.record.msmc_price = 0;
                config.record.msmc_old_price = 0;
            }
            return original;
        },
        getCurrencyFields: function (config) {
            return {
                title: _('ms2_product_msmc'),
                xtype: 'fieldset',
                cls: 'msmc-fieldset',
                style: 'padding-top: 5px',
                collapsible: true,
                stateId: 'ms2-msoptionsprice-msmc',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'msmc-combo-set-currency',
                    fieldLabel: _('ms2_product_currency_set'),
                    description: '<b>[[*currency_set_id]]</b>',
                    anchor: '100%',
                    name: 'currency_set_id',
                    value: config.record.currency_set_id || 1,
                    listeners: {
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
                                this.updatePrice(f.getValue(), 'price');
                            }, scope: this
                        }
                        , blur: {
                            fn: function (f, e) {
                                this.updatePrice(f.getValue(), 'price');
                            }, scope: this
                        }
                    }
                }, {
                    xtype: 'label',
                    id: 'msoptionsprice-msmc-price-desc' + this.id,
                    html: '',
                    cls: 'desc-under'
                }, {
                    xtype: 'numberfield'
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
                                this.updatePrice(f.getValue(), 'old_price');
                            }, scope: this
                        }
                        , blur: {
                            fn: function (f, e) {
                                this.updatePrice(f.getValue(), 'old_price');
                            }, scope: this
                        }
                    }
                }, {
                    xtype: 'label',
                    id: 'msoptionsprice-msmc-old_price-desc' + this.id,
                    html: '',
                    cls: 'desc-under'
                }]
            };
        },
        updatePrices: function () {
            var f = this.fp.getForm(),
                price = f.findField('price'),
                oldPrice = f.findField('old_price'),
                currency = f.findField('currency_id');
            this.updatePrice(f.findField('msmc_price').getValue(), 'price');
            this.updatePrice(f.findField('msmc_old_price').getValue(), 'old_price');
            if (currency.getValue() == 0) {
                price.setReadOnly(false);
                oldPrice.setReadOnly(false);
            } else {
                price.setReadOnly(true);
                oldPrice.setReadOnly(true);
            }
        },
        updatePrice: function (val, target) {
            var
                price = '',
                symbol = '',
                f = this.fp.getForm(),
                currency = f.findField('currency_id').getSelectedRecord();

            if (currency && parseInt(currency.data.cid)) {
                var price = parseFloat(val) * parseFloat(currency.data.val);
                price = MsMC.utils.roundNumeric(price, MsMC.config.baseCurrency.precision);
                symbol = MsMC.config.baseCurrency.symbol_right;
                f.findField(target).setValue(price);
            }
            Ext.getCmp('msoptionsprice-msmc-' + target + '-desc' + this.id).getEl().update(price + ' ' + symbol);

        },
        onChangeSetCurrency: function (combo, a, b) {
            var f = this.fp.getForm(),
                currency = f.findField('currency_id');
            currency.clearValue();
            currency.reload(combo.getValue());
        }
    });

});