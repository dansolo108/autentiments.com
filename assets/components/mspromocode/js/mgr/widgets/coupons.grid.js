msPromoCode.grid.Coupons = function (config) {
    config = config || {};
    config.owner = config.owner || 'coupon';

    if (!config.id) {
        config.id = 'mspromocode-grid-coupons';

        config.id += config.owner == 'action' ? '-actions' : '';
        config.id += config.action_id ? '-' + config.action_id : '';
    }

    if (typeof config.action_id == 'undefined') {
        config.action_id = 0;
    }
    if (typeof config.action_ref == 'undefined') {
        config.action_ref = false;
    }

    // msPromoCode[config.id] = {};
    // msPromoCode[config.id].action_id = config.action_id;
    // msPromoCode[config.id].action_ref = config.action_ref;

    // if (typeof msPromoCode[config.id].action_id == 'undefined') {
    //     msPromoCode[config.id].action_id = 0;
    // }
    // if (typeof msPromoCode[config.id].action_ref == 'undefined') {
    //     msPromoCode[config.id].action_ref = false;
    // }
    if (typeof msPromoCode.resource_id == 'undefined') {
        msPromoCode.resource_id = 0;
    }
    if (typeof msPromoCode.resource_type == 'undefined') {
        msPromoCode.resource_type = '';
    }

    Ext.applyIf(config, {
        url: msPromoCode.config['connector_url'],
        fields: ['id', 'action_id', 'action', 'referrer_id', 'referrer_username', 'referrer_fullname', 'order_id', 'order_num', 'order_date', 'discount', 'count', 'code', 'description', 'begins', 'ends', 'active', 'freeze', 'activated', 'orders', 'actions'],
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/coupon/getlist',
            owner: config.owner,
            actions: config.actions,
            action_id: config.action_id,
            action_ref: config.action_ref,
            resource_id: msPromoCode.resource_id,
            resource_type: msPromoCode.resource_type,
        },
        listeners: {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);

                if (!row.data.activated) {
                    this.updateCoupon(grid, e, row);
                }
            }
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec, ri, p) {
                var cls = '';
                cls += rec.data.activated ? ' mspromocode-grid-row-activated ' : '';
                cls += rec.data.freeze ? ' mspromocode-grid-row-freeze ' : '';
                cls += !rec.data.active ? ' mspromocode-grid-row-disabled ' : '';

                return cls;
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    msPromoCode.grid.Coupons.superclass.constructor.call(this, config);

    this.on('afterrender', function (grid) {
        var params = miniShop2.utils.Hash.get();
        var coupon_id = params['coupon_id'] || '';
        if (coupon_id) {
            this.updateCoupon(grid, Ext.EventObject, {
                data: {
                    id: coupon_id
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
Ext.extend(msPromoCode.grid.Coupons, MODx.grid.Grid,
    {
        windows: {},

        getMenu: function (grid, rowIndex) {
            var ids = this._getSelectedIds();

            var row = grid.getStore().getAt(rowIndex);
            var menu = msPromoCode.utils.getMenu(row.data['actions'], this, ids);

            this.addContextMenuItem(menu);
        },

        generateCoupons: function (btn, e) {
            if ('ownerCt' in btn && 'ownerCt' in btn.ownerCt) {
                action_id = btn.ownerCt.ownerCt.action_id;
                action_ref = btn.ownerCt.ownerCt.action_ref;
            }

            if (action_id > 0) {
                var w = MODx.load({
                    xtype: 'mspromocode-coupon-window-generate',
                    id: Ext.id(),
                    owner: this.owner,
                    action_id: action_id,
                    action_ref: action_ref,
                    listeners: {
                        success: {
                            fn: function (r) {
                                this.refresh();
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
                w.show(e.target);
            } else {
                Ext.Msg.alert(
                    '',
                    _('mspromocode_err_generate_only_to_action'),
                    function () {
                    },
                    this
                );
            }
        },

        createCoupon: function (btn, e) {
            var w = MODx.load({
                xtype: 'mspromocode-coupon-window-create',
                // id: Ext.id(),
                owner: this.owner,
                listeners: {
                    success: {
                        fn: function (r) {
                            this.refresh();

                            if (r.a.result.object) {
                                this.updateCoupon('', '', {
                                    data: r.a.result.object
                                });
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

        updateCoupon: function (btn, e, row) {
            if (typeof row != 'undefined') {
                this.menu.record = row.data;
            } else if (!this.menu.record) {
                return false;
            }

            var id = this.menu.record.id;

            MODx.Ajax.request({
                url: this.config.url,
                params: {
                    action: 'mgr/coupon/get',
                    id: id
                },
                listeners: {
                    success: {
                        fn: function (r) {
                            var w = MODx.load({
                                xtype: 'mspromocode-coupon-window-update',
                                // id: Ext.id(),
                                record: r,
                                owner: this.owner,
                                listeners: {
                                    success: {
                                        fn: function () {
                                            this.refresh();
                                        },
                                        scope: this
                                    },
                                    hide: {
                                        fn: function () {
                                            miniShop2.utils.Hash.remove('coupon_id');

                                            var item = this;
                                            window.setTimeout(function () {
                                                item.close()
                                            }, 100);
                                        }
                                    },
                                    afterrender: function () {
                                        miniShop2.utils.Hash.add('coupon_id', r.object['id']);
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

        removeCoupon: function (act, btn, e) {
            var ids = this._getSelectedIds();
            if (!ids.length) {
                return false;
            }
            MODx.msg.confirm({
                title: ids.length > 1 ? _('mspromocode_coupons_remove') : _('mspromocode_coupon_remove'),
                text: ids.length > 1 ? _('mspromocode_coupons_remove_confirm') : _('mspromocode_coupon_remove_confirm'),
                url: this.config.url,
                params: {
                    action: 'mgr/coupon/remove',
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
            });
            return true;
        },

        disableCoupon: function (act, btn, e) {
            var ids = this._getSelectedIds();
            if (!ids.length) {
                return false;
            }
            MODx.Ajax.request({
                url: this.config.url,
                params: {
                    action: 'mgr/coupon/disable',
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

        enableCoupon: function (act, btn, e) {
            var ids = this._getSelectedIds();
            if (!ids.length) {
                return false;
            }
            MODx.Ajax.request({
                url: this.config.url,
                params: {
                    action: 'mgr/coupon/enable',
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
            })
        },

        getColumns: function (config) {
            var r = [];

            if (!config.action_id) {
                r.push({
                    header: _('mspromocode_coupon_id'),
                    dataIndex: 'id',
                    sortable: true,
                    hidden: true,
                    width: 40,
                });
            }

            if (config.owner == 'action' && !config.action_id) {
                r.push({
                    header: _('mspromocode_coupon_action'),
                    dataIndex: 'action',
                    sortable: true,
                    width: 70,
                });
            }

            r.push({
                header: _('mspromocode_coupon_discount'),
                dataIndex: 'discount',
                sortable: true,
                width: 40,
                renderer: msPromoCode.utils.renderDiscount,
            });

            if (config.owner != 'action') {
                r.push({
                    header: _('mspromocode_coupon_count'),
                    dataIndex: 'count',
                    sortable: true,
                    width: 60,
                });
            }

            if (config.owner != 'action') {
                r.push({
                    header: _('mspromocode_coupon_orders'),
                    dataIndex: 'orders',
                    sortable: true,
                    width: 60,
                });
            }

            r.push({
                header: _('mspromocode_coupon_code'),
                dataIndex: 'code',
                sortable: true,
                width: 100,
            });

            if (config.owner != 'action') {
                r.push({
                    header: _('mspromocode_coupon_description'),
                    dataIndex: 'description',
                    sortable: true,
                    width: 140,
                });
            }

            if (config.owner != 'action') {
                r.push({
                    header: _('mspromocode_coupon_begins'),
                    dataIndex: 'begins',
                    sortable: true,
                    width: 100,
                    renderer: miniShop2.utils.formatDate,
                }, {
                    header: _('mspromocode_coupon_ends'),
                    dataIndex: 'ends',
                    sortable: true,
                    width: 100,
                    renderer: miniShop2.utils.formatDate,
                });
            }

            if (config.owner != 'action') {
                r.push({
                    header: _('mspromocode_coupon_active'),
                    dataIndex: 'active',
                    renderer: msPromoCode.utils.renderBoolean,
                    sortable: true,
                    width: 50,
                });
            }

            if (config.action_id || config.owner == 'action') {
                if (config.action_ref) {
                    r.push({
                        header: _('mspromocode_coupon_referrer_username'),
                        dataIndex: 'referrer_username',
                        sortable: true,
                        width: 90,
                        renderer: msPromoCode.utils.referrerLink,
                    });
                } else {
                    r.push({
                        header: _('mspromocode_coupon_order_date'),
                        dataIndex: 'order_date',
                        sortable: false,
                        width: 90,
                        renderer: miniShop2.utils.formatDate,
                    }, {
                        header: _('mspromocode_coupon_order_num'),
                        dataIndex: 'order_num',
                        sortable: false,
                        width: 60,
                    });
                }
            }

            r.push({
                header: _('mspromocode_grid_actions'),
                dataIndex: 'actions',
                renderer: msPromoCode.utils.renderActions,
                sortable: false,
                width: (config.owner != 'action' ? 90 : 50),
                id: 'actions',
            });

            return r;
        },

        getTopBar: function (config) {
            var r = [];

            if (config.owner != 'action') {
                r.push({
                    text: '<i class="icon icon-tag"></i>&nbsp;' + _('mspromocode_coupon_create'),
                    cls: 'mspc-btn-primary',
                    handler: this.createCoupon,
                    scope: this,
                });
            } else if (config.owner == 'action' && config.action_id > 0) {
                r.push({
                    text: '<i class="icon icon-tags"></i>&nbsp;' + _('mspromocode_coupon_generate'),
                    cls: 'mspc-btn-primary',
                    handler: this.generateCoupons,
                    scope: this,
                });
            }

            r.push('->', {
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
                                },
                                this);
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
            });

            return r;
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
Ext.reg('mspromocode-grid-coupons', msPromoCode.grid.Coupons);