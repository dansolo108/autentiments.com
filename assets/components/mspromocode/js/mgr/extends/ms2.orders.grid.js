msPromoCode.grid.ms2Orders = function (config) {
    Ext.applyIf(config, {
        url: msPromoCode.config['connector_url'],
        baseParams: {
            action: 'mgr/orders/getlist',
            sort: 'id',
            dir: 'desc',
        },
    });
    msPromoCode.grid.ms2Orders.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.grid.ms2Orders, Ext.ComponentMgr.types['minishop2-grid-orders'], {});
Ext.reg('minishop2-grid-orders', msPromoCode.grid.ms2Orders);