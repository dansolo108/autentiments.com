miniShop2.plugin.msMultiCurrency = {
    grid: null,
    isSetup: false,
    refreshed: false,
    setup: function () {
        this.isSetup = true;
        this.grid = Ext.getCmp('minishop2-grid-products');
        this.grid.on('beforeedit', function (e) {
            var editor = e.grid.getColumnModel().config[e.column].editor;
            if (editor && editor instanceof MsMC.combo.SetMember) {
                var editorStore = editor.getStore(),
                    setId = parseInt(e.record.data.currency_set_id) ? e.record.data.currency_set_id : 1;
                if (setId != parseInt(editorStore.baseParams.sid)) {
                    editorStore.setBaseParam('sid', setId);
                    editorStore.setBaseParam('exclude', MsMC.config.baseCurrency.id);
                    editorStore.load();
                }
            }
        });
        this.grid.getStore().on('update', function (store, record, operation) {
            if (operation == Ext.data.Record.COMMIT && this.refreshed) {
                this.grid.refresh(true);
            }
        }, this);
    },
    getFields: function (config) {
        return {}
    },
    getColumns: function (config) {
        return {
            msmc_price: {
                header: _('ms2_category_msmc_price')
                , dataIndex: 'msmc_price'
                , name: 'msmc_price'
                , sortable: true
                , editor: {
                    xtype: 'numberfield'
                    , decimalPrecision: 2
                    , listeners: {
                        change: {
                            fn: function () {
                                this.refreshed = true;
                            }, scope: this
                        }
                    }
                }
            },
            msmc_old_price: {
                header: _('ms2_category_msmc_old_price')
                , dataIndex: 'msmc_old_price'
                , name: 'msmc_old_price'
                , sortable: true
                , editor: {
                    xtype: 'numberfield'
                    , decimalPrecision: 2
                    , listeners: {
                        change: {
                            fn: function () {
                                this.refreshed = true;
                            }, scope: this
                        }
                    }
                }
            },
            currency_id: {
                header: _('ms2_product_currency')
                , dataIndex: 'currency_id'
                , name: 'currency_id'
                , sortable: true
                , renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
                    if (!miniShop2.plugin.msMultiCurrency.isSetup) {
                        miniShop2.plugin.msMultiCurrency.setup();
                    }
                    var editorStore = this.editor.getStore(),
                        setId = parseInt(record.data.currency_set_id) ? record.data.currency_set_id : 1;
                    if (!editorStore.loading || setId != parseInt(editorStore.baseParams.sid)) {
                        editorStore.on('load', function () {
                            editorStore.loading = true;
                            var rec = this.editor.findRecord(this.editor.valueField, value),
                                val = rec ? rec.get(this.editor.displayField) : '';
                            miniShop2.plugin.msMultiCurrency.grid.getView().getCell(rowIndex, colIndex).firstChild.innerHTML = val;
                        }, this);
                        editorStore.setBaseParam('sid', setId);
                        editorStore.setBaseParam('exclude', MsMC.config.baseCurrency.id);
                        editorStore.load();
                    } else {
                        var rec = this.editor.findRecord(this.editor.valueField, value)
                        return rec ? rec.get(this.editor.displayField) : '';
                    }
                }
                , editor: {
                    xtype: 'msmc-combo-set-member'
                    , listeners: {
                        change: {
                            fn: function () {
                                this.refreshed = true;
                            }, scope: this
                        }
                    }
                }
            }
        }
    }
};