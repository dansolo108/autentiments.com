Ext.onReady(function () {
    Ext.ComponentMgr.onAvailable("modx-resource-tabs", function () {
        var tabs = this,
            insertIndex = 1,
            insertAfter = 'modx-page-settings';
        tabs.on("beforerender", function () {
            Ext.each(tabs.items.items, function (item, index) {
                if (item.id == insertAfter) {
                    insertIndex = index + 1;
                    return false;
                }
            });
            tabs.insert(insertIndex, {
                title: _('polylang_content_tab_localization'),
                layout: 'form',
                anchor: '100%',
                autoHeight: true,
                items: [{
                    xtype: 'polylang-grid-polylangcontent',
                    cls: 'main-wrapper',
                    content_id: Polylang.config.rid || 0
                }]
            });
        });
    });
});