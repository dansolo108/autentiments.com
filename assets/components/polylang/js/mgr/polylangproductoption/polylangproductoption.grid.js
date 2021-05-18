Polylang.grid.PolylangProductOption = function(config) {
    config = config || {};
    if (!config.id) {
        config.id = 'polylang-grid-polylangproductoption';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/polylangproductoption/getList',
            sort: 'id',
            dir: 'DESC',
        },
        autoExpandColumn: 'id',
        save_action: 'mgr/polylangproductoption/updateFromGrid',
        /*
        enableDragDrop: true,
        multi_select: true,
        ddGroup: 'dd',
        ddAction: 'mgr/polylangproductoption/sort'
        */
    });

    Polylang.grid.PolylangProductOption.superclass.constructor.call(this,config)
};
Ext.extend(Polylang.grid.PolylangProductOption, Polylang.grid.Default,{
 getFields: function () {
        return ['id','content_id','culture_key','key','value','actions'];
  },
  getColumns: function () {
        return [{
				header:_('polylang_header_id'),
				dataIndex: 'id',
				sortable: true,
				hidden: true,
			},{
				header:_('polylang_header_content_id'),
				dataIndex: 'content_id',
				sortable: true,
				editor: {
					xtype: 'numberfield'
				},
			},{
				header:_('polylang_header_culture_key'),
				dataIndex: 'culture_key',
				sortable: true,
				editor: {
					xtype: 'textfield'
				},
			},{
				header:_('polylang_header_key'),
				dataIndex: 'key',
				sortable: true,
				editor: {
					xtype: 'textfield'
				},
			},{
				header:_('polylang_header_value'),
				dataIndex: 'value',
				sortable: true,
				editor: {
					xtype: 'textfield'
				},
			}, {
            header: _('polylang_header_actions'),
            dataIndex: 'actions',
            renderer: Polylang.utils.renderActions,
            width: 60

        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('polylang_btn_create'),
            handler: this.addItem,
            cls: 'primary-button',
            scope: this
        });
        /*tbar.push({
            text: '<i class="fa fa-cogs"></i> ',
            menu: [{
                text: '<i class="fa fa-plus"></i> ' + _('polylang_btn_create'),
                cls: 'polylang-cogs',
                handler: this.addItem,
                scope: this
            }, '-', {
                text: '<i class="fa fa-refresh"></i> ' + _('polylang_btn_update'),
                cls: 'polylang-cogs',
                handler: this.updateItem,
                scope: this
            }]
        });*/
        tbar.push('->', this.getSearchField());

        return tbar;
    },
    actionItem: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url:  Polylang.config.connector_url,
            params: {
                action: 'mgr/polylangproductoption/multiple',
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
        var record = {};
        var w = Ext.getCmp('polylang-window-polylangproductoption-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'polylang-window-polylangproductoption-create',
            id: 'polylang-window-polylangproductoption-create',
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
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url:  Polylang.config.connector_url,
            params: {
                action: 'mgr/polylangproductoption/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('polylang-window-polylangproductoption-update');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'polylang-window-polylangproductoption-update',
                            id: 'polylang-window-polylangproductoption-update',
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
    }

});
Ext.reg('polylang-grid-polylangproductoption',Polylang.grid.PolylangProductOption);


Polylang.window.CreatePolylangProductOption = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: config.record.id ? _('polylang_title_win_update') : _('polylang_title_win_create'),
        baseParams: {
            action: config.record.id ? 'mgr/polylangproductoption/update' : 'mgr/polylangproductoption/create'
        }
    });
    Polylang.window.CreatePolylangProductOption.superclass.constructor.call(this,config);
};
Ext.extend(Polylang.window.CreatePolylangProductOption,Polylang.window.Default,{
    getFields: function (config) {
        return [{
				xtype: 'hidden',
				name: 'id',
			},{
				xtype: 'label',
				html: _('polylang_label_id_help'),
				cls: 'desc-under',
			},{
				xtype: 'numberfield',
				fieldLabel: _('polylang_label_content_id'),
				description: '<b>[[*content_id]]</b>',
				name: 'content_id',
				allowBlank:false,
				anchor: '100%',
			},{
				xtype: 'label',
				html: _('polylang_label_content_id_help'),
				cls: 'desc-under',
			},{
				xtype: 'textfield',
				fieldLabel: _('polylang_label_culture_key'),
				description: '<b>[[*culture_key]]</b>',
				name: 'culture_key',
				allowBlank:false,
				anchor: '100%',
			},{
				xtype: 'label',
				html: _('polylang_label_culture_key_help'),
				cls: 'desc-under',
			},{
				xtype: 'textfield',
				fieldLabel: _('polylang_label_key'),
				description: '<b>[[*key]]</b>',
				name: 'key',
				allowBlank:false,
				anchor: '100%',
			},{
				xtype: 'label',
				html: _('polylang_label_key_help'),
				cls: 'desc-under',
			},{
				xtype: 'textarea',
				fieldLabel: _('polylang_label_value'),
				description: '<b>[[*value]]</b>',
				name: 'value',
				allowBlank:false,
				anchor: '100%',
			},{
				xtype: 'label',
				html: _('polylang_label_value_help'),
				cls: 'desc-under',
			}];
    },
});
Ext.reg('polylang-window-polylangproductoption-create',Polylang.window.CreatePolylangProductOption);

Polylang.window.UpdatePolylangProductOption = function(config) {
    config = config || {};
    Ext.applyIf(config,{});
    Polylang.window.UpdatePolylangProductOption.superclass.constructor.call(this,config);
};

Ext.extend(Polylang.window.UpdatePolylangProductOption, Polylang.window.CreatePolylangProductOption);
Ext.reg('polylang-window-polylangproductoption-update',Polylang.window.UpdatePolylangProductOption);