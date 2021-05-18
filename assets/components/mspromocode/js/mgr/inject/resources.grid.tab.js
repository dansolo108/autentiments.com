Ext.namespace('msPromoCode.functions');

msPromoCode.functions.tabResources = function (config, type) {
    // console.log(config)

    config = config || {};
    type = type || ['product', 'products'];

    return {
        id: 'mspromocode-window-coupon-tab-' + type[1] + '-' + config.id,
        title: _('mspromocode_tab_' + type[1]),
        layout: 'form',
        cls: 'modx-panel',
        autoHeight: true,
        anchor: '100% 100%',
        labelWidth: 100,
        items: [{
            html: _('mspromocode_msg_tab_save_resources'),
            cls: 'panel-desc',
            style: 'margin: 0;',
        }, {
            xtype: 'mspromocode-combo-' + type[0],
            id: type[1] + '-combo-' + config.id,
            name: type[0] + '_select',
            listeners: msPromoCode.functions.comboResourcesListeners(config, type),
            hideLabel: true,
            owner: config.owner,
            actions: config.actions,
            obj_id: config.record.object.id,
        }, {
            xtype: 'mspromocode-window-grid-coupon-resources',
            id: type[1] + '-grid-' + config.id,
            comboid: type[1] + '-combo-' + config.id,
            baseParams: {
                action: 'mgr/resource/getlist',
                owner: config.owner,
                actions: config.actions,
                obj_id: config.record.object.id,
                type: type[0],
            },
            style: {marginTop: '7px'},
        }],
    };
}

msPromoCode.functions.comboResourcesListeners = function (config, type) {
    config = config || {};
    type = type || ['product', 'products'];

    return {
        select: function (combo, row) {
            var grid = Ext.getCmp(type[1] + '-grid-' + config.id);
            if (typeof grid == 'undefined') {
                return;
            }

            MODx.Ajax.request({
                url: msPromoCode.config['connector_url'],
                params: {
                    action: 'mgr/resource/create',
                    // coupon_id: config.record.object.id,
                    owner: config.owner,
                    actions: combo.actions,
                    obj_id: combo.obj_id,
                    resource_id: row.id,
                    discount: '',
                    type: type[0],
                },
                listeners: {
                    success: {
                        fn: function (r) {
                            combo.reset();
                            combo.setValue('');
                            grid.refresh();
                        },
                        scope: this
                    }
                }
            });
        },
        expand: function (combo) {
            combo.store.removeAll();

            var grid = Ext.getCmp(type[1] + '-grid-' + config.id);
            if (typeof grid == 'undefined') {
                return;
            }
            // console.log(grid)

            var gridStore = grid.getStore();

            oldLimit = gridStore.lastOptions.params['limit'];
            //oldStart = gridStore.lastOptions.params['start'];

            lastOptions = gridStore.lastOptions;
            lastOptions.params['limit'] = gridStore.getTotalCount();
            lastOptions.params['start'] = 0;

            MODx.Ajax.request({
                url: msPromoCode.config.connector_url,
                params: lastOptions.params,
                listeners: {
                    success: {
                        fn: function (r) {
                            var res = r.results;
                            // console.log(res)

                            msPromoCode.functions.gridResourcesStoreAfterLoad({
                                records: res,
                                combo: combo,
                            })

                            gridStore.lastOptions.params['limit'] = oldLimit;
                            //gridStore.lastOptions.params['start'] = 0;
                        },
                        scope: this
                    }
                }
            });
        }
    }
}

msPromoCode.functions.gridResourcesStoreAfterLoad = function (param) {
    var records = param.records || {};
    var combo = param.combo;
    var comboid = param.comboid;
    var notload = param.notload;
    var collapse = param.collapse;

    if (typeof combo == 'undefined') {
        var combo = Ext.getCmp(comboid);
    }
    if (typeof combo == 'undefined') {
        return
    }

    if (!msPromoCode.utils.isEmptyObject(records)) {
        msPromoCode.window.tmp.exclude_ids = '';

        for (var i = 0; i < records.length; i++) {
            if (!msPromoCode.utils.isEmptyObject(records[i].json)) {
                rec = records[i].json;
            } else {
                rec = records[i];
            }

            msPromoCode.window.tmp.exclude_ids += msPromoCode.window.tmp.exclude_ids == '' ? rec.rid : ',' + rec.rid;
        }
    }

    combo.store.baseParams.exclude_ids = msPromoCode.window.tmp.exclude_ids;
    combo.reset();
    combo.setValue('');
    if (typeof notload == 'undefined') {
        combo.store.load();
    }
}

msPromoCode.window.gridCouponResources = function (config) {
    config = config || {};
    config.id = config.id || 'mspromocode-window-grid-coupon-resources';

    Ext.applyIf(config, {
        url: msPromoCode.config.connector_url,
        fields: ['id', 'type', 'pagetitle', 'discount', 'power', 'actions'],
        columns: this.getColumns(config),
        tbar: [],
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/resource/getlist',
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec, ri, p) {
                return !rec.json.published ? 'mspromocode-grid-row-disabled' : '';
            }
        },
        paging: true,
        pageSize: 10,
        remoteSort: true,
        autoHeight: true,

        save_action: 'mgr/resource/updatefromgrid',
        autosave: true,
        save_callback: this.updateGridRow,
    });
    msPromoCode.window.gridCouponResources.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('beforeload', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);

    this.store.on('load', function (grid, records, opt) {
        msPromoCode.functions.gridResourcesStoreAfterLoad({
            records: records,
            notload: true,
        })
    }, this);

    this.on('afterrender', function () {
        this.topToolbar.hide();
    }, this);
};
Ext.extend(msPromoCode.window.gridCouponResources, MODx.grid.Grid, {
    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = msPromoCode.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    getColumns: function (config) {
        var r = [{
            header: _('mspromocode_coupon_id'),
            dataIndex: 'id',
            sortable: true,
            hidden: true,
            width: 40,
        }, {
            header: _('mspromocode_type'),
            dataIndex: 'type',
            sortable: true,
            hidden: true,
            width: 40,
        }, {
            header: _('mspromocode_resource'),
            dataIndex: 'pagetitle',
            sortable: true,
            width: 200,
        }, {
            header: _('mspromocode_coupon_discount'),
            dataIndex: 'discount',
            sortable: true,
            width: 70,
            editor: {
                xtype: 'textfield',
            },
        }];

        if (config.baseParams.type == 'category') {
            r.push({
                header: _('mspromocode_coupon_power'),
                dataIndex: 'power',
                sortable: true,
                width: 50,
                editor: {
                    xtype: 'numberfield',
                },
            });
        }

        r.push({
            header: _('mspromocode_grid_actions'),
            dataIndex: 'actions',
            renderer: msPromoCode.utils.renderActions,
            sortable: false,
            width: 60,
            id: 'actions',
        });

        return r;
    },

    updateGridRow: function (response) {
        /*var row = response.object;
         var items = this.store.data.items;

         for( var i = 0; i < items.length; i++ )
         {
         var item = items[i];
         if( item.id == row.id )
         {
         item.data = row;
         }
         }*/
        this.refresh();
    },

    detachResource: function (act, btn, e) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1 ? _('mspromocode_resources_detach') : _('mspromocode_resource_detach'),
            text: ids.length > 1 ? _('mspromocode_resources_detach_confirm') : _('mspromocode_resource_detach_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/resource/detach',
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
});
Ext.reg('mspromocode-window-grid-coupon-resources', msPromoCode.window.gridCouponResources);