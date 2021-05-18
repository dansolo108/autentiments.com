Polylang.panel.PolylangContent = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false,
        baseCls: 'modx-formpanel',
        cls: 'container',
        items: [{
            html: '<h2>'+_('polylang_polylangcontent_title')+'</h2>',
            border: false,
            cls: 'modx-page-header'
        }, {
            xtype: 'modx-tabs',
            id: 'polylang-polylangcontent-tabs',
            defaults: { border: true ,autoHeight: true },
            stateEvents: ['tabchange'],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },
            items: [{
                title: _('polylang_tab_polylangcontent'),
                defaults: { autoHeight: true },
                items: [{
                 xtype: 'polylang-grid-polylangcontent',
                 cls: 'main-wrapper',
                 preventRender: true
                 }]
            }]
        }]
    });
    Polylang.panel.PolylangContent.superclass.constructor.call(this,config);
};
Ext.extend(Polylang.panel.PolylangContent,MODx.Panel);
Ext.reg('polylang-panel-polylangcontent',Polylang.panel.PolylangContent);