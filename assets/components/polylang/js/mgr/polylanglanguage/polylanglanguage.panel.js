Polylang.panel.PolylangLanguage = function (config) {
    config = config || {};
    Ext.apply(config, {
        border: false,
        baseCls: 'modx-formpanel',
        cls: 'container',
        items: [{
            html: '<h2>' + _('polylang_polylanglanguage_title') + '</h2>',
            border: false,
            cls: 'modx-page-header'
        }, {
            xtype: 'modx-tabs',
            id: 'polylang-polylanglanguage-tabs',
            defaults: {border: true, autoHeight: true},
            stateEvents: ['tabchange'],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },
            items: [{
                title: _('polylang_tab_polylanglanguage'),
                defaults: {autoHeight: true},
                items: [{
                    xtype: 'polylang-grid-polylanglanguage',
                    cls: 'main-wrapper',
                    preventRender: true
                }]
            }, {
                title: _('polylang_tab_polylangfield'),
                defaults: {autoHeight: true},
                items: [{
                    xtype: 'polylang-polylangfield-filter'
                }, {
                    xtype: 'polylang-grid-polylangfield',
                    cls: 'main-wrapper',
                    preventRender: true
                }]
            }, {
                title: _('polylang_tab_polylangtvtmplvars'),
                defaults: {autoHeight: true},
                items: [{
                    html: '<p>' + _('polylang_polylangtvtmplvars_intro_msg') + '</p>',
                    xtype: 'modx-description'
                }, {
                    xtype: 'polylang-polylangtvtmplvars-filter'
                }, {
                    xtype: 'polylang-grid-polylangtvtmplvars',
                    cls: 'main-wrapper',
                    preventRender: true
                }]
            }]
        }]
    });
    Polylang.panel.PolylangLanguage.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.panel.PolylangLanguage, MODx.Panel);
Ext.reg('polylang-panel-polylanglanguage', Polylang.panel.PolylangLanguage);