MsMC.grid.MultiCurrencyProvider = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'msmc-grid-multicurrencyprovider';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/multicurrencyprovider/getList'
        }
        , autoExpandColumn: 'name'
        , save_action: 'mgr/multicurrencyprovider/updateFromGrid'
    });
    MsMC.grid.MultiCurrencyProvider.superclass.constructor.call(this, config);
    this.getStore().on('update', this.handleUpdateStore, this);
};
Ext.extend(MsMC.grid.MultiCurrencyProvider, MsMC.grid.Default, {
    isRefresh: false,
    getListeners: function () {
        return {
            afteredit: {
                fn: function (e) {
                    var fields = ['enable'];
                    if (fields.indexOf(e.field) != -1) {
                        this.isRefresh = true;
                    }
                },
                scope: this
            }
        };
    },
    getFields: function () {
        return ['id', 'name', 'class_name', 'enable', 'properties', 'actions'];
    },
    getColumns: function () {
        return [{
            header: _('id')
            , dataIndex: 'id'
            , sortable: true
            , hidden: true
        }, {
            header: _('msmulticurrency.header_provider_name')
            , dataIndex: 'name'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msmulticurrency.header_provider_class_name')
            , dataIndex: 'class_name'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msmulticurrency.header_provider_enable')
            , dataIndex: 'enable'
            , sortable: true
            , editor: {
                xtype: 'combo-boolean',
                renderer: 'boolean'
            }
        }, {
            header: _('msmulticurrency.header_provider_actions')
            , dataIndex: 'actions'
            , width: 60
            , renderer: MsMC.utils.renderActions

        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('msmulticurrency.btn_provider_create'),
            handler: this.createProvider,
            cls: 'primary-button',
            scope: this
        });
        tbar.push('->', this.getSearchField());

        return tbar;
    },
    actionProvider: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrencyprovider/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        })
    },
    createProvider: function (btn, e) {
        var record = {
            enable: 0,
            properties: ''
        };
        var w = MODx.load({
            xtype: 'msmc-window-multicurrencyprovider-create'
            , record: record
            , listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues(record);
        w.show(e.target);
    },
    updateProvider: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrencyprovider/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msmc-window-multicurrencyprovider-edit');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msmc-window-multicurrencyprovider-edit',
                            id: 'msmc-window-multicurrencyprovider-edit',
                            record: r.object,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                },
                            }
                        });
                        w.fp.getForm().reset();
                        w.fp.getForm().setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
    currencyProvider: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        var record = {
            id: id
        };
        var w = Ext.getCmp('msmc-window-multicurrencyprovider-currency');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msmc-window-multicurrencyprovider-currency',
            id: 'msmc-window-multicurrencyprovider-currency',
            record: record
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(record);
        w.show(e.target);
    },
    removeProvider: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('msmulticurrency.title.win_provider_remove'),
            ids.length > 1
                ? _('msmulticurrency.confirm.provider_multiple_remove')
                : _('msmulticurrency.confirm.provider_remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionProvider('remove');
                }
            }, this
        );
    },
    handleUpdateStore: function (store, record, operation) {
        if (operation == 'commit' && this.isRefresh) {
            this.refresh();
            this.isRefresh = false;
        }
    }
});
Ext.reg('msmc-grid-multicurrencyprovider', MsMC.grid.MultiCurrencyProvider);


MsMC.window.CreateMultiCurrencyProvider = function (config) {
    config = config || {};
    var r = config.record;
    this.ident = config.ident || Ext.id();
    Ext.applyIf(config, {
        title: r.id ? _('msmulticurrency.title.win_provider_edit') : _('msmulticurrency.title.win_provider_create')
        , url: MsMC.config.connectorUrl
        , width: 600
        , autoHeight: true
        , modal: true
        , baseParams: {
            action: r.id ? 'mgr/multicurrencyprovider/update' : 'mgr/multicurrencyprovider/create'
        }
        , fields: [{
            xtype: 'hidden'
            , name: 'id'
        }, {
            xtype: 'textfield'
            , fieldLabel: _('msmulticurrency.label_provider_name')
            , description: _('msmulticurrency.label_provider_name_help')
            , name: 'name'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'textfield'
            , fieldLabel: _('msmulticurrency.label_provider_class_name')
            , description: _('msmulticurrency.label_provider_class_name_help')
            , name: 'class_name'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'combo-boolean'
            , fieldLabel: _('msmulticurrency.label_provider_enable')
            , description: _('msmulticurrency.label_provider_enable_help')
            , name: 'enable'
            , hiddenName: 'enable'
            , anchor: '100%'
        }, {
            xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea'
            , fieldLabel: _('msmulticurrency.label_provider_properties')
            , description: _('msmulticurrency.label_provider_properties_help')
            , name: 'properties'
            , mimeType: 'application/json'
            , height: 150
            , anchor: '100%'
        }]
    });
    MsMC.window.CreateMultiCurrencyProvider.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.CreateMultiCurrencyProvider, MODx.Window);
Ext.reg('msmc-window-multicurrencyprovider-create', MsMC.window.CreateMultiCurrencyProvider);

MsMC.window.EditMultiCurrencyProvider = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    MsMC.window.EditMultiCurrencyProvider.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.EditMultiCurrencyProvider, MsMC.window.CreateMultiCurrencyProvider);
Ext.reg('msmc-window-multicurrencyprovider-edit', MsMC.window.EditMultiCurrencyProvider);

MsMC.window.CurrencyMultiCurrencyProvider = function (config) {
    config = config || {};
    var r = config.record;
    Ext.applyIf(config, {
        title: _('msmulticurrency.title.win_provider_currency')
        , width: 500
        , autoHeight: true
        , autoScroll: true
        , modal: true
        , buttons: [{
            text: _('msmulticurrency.btn_close')
            , scope: this
            , handler: function () {
                config.closeAction !== 'close' ? this.hide() : this.close();
            }
        }]
        , fields: [{
            xtype: 'msmc-grid-multicurrencyprovider-currency'
            , preventRender: true
            , provider: r.id
        }]
    });
    MsMC.window.CurrencyMultiCurrencyProvider.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.CurrencyMultiCurrencyProvider, MODx.Window);
Ext.reg('msmc-window-multicurrencyprovider-currency', MsMC.window.CurrencyMultiCurrencyProvider);