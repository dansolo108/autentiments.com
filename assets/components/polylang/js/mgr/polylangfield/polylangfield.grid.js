Polylang.grid.PolylangField = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'polylang-grid-polylangfield';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/polylangfield/getList',
            sort: 'rank',
            dir: 'ASC',
        },
        autoExpandColumn: 'id',
        save_action: 'mgr/polylangfield/updateFromGrid',
        enableDragDrop: true,
        multi_select: true,
        ddGroup: 'dd',
        ddAction: 'mgr/polylangfield/sort'
    });

    Polylang.grid.PolylangField.superclass.constructor.call(this, config)
};
Ext.extend(Polylang.grid.PolylangField, Polylang.grid.Default, {
    getFields: function () {
        return ['id', 'class_name', 'name', 'meta', 'xtype', 'code', 'caption', 'description', 'required','translate','sortable', 'active', 'rank', 'actions'];
    },
    getColumns: function () {
        return [{
            header: _('id'),
            dataIndex: 'id',
            sortable: true,
            hidden: true,
        }, {
            header: _('polylang_field_header_class_name'),
            dataIndex: 'class_name',
            sortable: true,
            renderer: function (string) {
                return _('polylang_field_class_name_' + string.toLowerCase());
            }
        }, {
            header: _('polylang_field_header_caption'),
            dataIndex: 'caption',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_field_header_description'),
            dataIndex: 'description',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_field_header_xtype'),
            dataIndex: 'xtype',
            sortable: true,
            editor: {
                xtype: 'polylang-combo-input-types',
                renderer: true
            },
        }, {
            header: _('polylang_field_header_required'),
            dataIndex: 'required',
            sortable: true,
            width: 60,
            editor: {
                xtype: 'polylang-combo-boolean',
                renderer: 'boolean'
            },
        }, {
            header: _('polylang_field_header_translate'),
            dataIndex: 'translate',
            sortable: true,
            width: 60,
            editor: {
                xtype: 'polylang-combo-boolean',
                renderer: 'boolean'
            },
        }, {
            header: _('polylang_field_header_rank'),
            dataIndex: 'rank',
            sortable: true,
            editor: {
                xtype: 'numberfield',
            },
        }, {
            header: _('polylang_field_header_actions'),
            dataIndex: 'actions',
            renderer: Polylang.utils.renderActions,
            width: 60

        }];
    },
    getTopBar: function (config) {
        var tbar = [];
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
                action: 'mgr/polylangfield/multiple',
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
            code: '',
            active: 1,
            dbindex: 0,
            required: 0,
            translate: 1,
            sortable: 0
        };
        var w = Ext.getCmp('polylang-window-polylangfield-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'polylang-window-polylangfield-create',
            id: 'polylang-window-polylangfield-create',
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
                action: 'mgr/polylangfield/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        r.object.code = r.object.code || '';
                        var w = Ext.getCmp('polylang-window-polylangfield-update');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'polylang-window-polylangfield-update',
                            id: 'polylang-window-polylangfield-update',
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
            _('polylang_field_title_win_remove'),
            ids.length > 1
                ? _('polylang_field_confirm.multiple_remove')
                : _('polylang_field_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionItem('remove');
                }
            }, this
        );
    }

});
Ext.reg('polylang-grid-polylangfield', Polylang.grid.PolylangField);


Polylang.window.CreatePolylangField = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: config.record.id ? _('polylang_field_title_win_update') : _('polylang_field_title_win_create'),
        baseParams: {
            action: config.record.id ? 'mgr/polylangfield/update' : 'mgr/polylangfield/create'
        }
    });
    Polylang.window.CreatePolylangField.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.window.CreatePolylangField, Polylang.window.Default, {
    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
        }, {
            xtype: 'polylang-field',
            fieldLabel: _('polylang_field_label_caption'),
            description: '<b>caption</b>',
            name: 'caption',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_field_label_caption_help'),
            cls: 'desc-under',
        }, {
            xtype: 'textarea',
            fieldLabel: _('polylang_field_label_description'),
            description: '<b>description</b>',
            name: 'description',
            allowBlank: true,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_field_label_description_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-combo-input-types',
            fieldLabel: _('polylang_field_label_xtype'),
            description: '<b>xtype</b>',
            name: 'xtype',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_field_label_xtype_help'),
            cls: 'desc-under',
        }, {
            xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
            mimeType: 'application/javascript',
            height: 200,
            fieldLabel: _('polylang_field_label_code'),
            description: '<b>code</b>',
            name: 'code',
            allowBlank: true,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_field_label_code_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-combo-boolean',
            fieldLabel: _('polylang_field_label_required'),
            description: '<b>required</b>',
            name: 'required',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_field_label_required_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-combo-boolean',
            fieldLabel: _('polylang_field_label_translate'),
            description: '<b>translate</b>',
            name: 'translate',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_field_label_translate_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-combo-boolean',
            fieldLabel: _('polylang_field_label_active'),
            description: '<b>active</b>',
            name: 'active',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_field_label_active_help'),
            cls: 'desc-under',
        }];
    },
});
Ext.reg('polylang-window-polylangfield-create', Polylang.window.CreatePolylangField);

Polylang.window.UpdatePolylangField = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    Polylang.window.UpdatePolylangField.superclass.constructor.call(this, config);
};

Ext.extend(Polylang.window.UpdatePolylangField, Polylang.window.CreatePolylangField);
Ext.reg('polylang-window-polylangfield-update', Polylang.window.UpdatePolylangField);