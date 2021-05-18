Ext.namespace('msPromoCode.functions');


msPromoCode.functions.tabOrders = function (config) {
    console.log('tabOrders config', config);

    config = config || {};

    // if (config.owner != 'coupon') {
    //     return {};
    // }

    return {
        id: 'mspromocode-window-coupon-tab-orders' + config.id,
        title: _('mspromocode_tab_orders'),
        layout: 'form',
        cls: 'modx-panel',
        autoHeight: true,
        anchor: '100% 100%',
        labelWidth: 100,
        items: [{
            html: _('mspromocode_msg_tab_orders'),
            cls: 'panel-desc',
            style: 'margin:0;',
        }, {
            xtype: 'mspromocode-window-grid-coupon-orders',
            id: 'orders-grid-' + config.id,
            baseParams: {
                action: 'mgr/order/getlist',
                // actions: config.actions,
                coupon_id: config.record.object.id,
            },
            style: {marginTop: '7px'},
        }],
    };
};

msPromoCode.window.gridCouponOrders = function (config) {
    config = config || {};
    config.id = config.id || 'mspromocode-window-grid-coupon-orders';

    Ext.applyIf(config, {
        url: msPromoCode.config.connector_url,
        fields: ['id', 'action_id', 'coupon_id', 'order_id', 'order_num', 'actions'],
        columns: this.getColumns(config),
        tbar: [],
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/order/getlist',
            // coupon_id: config.record.object.id,
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
    });
    msPromoCode.window.gridCouponOrders.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('beforeload', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);

    this.on('afterrender', function () {
        this.topToolbar.hide();
    }, this);
};
Ext.extend(msPromoCode.window.gridCouponOrders, MODx.grid.Grid, {
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
            header: _('mspromocode_order_coupon'),
            dataIndex: 'coupon_id',
            sortable: true,
            hidden: true,
            width: 50,
        });

        r.push({
            header: _('mspromocode_order_id'),
            dataIndex: 'order_id',
            sortable: true,
            hidden: true,
            width: 100,
        }, {
            header: _('mspromocode_order_num'),
            dataIndex: 'order_num',
            sortable: true,
            width: 200,
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
Ext.reg('mspromocode-window-grid-coupon-orders', msPromoCode.window.gridCouponOrders);