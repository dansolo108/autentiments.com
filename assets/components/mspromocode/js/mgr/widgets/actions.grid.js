msPromoCode.grid.Actions = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'mspromocode-grid-actions';
    }

    Ext.applyIf(config, {
        url: msPromoCode.config.connector_url,
        fields: ['id', 'discount', 'coupons', 'activated', 'name', 'description', 'begins', 'ends', 'ref', 'active', 'actions'],
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/action/getlist'
        },
        listeners: {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateAction(grid, e, row);
            }
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec, ri, p) {
                return !rec.data.active ? 'mspromocode-grid-row-disabled' : '';
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    msPromoCode.grid.Actions.superclass.constructor.call(this, config);

    this.on('afterrender', function (grid) {
        var params = miniShop2.utils.Hash.get();
        var action_id = params['action_id'] || '';
        if (action_id) {
            this.updateAction(grid, Ext.EventObject, {
                data: {
                    id: action_id
                }
            });
        }
    });

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(msPromoCode.grid.Actions, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = msPromoCode.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    createAction: function (btn, e) {
        var w = MODx.load({
            xtype: 'mspromocode-action-window-create',
            // id: Ext.id(),
            listeners: {
                success: {
                    fn: function (response) {
                        this.refresh();

                        if (response.a.result.object) {
                            this.updateAction('', '', {
                                data: response.a.result.object
                            }, 1);
                        }
                    },
                    scope: this
                },
                hide: {
                    fn: function () {
                        var item = this;
                        window.setTimeout(function () {
                            item.close()
                        }, 100);
                    }
                },
            }
        });
        w.reset();
        w.setValues({
            active: true
        });
        w.show(e.target);
    },

    updateAction: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        } else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/action/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'mspromocode-action-window-update',
                            // id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    },
                                    scope: this
                                },
                                hide: {
                                    fn: function () {
                                        grid = Ext.getCmp('mspromocode-grid-actions');
                                        grid.refresh();

                                        miniShop2.utils.Hash.remove('action_id');

                                        var item = this;
                                        window.setTimeout(function () {
                                            item.close()
                                        }, 100);
                                    }
                                },
                                afterrender: function () {
                                    miniShop2.utils.Hash.add('action_id', r.object['id']);
                                },
                            }
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    },
                    scope: this
                }
            }
        });
    },

    downloadCoupons: function (btn, e, row) {
        var ids = Ext.util.JSON.encode(this._getSelectedIds());

        MODx.Ajax.request({
            url: msPromoCode.config.connector_url,
            params: {
                action: 'mgr/action/download',
                check: true,
                ids: ids,
            },
            listeners: {
                success: {
                    fn: function () {
                        location.href = msPromoCode.config.connector_url + '?action=mgr/action/download&HTTP_MODAUTH=' + MODx.siteId + '&ids=' + ids;
                    },
                    scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(
                            _('error'),
                            response.message
                        );
                    },
                    scope: this
                },
            }
        });
    },

    removeAction: function (act, btn, e) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1 ? _('mspromocode_actions_remove') : _('mspromocode_action_remove'),
            text: ids.length > 1 ? _('mspromocode_actions_remove_confirm') : _('mspromocode_action_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/action/remove',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.refresh();
                    },
                    scope: this
                }
            }
        });
        return true;
    },

    disableAction: function (act, btn, e) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/action/disable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    },
                    scope: this
                }
            }
        })
    },

    enableAction: function (act, btn, e) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/action/enable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    },
                    scope: this
                }
            }
        })
    },

    getColumns: function (config) {
        return [{
            header: _('mspromocode_action_id'),
            dataIndex: 'id',
            sortable: true,
            hidden: true,
            width: 40,
        }, {
            header: _('mspromocode_action'),
            dataIndex: 'name',
            sortable: true,
            width: 120,
        }, {
            header: _('mspromocode_action_discount'),
            dataIndex: 'discount',
            sortable: true,
            width: 70,
        }, {
            header: _('mspromocode_action_coupons'),
            dataIndex: 'coupons',
            sortable: true,
            width: 70,
        }, {
            header: _('mspromocode_action_activated'),
            dataIndex: 'activated',
            sortable: true,
            width: 70,
        }, {
            header: _('mspromocode_action_begins'),
            dataIndex: 'begins',
            sortable: true,
            width: 100,
            renderer: miniShop2.utils.formatDate,
        }, {
            header: _('mspromocode_action_ends'),
            dataIndex: 'ends',
            sortable: true,
            width: 100,
            renderer: miniShop2.utils.formatDate,
        }, {
            header: _('mspromocode_action_active'),
            dataIndex: 'active',
            renderer: msPromoCode.utils.renderBoolean,
            sortable: true,
            width: 50,
        }, {
            header: _('mspromocode_action_ref'),
            dataIndex: 'ref',
            renderer: msPromoCode.utils.renderBoolean,
            sortable: true,
            width: 50,
        }, {
            header: _('mspromocode_grid_actions'),
            dataIndex: 'actions',
            id: 'actions',
            renderer: msPromoCode.utils.renderActions,
            sortable: false,
            width: 70,
        }];
    },

    getTopBar: function (config) {
        return [{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('mspromocode_action_create'),
            cls: 'mspc-btn-primary',
            handler: this.createAction,
            scope: this
        }, '->', {
            xtype: 'textfield',
            name: 'query',
            width: 200,
            id: config.id + '-search-field',
            emptyText: _('mspromocode_grid_search'),
            listeners: {
                render: {
                    fn: function (tf) {
                        tf.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
                            this._doSearch(tf);
                        }, this);
                    },
                    scope: this
                }
            }
        }, {
            xtype: 'button',
            id: config.id + '-search-clear',
            text: '<i class="icon icon-times"></i>',
            listeners: {
                click: {
                    fn: this._clearSearch,
                    scope: this
                }
            }
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
                } else if (typeof this[action] === 'function') {
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

    _doSearch: function (tf, nv, ov) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },

    _clearSearch: function (btn, e) {
        this.getStore().baseParams.query = '';
        Ext.getCmp(this.config.id + '-search-field').setValue('');
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
});
Ext.reg('mspromocode-grid-actions', msPromoCode.grid.Actions);