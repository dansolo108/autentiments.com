msPromoCode.ms2tabProduct = {
    title: _('mspromocode_tab_coupons'),
    hideMode: 'offsets',
    items: [{
        xtype: 'mspromocode-grid-coupons',
    }]
};

// Для miniShop2 => v2.4
Ext.ComponentMgr.onAvailable('minishop2-product-tabs', function () {
    this.on('beforerender', function () {
        this.add(msPromoCode.ms2tabProduct);
    });
});

// Для miniShop2 <= 2.3
Ext.ComponentMgr.onAvailable('minishop2-product-settings-panel', function () {
    this.on('beforerender', function () {
        this.add(msPromoCode.ms2tabProduct);
    });
});
Ext.ComponentMgr.onAvailable('minishop2-product-settings-panel-horizontal', function () {
    this.on('beforerender', function () {
        this.items.items[1].add(msPromoCode.ms2tabProduct);
    });
});