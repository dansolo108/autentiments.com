MsMC.grid.MultiCurrencySet = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'msmc-grid-multicurrencyset';
    }
    Ext.applyIf(config, {
        url: MsMC.config.connectorUrl
        , baseParams: {
            action: 'mgr/multicurrencyset/getList'
            , sort: 'id'
            , dir: 'ASC'
        }
        , autoExpandColumn: 'id'
        , save_action: 'mgr/multicurrencyset/updateFromGrid'
        /*, enableDragDrop: true
        , multi_select: true
        , ddGroup: 'dd'
        , ddAction: 'mgr/multicurrencyset/sort'*/

    });

    MsMC.grid.MultiCurrencySet.superclass.constructor.call(this, config)
};
Ext.extend(MsMC.grid.MultiCurrencySet, MsMC.grid.Default, {
    getFields: function () {
        return ['id', 'rid', 'name', 'description', 'properties', 'actions'];
    }
    , getColumns: function () {
        return [{
            header: _('msmulticurrency.set.header_id')
            , dataIndex: 'id'
            , sortable: true
            , hidden: true
        }, {
            header: _('msmulticurrency.set.header_name')
            , dataIndex: 'name'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msmulticurrency.set.header_description')
            , dataIndex: 'description'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msmulticurrency.set.header_actions')
            , dataIndex: 'actions'
            , renderer: MsMC.utils.renderActions
            , width: 60

        }];
    }
    , getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('msmulticurrency.set.btn_create'),
            handler: this.addItem,
            cls: 'primary-button',
            scope: this
        });
        tbar.push('->', this.getSearchField());

        return tbar;
    }
    , actionItem: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrencyset/multiple',
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
    , addItem: function (btn, e, row) {
        var record = {};
        var w = Ext.getCmp('msmc-window-multicurrencyset-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msmc-window-multicurrencyset-create'
            , id: 'msmc-window-multicurrencyset-create'
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
    , updateItem: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrencyset/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msmc-window-multicurrencyset-update');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msmc-window-multicurrencyset-update',
                            id: 'msmc-window-multicurrencyset-update',
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
    , removeItem: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('msmulticurrency.set.title.win_remove'),
            ids.length > 1
                ? _('msmulticurrency.set.confirm.multiple_remove')
                : _('msmulticurrency.set.confirm.remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionItem('remove');
                }
            }, this
        );
    }

});
Ext.reg('msmc-grid-multicurrencyset', MsMC.grid.MultiCurrencySet);


MsMC.window.CreateMultiCurrencySet = function (config) {
    config = config || {};
    var r = config.record;
    Ext.applyIf(config, {
        title: r.id ? _('msmulticurrency.set.title.win_update') : _('msmulticurrency.set.title.win_create')
        , url: MsMC.config.connectorUrl
        , autoHeight: true
        , modal: true
        , baseParams: {
            action: r.id ? 'mgr/multicurrencyset/update' : 'mgr/multicurrencyset/create'
        }
        , fields: this.getFields(r)
    });
    MsMC.window.CreateMultiCurrencySet.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.CreateMultiCurrencySet, MODx.Window, {
    getFields: function (record) {
        var fields = [{
            xtype: 'hidden'
            , name: 'id'
        }, {
            xtype: 'textfield'
            , fieldLabel: _('msmulticurrency.set.label_name')
            , description: '<b>[[*name]]</b><br />' + _('msmulticurrency.set.label_name_help')
            , name: 'name'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'textarea'
            , fieldLabel: _('msmulticurrency.set.label_description')
            , description: '<b>[[*description]]</b><br />' + _('msmulticurrency.set.label_description_help')
            , name: 'description'
            , allowBlank: true
            , anchor: '100%'
        }];
        if (!record.id) {
            fields.push({
                xtype: 'msmc-currency-checkboxgroup'
            });
        }
        return fields;
    }

});
Ext.reg('msmc-window-multicurrencyset-create', MsMC.window.CreateMultiCurrencySet);

MsMC.window.UpdateMultiCurrencySet = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    MsMC.window.UpdateMultiCurrencySet.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.UpdateMultiCurrencySet, MsMC.window.CreateMultiCurrencySet);
Ext.reg('msmc-window-multicurrencyset-update', MsMC.window.UpdateMultiCurrencySet);