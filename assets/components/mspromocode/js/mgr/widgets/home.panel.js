msPromoCode.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'mspromocode-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('mspromocode') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            id: 'modx-tabs-mspromocode-main',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            stateEvents: ['tabchange'],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())}
            },
            items: [{
                title: _('mspromocode_tab_coupons'),
                layout: 'anchor',
                items: [{
                    html: _('mspromocode_coupons_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'mspromocode-grid-coupons',
                    cls: 'main-wrapper',
                    owner: 'coupon',
                    actions: false,
                }]
            }, {
                title: _('mspromocode_tab_actions'),
                layout: 'anchor',
                cls: 'modx-panel modx-panel-subtabs',
                items: [{
                    xtype: 'modx-tabs',
                    id: 'modx-tabs-mspromocode-actions',
                    defaults: {border: true, autoHeight: true},
                    border: true,
                    cls: 'x-tabpanel-in-tabpanel-bwrap',
                    hideMode: 'offsets',
                    stateEvents: ['tabchange'],
                    getState: function () {
                        return {activeTab: this.items.indexOf(this.getActiveTab())}
                    },
                    items: [{
                        title: _('mspromocode_tab_actions_list'),
                        layout: 'anchor',
                        items: [{
                            html: _('mspromocode_actions_intro_msg'),
                            cls: 'panel-desc',
                        }, {
                            xtype: 'mspromocode-grid-actions',
                            cls: 'main-wrapper',
                        }]
                    }, {
                        title: _('mspromocode_tab_actions_coupons'),
                        layout: 'anchor',
                        items: [{
                            html: _('mspromocode_action_coupons_intro_msg'),
                            cls: 'panel-desc',
                        }, {
                            xtype: 'mspromocode-grid-coupons',
                            cls: 'main-wrapper',
                            owner: 'action',
                            actions: true,
                        }]
                    }]
                }]
            }]
        }]
    });
    msPromoCode.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.panel.Home, MODx.Panel);
Ext.reg('mspromocode-panel-home', msPromoCode.panel.Home);