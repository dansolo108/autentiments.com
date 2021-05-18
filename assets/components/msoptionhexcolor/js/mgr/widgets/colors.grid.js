miniShop2.grid.HexColor = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'minishop2-grid-hexcolor';
    }
    config.url = msOptionHexColor.config.connector_url;

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/color/getlist'
        },
        stateful: true,
        stateId: config.id,
        multi_select: true,
    });
    miniShop2.grid.HexColor.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.grid.HexColor, miniShop2.grid.Default, {
    getFields: function () {
        return [
            'id', 'name', 'resource', 'hex', 'email', 'logo', 'pagetitle',
            'address', 'phone', 'fax', 'description', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('ms2_id'), dataIndex: 'id', width: 30, sortable: true},
            /*
            {header: _('ms2_logo'), dataIndex: 'logo', id: 'image', width: 50, renderer: miniShop2.utils.renderImage},
            */
            {header: _('ms2_name'), dataIndex: 'name', width: 100, sortable: true},
            /*
            {
                header: _('ms2_resource'),
                dataIndex: 'resource',
                width: 100,
                sortable: true,
                hidden: true,
                renderer: this._renderResource
            },
            */
            {header: _('msoptionhexcolor_hex'), dataIndex: 'hex', width: 75, sortable: true},
            /*
            {header: _('ms2_email'), dataIndex: 'email', width: 100, sortable: true},
            {header: _('ms2_address'), dataIndex: 'address', width: 100, sortable: true, hidden: true},
            {header: _('ms2_phone'), dataIndex: 'phone', width: 75, sortable: true},
            {header: _('ms2_fax'), dataIndex: 'fax', width: 75, sortable: true, hidden: true},
            */
            {
                header: _('ms2_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 50,
                renderer: miniShop2.utils.renderActions
            }
        ];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i> ' + _('ms2_btn_create'),
            handler: this.createColor,
            scope: this
        }, '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateColor(grid, e, row);
            },
        };
    },

    colorAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: msOptionHexColor.config.connector_url,
            params: {
                action: 'mgr/color/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        //noinspection JSUnresolvedFunction
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

    createColor: function (btn, e) {
        var w = Ext.getCmp('minishop2-window-hexcolor-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-hexcolor-create',
            id: 'minishop2-window-hexcolor-create',
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.show(e.target);
    },

    updateColor: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }

        var w = Ext.getCmp('minishop2-window-hexcolor-update');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'minishop2-window-hexcolor-update',
            id: 'minishop2-window-hexcolor-update',
            title: this.menu.record['name'],
            record: this.menu.record,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(this.menu.record);
        w.show(e.target);
    },

    enableColor: function () {
        this.colorAction('enable');
    },

    disableColor: function () {
        this.colorAction('disable');
    },

    removeColor: function () {
        var ids = this._getSelectedIds();

        Ext.MessageBox.confirm(
            _('ms2_menu_remove_title'),
            ids.length > 1
                ? _('ms2_menu_remove_multiple_confirm')
                : _('ms2_menu_remove_confirm'),
            function (val) {
                if (val == 'yes') {
                    this.colorAction('remove');
                }
            }, this
        );
    },

    _renderResource: function (value, cell, row) {
        return value
            ? String.format('({0}) {1}', value, row.data['pagetitle'])
            : '';
    }
});
Ext.reg('minishop2-grid-hexcolor', miniShop2.grid.HexColor);