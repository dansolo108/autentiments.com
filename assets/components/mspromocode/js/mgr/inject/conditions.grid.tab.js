Ext.namespace('msPromoCode.functions');


msPromoCode.functions.tabConditions = function (config) {
    // console.log(config)

    config = config || {};

    return {
        id: 'mspromocode-window-coupon-tab-conditions' + config.id,
        title: _('mspromocode_tab_conditions'),
        layout: 'form',
        cls: 'modx-panel',
        autoHeight: true,
        anchor: '100% 100%',
        labelWidth: 100,
        items: [{
            html: _('mspromocode_msg_tab_conditions'),
            cls: 'panel-desc',
            style: 'margin:0;',
        }, {
            xtype: 'mspromocode-combo-condition-type',
            id: 'conditions-combo-' + config.id,
            name: 'condition_select',
            listeners: msPromoCode.functions.comboConditionsListeners(config),
            hideLabel: true,
            owner: config.owner,
            // actions: config.actions,
            owner_id: config.record.object.id,
        }, {
            xtype: 'mspromocode-window-grid-coupon-conditions',
            id: 'conditions-grid-' + config.id,
            comboid: 'conditions-combo-' + config.id,
            baseParams: {
                action: 'mgr/condition/getlist',
                owner: config.owner,
                // actions: config.actions,
                owner_id: config.record.object.id,
            },
            style: {marginTop: '7px'},
        }],
    };
}

msPromoCode.functions.comboConditionsListeners = function (config) {
    config = config || {};

    return {
        select: function (combo, row) {
            var grid = Ext.getCmp('conditions-grid-' + config.id);
            if (typeof grid == 'undefined') {
                return;
            }

            MODx.Ajax.request({
                url: msPromoCode.config.connector_url,
                params: {
                    action: 'mgr/condition/create',
                    // coupon_id: config.record.object.id,
                    owner: combo.owner,
                    // actions: combo.actions,
                    owner_id: combo.owner_id,
                    type: row.json.value,
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

            var grid = Ext.getCmp('conditions-grid-' + config.id);
            if (typeof grid == 'undefined') {
                return;
            }

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

                            msPromoCode.functions.gridConditionsStoreAfterLoad({
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

msPromoCode.functions.gridConditionsStoreAfterLoad = function (param) {
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
        msPromoCode.window.tmp.exclude = [];

        for (var i = 0; i < records.length; i++) {
            if (!msPromoCode.utils.isEmptyObject(records[i].json)) {
                rec = records[i].json;
            } else {
                rec = records[i];
            }

            msPromoCode.window.tmp.exclude.push(rec.type);
        }
    }

    combo.store.baseParams.exclude = Ext.util.JSON.encode(msPromoCode.window.tmp.exclude);
    combo.reset();
    combo.setValue('');
    if (typeof notload == 'undefined') {
        combo.store.load();
    }
}

msPromoCode.window.gridCouponConditions = function (config) {
    config = config || {};
    config.id = config.id || 'mspromocode-window-grid-coupon-conditions';

    Ext.applyIf(config, {
        url: msPromoCode.config.connector_url,
        fields: ['id', 'action_id', 'coupon_id', 'type', 'value', 'actions'],
        columns: this.getColumns(config),
        tbar: [],
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/condition/getlist',
            owner: config.owner,
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            // getRowClass: function(rec, ri, p) {
            //     return !rec.json.published ? 'mspromocode-grid-row-disabled' : '';
            // }
        },
        paging: true,
        pageSize: 10,
        remoteSort: true,
        autoHeight: true,

        save_action: 'mgr/condition/updatefromgrid',
        autosave: true,
        save_callback: this.updateGridRow,
    });
    msPromoCode.window.gridCouponConditions.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('beforeload', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);

    this.store.on('load', function (grid, records, opt) {
        msPromoCode.functions.gridConditionsStoreAfterLoad({
            records: records,
            notload: true,
        })
    }, this);

    this.on('afterrender', function () {
        this.topToolbar.hide();
    }, this);
};
Ext.extend(msPromoCode.window.gridCouponConditions, MODx.grid.Grid, {
    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = msPromoCode.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    getColumns: function (config) {
        var r = [{
            header: _('mspromocode_id'),
            dataIndex: 'id',
            sortable: true,
            hidden: true,
            width: 40,
        }];

        r.push({
            header: _('mspromocode_condition_' + config.baseParams.owner),
            dataIndex: config.baseParams.owner + '_id',
            sortable: true,
            hidden: true,
            width: 50,
        });

        r.push({
            header: _('mspromocode_condition_type'),
            dataIndex: 'type',
            sortable: true,
            width: 200,
            renderer: msPromoCode.utils.renderConditionType,
        }, {
            header: _('mspromocode_value'),
            dataIndex: 'value',
            sortable: true,
            // hidden: true,
            width: 100,
            editor: {
                xtype: 'textfield',
            },
        });

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

    removeCondition: function (act, btn, e) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1 ? _('mspromocode_conditions_remove') : _('mspromocode_consition_remove'),
            text: ids.length > 1 ? _('mspromocode_conditions_remove_confirm') : _('mspromocode_consition_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/condition/remove',
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
Ext.reg('mspromocode-window-grid-coupon-conditions', msPromoCode.window.gridCouponConditions);