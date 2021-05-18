MsMC.grid.MultiCurrencySetMember = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'msmc-grid-multicurrencysetmember';
    }
    Ext.applyIf(config, {
        url: MsMC.config.connectorUrl
        , baseParams: {
            action: 'mgr/multicurrencysetmember/getList'
            , sort: 'rank'
            , dir: 'ASC'
            , sid: MsMC.config.baseCurrencySetId || 1
        }
        , autoExpandColumn: 'id'
        , save_action: 'mgr/multicurrencysetmember/updateFromGrid'
        , enableDragDrop: true
        , multi_select: true
        , ddGroup: 'dd'
        , ddAction: 'mgr/multicurrencysetmember/sort'

    });

    MsMC.grid.MultiCurrencySetMember.superclass.constructor.call(this, config);
    this.getStore().on('update', this.handleUpdateStore, this);
};
Ext.extend(MsMC.grid.MultiCurrencySetMember, MsMC.grid.Default, {
    isRefresh: false,
    getListeners: function () {
        return {
            afteredit: {
                fn: function (e) {
                    var fields = ['course', 'rate', 'base', 'selected'];
                    if (fields.indexOf(e.field) != -1) {
                        this.isRefresh = true;
                    }
                },
                scope: this
            }
        };
    },
    getFields: function () {
        return ['id', 'sid', 'cid', 'currency_name', 'set_name', 'course', 'rate', 'val', 'auto', 'base', 'selected', 'rank', 'enable', 'updatedon', 'actions'];
    },
    getColumns: function () {
        return [{
            header: _('msmulticurrency.setmember.header_id')
            , dataIndex: 'id'
            , sortable: true
            , hidden: true
        }, {
            header: _('msmulticurrency.setmember.header_sid')
            , dataIndex: 'set_name'
            , hidden: true
        }, {
            header: _('msmulticurrency.setmember.header_cid')
            , dataIndex: 'currency_name'
        }, {
            header: _('msmulticurrency.setmember.header_course')
            , dataIndex: 'course'
            , sortable: true
            , editor: {
                xtype: 'numberfield',
                decimalPrecision: 4
            }
        }, {
            header: _('msmulticurrency.setmember.header_rate')
            , dataIndex: 'rate'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msmulticurrency.setmember.header_val')
            , dataIndex: 'val'
            , sortable: true
            , editor: {
                xtype: 'numberfield',
                decimalPrecision: 4
            }
        }, {
            header: _('msmulticurrency.setmember.header_auto')
            , dataIndex: 'auto'
            , sortable: true
            , width: 60
            , editor: {
                xtype: 'combo-boolean',
                renderer: 'boolean'
            }
        }, {
            header: _('msmulticurrency.setmember.header_base')
            , dataIndex: 'base'
            , sortable: true
            , width: 60
            , editor: {
                xtype: 'combo-boolean',
                renderer: 'boolean'
            }
        }, {
            header: _('msmulticurrency.setmember.header_selected')
            , dataIndex: 'selected'
            , sortable: true
            , width: 60
            , editor: {
                xtype: 'combo-boolean',
                renderer: 'boolean'
            }
        }, {
            header: _('msmulticurrency.setmember.header_enable')
            , dataIndex: 'enable'
            , sortable: true
            , width: 60
            , editor: {
                xtype: 'combo-boolean',
                renderer: 'boolean'
            }
        }, {
            header: _('msmulticurrency.setmember.header_updatedon')
            , dataIndex: 'updatedon'
            , sortable: true
            , renderer: MsMC.utils.formatDate
            , width: 75
        }, {
            header: _('msmulticurrency.setmember.header_actions')
            , dataIndex: 'actions'
            , renderer: MsMC.utils.renderActions
            , width: 60

        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="fa fa-cogs"></i> ',
            menu: [{
                text: '<i class="fa fa-plus"></i> ' + _('msmulticurrency.setmember.btn_create'),
                cls: 'msmc-cogs',
                handler: this.addItem,
                scope: this
            }, '-', {
                text: '<i class="fa fa-refresh"></i> ' + _('msmulticurrency.setmember.btn_update_course'),
                cls: 'msmc-cogs',
                handler: this.updateCourse,
                scope: this
            }]
        });
        tbar.push('->', this.getSetField(), this.getSearchField());

        return tbar;
    },
    getSetField: function () {
        return {
            xtype: 'msmc-combo-set-currency'
            , id: 'msmc-filter-set'
            , value: MsMC.config.baseCurrencySetId || 1
            , anchor: '100%'
            , listeners: {
                select: {
                    fn: this.onChangeSet, scope: this
                }
            }
        }
    },
    actionItem: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrencysetmember/multiple',
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
    },
    addItem: function (btn, e, row) {
        var record = {
            sid: Ext.getCmp('msmc-filter-set').getValue(),
            course: 1,
            rate: 1,
            val: 1,
            base: 0,
            auto: 0,
            enable: 1
        };
        var w = Ext.getCmp('msmc-window-multicurrencysetmember-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msmc-window-multicurrencysetmember-create'
            , id: 'msmc-window-multicurrencysetmember-create'
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
    },
    updateItem: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrencysetmember/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msmc-window-multicurrencysetmember-update');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msmc-window-multicurrencysetmember-update',
                            id: 'msmc-window-multicurrencysetmember-update',
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
    removeItem: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('msmulticurrency.setmember.title.win_remove'),
            ids.length > 1
                ? _('msmulticurrency.setmember.confirm.multiple_remove')
                : _('msmulticurrency.setmember.confirm.remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionItem('remove');
                }
            }, this
        );
    },
    updateCourse: function () {
        MODx.Ajax.request({
            url: MsMC.config.connectorUrl,
            params: {
                action: 'mgr/multicurrencysetmember/updateCourse',
            },
            listeners: {
                success: {
                    fn: function (response) {
                        this.refresh();
                        MODx.msg.alert(_('success'), response.message);
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
    onChangeSet: function (combo, a, b) {
        this.getStore().baseParams.sid = combo.value;
        this.getBottomToolbar().changePage(1);
    },
    handleUpdateStore: function (store, record, operation) {
        if (operation == 'commit' && this.isRefresh) {
            this.refresh();
            this.isRefresh = false;
        }
    }

});
Ext.reg('msmc-grid-multicurrencysetmember', MsMC.grid.MultiCurrencySetMember);


MsMC.window.CreateMultiCurrencySetMember = function (config) {
    config = config || {};
    var r = config.record;
    Ext.applyIf(config, {
        title: r.id ? _('msmulticurrency.setmember.title.win_update') : _('msmulticurrency.setmember.title.win_create')
        , url: MsMC.config.connectorUrl
        , autoHeight: true
        , modal: true
        , baseParams: {
            action: r.id ? 'mgr/multicurrencysetmember/update' : 'mgr/multicurrencysetmember/create'
        }
        , fields: [{
            xtype: 'hidden'
            , name: 'id'
        }, {
            xtype: 'msmc-combo-set-currency'
            , fieldLabel: _('msmulticurrency.setmember.label_sid')
            , description: '<b>[[*sid]]</b><br />' + _('msmulticurrency.setmember.label_sid_help')
            , name: 'sid'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'msmc-combo-currency'
            , fieldLabel: _('msmulticurrency.setmember.label_cid')
            , description: '<b>[[*cid]]</b><br />' + _('msmulticurrency.setmember.label_cid_help')
            , name: 'cid'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'numberfield'
            , decimalPrecision: 4
            , fieldLabel: _('msmulticurrency.setmember.label_course')
            , description: '<b>[[*course]]</b><br />' + _('msmulticurrency.setmember.label_course_help')
            , name: 'course'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'textfield'
            , fieldLabel: _('msmulticurrency.setmember.label_rate')
            , description: '<b>[[*rate]]</b><br />' + _('msmulticurrency.setmember.label_rate_help')
            , name: 'rate'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'numberfield'
            , decimalPrecision: 4
            , fieldLabel: _('msmulticurrency.setmember.label_val')
            , description: '<b>[[*val]]</b><br />' + _('msmulticurrency.setmember.label_val_help')
            , name: 'val'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'combo-boolean'
            , hiddenName: 'auto'
            , fieldLabel: _('msmulticurrency.setmember.label_auto')
            , description: '<b>[[*auto]]</b><br />' + _('msmulticurrency.setmember.label_auto_help')
            , name: 'auto'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'combo-boolean'
            , hiddenName: 'base'
            , fieldLabel: _('msmulticurrency.setmember.label_base')
            , description: '<b>[[*base]]</b><br />' + _('msmulticurrency.setmember.label_base_help')
            , name: 'base'
            , allowBlank: false
            , anchor: '100%'
        }, {
            xtype: 'combo-boolean'
            , hiddenName: 'enable'
            , fieldLabel: _('msmulticurrency.setmember.label_enable')
            , description: '<b>[[*enable]]</b><br />' + _('msmulticurrency.setmember.label_enable_help')
            , name: 'enable'
            , allowBlank: false
            , anchor: '100%'
        }]
    });
    MsMC.window.CreateMultiCurrencySetMember.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.CreateMultiCurrencySetMember, MODx.Window);
Ext.reg('msmc-window-multicurrencysetmember-create', MsMC.window.CreateMultiCurrencySetMember);

MsMC.window.UpdateMultiCurrencySetMember = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    MsMC.window.UpdateMultiCurrencySetMember.superclass.constructor.call(this, config);
};
Ext.extend(MsMC.window.UpdateMultiCurrencySetMember, MsMC.window.CreateMultiCurrencySetMember);
Ext.reg('msmc-window-multicurrencysetmember-update', MsMC.window.UpdateMultiCurrencySetMember);