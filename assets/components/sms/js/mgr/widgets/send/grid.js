sms.grid.Send = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'sms-grid-send';
    }
    Ext.applyIf(config, {
        url: sms.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        //sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/send/getlist'
        },
        listeners: {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.viewData(grid, e, row);
            },
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec) {
                return 'grid_row_class';
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    sms.grid.Send.superclass.constructor.call(this, config);

    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(sms.grid.Send, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = sms.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    viewData: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = 'sms-send-window-view';
        if (Ext.getCmp(id)) {
            Ext.getCmp(id).close();
        }
        this.windows[id] = '';
        this.loadWindow(btn, e, {
            xtype: id,
            id: id,
            record: this.menu.record,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
    },

    getFields: function () {
        return ['id', 'sms_id', 'user_id', 'user_fullname', 'user_email', 'phone', 'message', 'send', 'status', 'count', 'service', 'info', 'properties', 'ip', 'client', 'createdon', 'actions'];
    },

    getColumns: function () {
        var fields = sms.config.fields.send;
        var data = [];
        var temp = {};
        for (var i = 0; i < fields.length; i++) {
            var field = fields[i].split(':');
            temp = {
                header: _('sms_send_field_' + field[0]),
                dataIndex: field[0],
                sortable: true,
            };
            if (!isNaN(parseInt(field[1]))) {
                temp.width = parseInt(field[1]);
            }
            switch (field[0]) {
                case 'id':
                    temp.hidden = true;
                    break;
                case 'send':
                    temp.renderer = sms.utils.renderBoolean;
                    break;
            }
            if (!temp.renderer && typeof sms.utils['render_send_' + field[0]] == 'function') {
                temp.renderer = sms.utils['render_send_' + field[0]];
            }
            data.push(temp);
        }
        return data;
    },

    getTopBar: function (config) {
        return ['->', {
            xtype: 'textfield',
            name: 'query',
            id: config.id + '-filter-query',
            width: 200,
            filter: true,
            emptyText: _('sms_filter_query'),
            listeners: {
                select: {
                    fn: this.filterSend,
                    scope: this,
                },
                render: {
                    fn: function (tf) {
                        tf.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
                            this.filterSend();
                        }, this);
                    }, scope: this
                }
            },
        }, {
            xtype: 'sms-combo-user',
            name: 'user_id',
            id: config.id + '-filter-user_id',
            width: 200,
            filter: true,
            emptyText: _('sms_filter_user'),
            listeners: {
                select: {
                    fn: this.filterSend,
                    scope: this,
                }
            }
        }, {
            xtype: 'sms-combo-service',
            name: 'service',
            id: config.id + '-filter-service',
            width: 150,
            filter: true,
            emptyText: _('sms_filter_service'),
            listeners: {
                select: {
                    fn: this.filterSend,
                    scope: this,
                }
            }
        }, {
            text: '<i class="icon icon-check"></i>',
            handler: this.filterSend,
            scope: this
        }, {
            text: '<i class="icon icon-close"></i>',
            handler: this.filterReset,
            scope: this
        }];
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                }
                else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

    filters: ['user_id', 'service', 'query'],

    filterSend: function (tf, nv, ov) {
        var data = this.filters;
        for (var i = 0; i < data.length; i++) {
            this.getStore().baseParams[data[i]] = Ext.getCmp(this.id + '-filter-' + data[i]).getValue();
        }
        this.getBottomToolbar().changePage(1);
    },

    filterReset: function (btn, e) {
        var data = this.filters;
        for (var i = 0; i < data.length; i++) {
            Ext.getCmp(this.id + '-filter-' + data[i]).reset();
        }
        this.filterSend();
    },

});
Ext.reg('sms-grid-send', sms.grid.Send);
