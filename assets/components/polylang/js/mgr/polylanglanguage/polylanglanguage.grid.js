Polylang.grid.PolylangLanguage = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'polylang-grid-polylanglanguage';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/polylanglanguage/getList',
            sort: 'rank',
            dir: 'ASC',
        },
        autoExpandColumn: 'id',
        save_action: 'mgr/polylanglanguage/updateFromGrid',
        enableDragDrop: true,
        multi_select: true,
        ddGroup: 'dd',
        ddAction: 'mgr/polylanglanguage/sort'
    });

    Polylang.grid.PolylangLanguage.superclass.constructor.call(this, config);
    // this.getStore().on('update', this.handleUpdateStore, this);
};
Ext.extend(Polylang.grid.PolylangLanguage, Polylang.grid.Default, {
    isRefresh: false,
    getListeners: function () {
        return {
            afteredit: {
                fn: function (e) {
                },
                scope: this
            }
        };
    },
    getFields: function () {
        return ['id', 'currency_id', 'name', 'culture_key', 'site_url', 'rank', 'group', 'rank_translation', 'active', 'actions'];
    },
    getColumns: function () {
        var columns = [{
            header: _('id'),
            dataIndex: 'id',
            sortable: true,
            hidden: true,
        }, {
            header: _('polylang_header_name'),
            dataIndex: 'name',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_header_culture_key'),
            dataIndex: 'culture_key',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_header_site_url'),
            dataIndex: 'site_url',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_header_rank'),
            dataIndex: 'rank',
            sortable: true,
            editor: {
                xtype: 'numberfield'
            },
        }, {
            header: _('polylang_header_rank_translation'),
            dataIndex: 'rank_translation',
            sortable: true,
            editor: {
                xtype: 'numberfield'
            },
        }, {
            header: _('polylang_header_group'),
            dataIndex: 'group',
            sortable: true,
            hidden: true,
            editor: {
                xtype: 'textfield'
            },
        },];
        if (Polylang.config.currency.enable) {
            columns.push({
                header: _('polylang_header_currency'),
                dataIndex: 'currency_id',
                width: 125,
                sortable: true,
                editor: {
                    xtype: 'polylang-combo-currency',
                    renderer: true
                },
            });
        }
        columns.push({
            header: _('polylang_header_actions'),
            dataIndex: 'actions',
            renderer: Polylang.utils.renderActions,
            width: 60

        });
        return columns;
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('polylang_btn_create'),
            handler: this.addItem,
            cls: 'primary-button',
            scope: this
        });
        tbar.push('->', this.getSearchField());

        return tbar;
    },
    actionItem: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/polylanglanguage/multiple',
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
            active: 1,
            rank_translation: 0,
            site_url: '[[+schema]][[+base_domain]]'
        };
        var w = Ext.getCmp('polylang-window-polylanglanguage-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'polylang-window-polylanglanguage-create',
            id: 'polylang-window-polylanglanguage-create',
            record: record,
            listeners: {
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
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/polylanglanguage/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('polylang-window-polylanglanguage-update');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'polylang-window-polylanglanguage-update',
                            id: 'polylang-window-polylanglanguage-update',
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
    enableItem: function () {
        this.actionItem('enable');
    },
    disableItem: function () {
        this.actionItem('disable');
    },
    removeItem: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('polylang_title_win_remove'),
            ids.length > 1
                ? _('polylang_confirm.multiple_remove')
                : _('polylang_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionItem('remove');
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
Ext.reg('polylang-grid-polylanglanguage', Polylang.grid.PolylangLanguage);

Polylang.window.CreatePolylangLanguage = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: config.record.id ? _('polylang_title_win_update') : _('polylang_title_win_create'),
        baseParams: {
            action: config.record.id ? 'mgr/polylanglanguage/update' : 'mgr/polylanglanguage/create'
        }
    });
    Polylang.window.CreatePolylangLanguage.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.window.CreatePolylangLanguage, Polylang.window.Default, {
    getFields: function (config) {
        var fields = [{
            xtype: 'hidden',
            name: 'id',
        }, {
            xtype: 'polylang-field',
            fieldLabel: _('polylang_label_name'),
            description: '<b>name</b>',
            name: 'name',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_label_name_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-field',
            fieldLabel: _('polylang_label_culture_key'),
            description: '<b>culture_key</b>',
            name: 'culture_key',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_label_culture_key_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-field',
            fieldLabel: _('polylang_label_site_url'),
            description: '<b>site_url</b>',
            name: 'site_url',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_label_site_url_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-combo-boolean',
            fieldLabel: _('polylang_label_active'),
            description: '<b>active</b>',
            name: 'active',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_label_active_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-field',
            fieldLabel: _('polylang_label_rank_translation'),
            description: '<b>rank_translation</b>',
            name: 'rank_translation',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_label_rank_translation_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-field',
            fieldLabel: _('polylang_label_group'),
            description: '<b>group</b>',
            name: 'group',
            allowBlank: true,
            anchor: '100%',
            hidden: true,
        }, {
            xtype: 'label',
            html: _('polylang_label_group_help'),
            cls: 'desc-under',
        }];
        if (Polylang.config.currency.enable) {
            fields.push({
                xtype: 'polylang-combo-currency',
                fieldLabel: _('polylang_label_currency'),
                description: '<b>currency</b>',
                name: 'currency_id',
                allowBlank: true,
                anchor: '100%',
            }, {
                xtype: 'label',
                html: _('polylang_label_currency_help'),
                cls: 'desc-under',
            });
        }
        return fields;
    },
});
Ext.reg('polylang-window-polylanglanguage-create', Polylang.window.CreatePolylangLanguage);

Polylang.window.UpdatePolylangLanguage = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    Polylang.window.UpdatePolylangLanguage.superclass.constructor.call(this, config);
};

Ext.extend(Polylang.window.UpdatePolylangLanguage, Polylang.window.CreatePolylangLanguage);
Ext.reg('polylang-window-polylanglanguage-update', Polylang.window.UpdatePolylangLanguage);