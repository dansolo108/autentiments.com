MsMC.grid.MultiCurrency = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'msmc-grid-multicurrency';
    }

    Ext.applyIf(config, {
        url: MsMC.config.connectorUrl
        , baseParams: {
            action: 'mgr/multicurrency/getList'
            , sort: 'code'
            , dir: 'ASC'
        }
        , autoExpandColumn: 'name'
        , save_action: 'mgr/multicurrency/updateFromGrid'
    });
    MsMC.grid.MultiCurrency.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.grid.MultiCurrency, MsMC.grid.Default, {
    multiCurrencyAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrency/multiple',
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
    }
    , multiCurrencyUpdate: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrency/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msproductscomposerselection-filter-window-update');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msproductscomposerselection-filter-window-update',
                            id: 'msproductscomposerselection-filter-window-update',
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
    }
    , getFields: function () {
        return ['id', 'name', 'code', 'symbol_left', 'symbol_right','precision', 'actions'];
    }
    , getColumns: function () {
        return [{
            header: _('id')
            , dataIndex: 'id'
            , sortable: true
            , width:20
            , hidden: false
        }, {
            header: _('msmulticurrency.header_name')
            , dataIndex: 'name'
            , sortable: true
        }, {
            header: _('msmulticurrency.header_code')
            , dataIndex: 'code'
            , sortable: true
        }, {
            header: _('msmulticurrency.header_symbol_left')
            , dataIndex: 'symbol_left'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msmulticurrency.header_symbol_right')
            , dataIndex: 'symbol_right'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msmulticurrency.header_precision')
            , dataIndex: 'precision'
            , sortable: true
            , editor: {
                xtype: 'numberfield'
            }
        },{
            header: _('msmulticurrency.header_actions')
            , dataIndex: 'actions'
            , renderer: MsMC.utils.renderActions
            , width: 60

        }];
    }
    , getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('msmulticurrency.btn_create'),
            handler: this.addCurrency,
            cls: 'primary-button',
            scope: this
        });
        tbar.push('->', this.getSearchField());

        return tbar;
    }
    , actionCurrency: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrency/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (response) {
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
    }
    , addCurrency: function (btn, e, row) {
        var record = {
            course: 1,
            rate: 1,
            val: 1,
            base: 0,
            auto: 0,
            enable: 1
        };
        var w = Ext.getCmp('msmc-window-multicurrency-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msmc-window-multicurrency-create'
            , id: 'msmc-window-multicurrency-create'
            , record: record
            , listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(record);
        w.show(e.target);
    }
    , updateCurrency: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrency/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msmc-window-multicurrency-edit');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msmc-window-multicurrency-edit',
                            id: 'msmc-window-multicurrency-edit',
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
    }
    , removeCurrency: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('msmulticurrency.title.win_remove'),
            ids.length > 1
                ? _('msmulticurrency.confirm.multiple_remove')
                : _('msmulticurrency.confirm.remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionCurrency('remove');
                }
            }, this
        );
    }
});
Ext.reg('msmc-grid-multicurrency', MsMC.grid.MultiCurrency);


MsMC.window.CreateMultiCurrency = function (config) {
    config = config || {};
    var r = config.record;
    Ext.applyIf(config, {
        title: r.id ? _('msmulticurrency.title.win_update') : _('msmulticurrency.title.win_create')
        , url: MsMC.config.connectorUrl
        , width: 500
        , autoHeight: true
        , modal: true
        , baseParams: {
            action: r.id ? 'mgr/multicurrency/update' : 'mgr/multicurrency/create'
        }
        , fields: [{
            xtype: 'hidden'
            , name: 'id'
        }, {
            xtype: 'textfield'
            , fieldLabel: _('msmulticurrency.label_name')
            , description: '<b>[[+name]]</b><br />' + _('msmulticurrency.label_name_help')
            , name: 'name'
            , allowBlank: true
            , anchor: '100%'
        }, {
            xtype: 'textfield'
            , fieldLabel: _('msmulticurrency.label_code')
            , description: '<b>[[+code]]</b><br />' + _('msmulticurrency.label_code_help')
            , name: 'code'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'textfield'
            , fieldLabel: _('msmulticurrency.label_symbol_left')
            , description: '<b>[[+symbol_left]]</b><br />' + _('msmulticurrency.label_symbol_left_help')
            , name: 'symbol_left'
            , allowBlank: true
            , anchor: '100%'
        }, {
            xtype: 'textfield'
            , fieldLabel: _('msmulticurrency.label_symbol_right')
            , description: '<b>[[+symbol_right]]</b><br />' + _('msmulticurrency.label_symbol_right_help')
            , name: 'symbol_right'
            , allowBlank: true
            , anchor: '100%'
        }, {
            xtype: 'numberfield'
            , fieldLabel: _('msmulticurrency.label_precision')
            , description: '<b>[[+precision]]</b><br />' + _('msmulticurrency.label_precision_help')
            , name: 'precision'
            , allowBlank: true
            , anchor: '100%'
        }]
    });
    MsMC.window.CreateMultiCurrency.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.CreateMultiCurrency, MODx.Window);
Ext.reg('msmc-window-multicurrency-create', MsMC.window.CreateMultiCurrency);

MsMC.window.EditMultiCurrency = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    MsMC.window.EditMultiCurrency.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.EditMultiCurrency, MsMC.window.CreateMultiCurrency);
Ext.reg('msmc-window-multicurrency-edit', MsMC.window.EditMultiCurrency);