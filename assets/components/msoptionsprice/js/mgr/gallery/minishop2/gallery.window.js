if (typeof ms2Gallery === 'undefined') {
    msoptionsprice.window.Image = function (config) {
        config = config || {};

        Ext.applyIf(config, {
            url: miniShop2.config['connector_url'],
        });
        msoptionsprice.window.Image.superclass.constructor.call(this, config);
    };
    Ext.extend(msoptionsprice.window.Image, miniShop2.window.Image, {});
}
else if (MODx.config['ms2gallery_sync_ms2'] === '1') {
    msoptionsprice.window.Image = function (config) {
        config = config || {};

        Ext.applyIf(config, {
            url: ms2Gallery.config['connector_url'],
        });
        msoptionsprice.window.Image.superclass.constructor.call(this, config);
    };
    Ext.extend(msoptionsprice.window.Image, ms2Gallery.window.Image, {});
}

Ext.reg('msoptionsprice-gallery-image', msoptionsprice.window.Image);