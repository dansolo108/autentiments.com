Ext.namespace('msPromoCode.functions');

msPromoCode.functions.codeGen = function (codeGenCmp, codeCmp) {
    var value = codeGenCmp.getValue();
    var newCode = msPromoCode.utils.genRegExpString(value);

    codeCmp.setValue(newCode);
}

msPromoCode.window.GenerateCoupon = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'mspromocode-coupon-window-generate';
    }
    if (typeof config.action_id == 'undefined') {
        config.action_id = 0;
    }
    if (typeof config.action_ref == 'undefined') {
        config.action_ref = false;
    }

    Ext.applyIf(config, {
        title: _('mspromocode_coupon_generate'),
        // bwrapCssClass: 'x-window-with-tabs',
        width: 500,
        autoHeight: true,
        modal: true,
        collapsible: false,
        maximizable: false,
        labelAlign: 'left',
        labelWidth: 140,
        url: msPromoCode.config['connector_url'],
        baseParams: {
            action: 'mgr/coupon/generate',
            resource_id: msPromoCode.resource_id,
            resource_type: msPromoCode.resource_type,
        },
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit()
            },
            scope: this
        }],
        // cls: 'modx-window mspc-window',
        // bodyCssClass: 'mspc-window-tabs',
    });
    msPromoCode.window.GenerateCoupon.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.window.GenerateCoupon, MODx.Window,
    {
        getFields: function (config) {
            var r = [];

            r.push({
                xtype: 'hidden',
                name: 'action_id',
                id: config.id + '-action_id',
                value: config.action_id,
                originalValue: config.action_id,
            });
            r.push({
                xtype: 'hidden',
                name: 'action_ref',
                id: config.id + '-action_ref',
                value: config.action_ref,
                originalValue: config.action_ref,
            });
            r.push({
                xtype: 'textfield',
                name: 'mask',
                id: config.id + '-mask',
                fieldLabel: _('mspromocode_coupon_mask'),
                anchor: '100%',
                allowBlank: false,
                originalValue: msPromoCode.config['regexp_gen_code'],
            });
            if (!config.action_ref) {
                r.push({
                    xtype: 'numberfield',
                    name: 'count',
                    id: config.id + '-count',
                    fieldLabel: _('mspromocode_coupon_count'),
                    anchor: '100%',
                    allowBlank: false,
                    originalValue: 1,
                });
            }

            return r;
        },

        loadDropZones: function () {
        },

    });
Ext.reg('mspromocode-coupon-window-generate', msPromoCode.window.GenerateCoupon);

msPromoCode.window.CreateCoupon = function (config) {
    msPromoCode.window.tmp = {};
    msPromoCode.window.tmp.exclude_ids = 0;

    config = config || {};
    if (!config.id) {
        config.id = 'mspromocode-coupon-window-create';
    }
    // msPromoCode.window.tmp.id = config.id;

    Ext.applyIf(config, {
        title: _('mspromocode_coupon_create'),
        bwrapCssClass: 'x-window-with-tabs',
        width: 700,
        autoHeight: true,
        modal: true,
        collapsible: false,
        url: msPromoCode.config['connector_url'],
        baseParams: {
            action: 'mgr/coupon/create',
            resource_id: msPromoCode.resource_id,
            resource_type: msPromoCode.resource_type,
        },
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit()
            },
            scope: this
        }],
        cls: 'modx-window mspc-window',
        bodyCssClass: 'mspc-window-tabs',
    });
    msPromoCode.window.CreateCoupon.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.window.CreateCoupon, MODx.Window, {
    getFields: function (config) {
        r = [{
            xtype: 'modx-tabs',
            id: 'modx-tabs-' + config.id,
            overCls: 'mspc-tabs',
            bodyStyle: {
                background: 'transparent'
            },
            border: true,
            deferredRender: false,
            autoHeight: true,
            autoScroll: false,
            anchor: '100% 100%',
            items: [
                msPromoCode.window.tabCouponMain(config),
            ],
        }];

        // r[0].items.push([{
        //     id: 'conditions-tab-' + config.id,
        //     title: _('mspromocode_tab_conditions'),
        //     layout: 'form',
        //     cls: 'modx-panel',
        //     autoHeight: true,
        //     forceLayout: true,
        //     labelWidth: 100,
        //     items: [{
        //         html: _('mspromocode_msg_begin_save_object'),
        //         cls: 'panel-desc',
        //         style: {
        //             fontSize: '170%',
        //             textAlign: 'center',
        //         },
        //     }],
        // }, {
        //     id: 'links-tab-' + config.id,
        //     title: _('mspromocode_tab_links'),
        //     layout: 'form',
        //     cls: 'modx-panel',
        //     autoHeight: true,
        //     anchor: '100% 100%',
        //     labelWidth: 100,
        //     items: [{
        //         html: _('mspromocode_msg_begin_save_object'),
        //         cls: 'panel-desc',
        //         style: {
        //             fontSize: '170%',
        //             textAlign: 'center',
        //         },
        //     }],
        // }]);

        return r;
    },

    loadDropZones: function () {
    },

});
Ext.reg('mspromocode-coupon-window-create', msPromoCode.window.CreateCoupon);

msPromoCode.window.UpdateCoupon = function (config) {
    msPromoCode.window.tmp = {};
    msPromoCode.window.tmp.exclude_ids = 0;

    config = config || {};
    if (!config.id) {
        config.id = 'mspromocode-coupon-window-update';
    }
    // msPromoCode.window.tmp.id = config.id;

    Ext.applyIf(config, {
        title: _('mspromocode_coupon_update'),
        width: 700,
        autoHeight: true,
        modal: true,
        collapsible: false,
        url: msPromoCode.config['connector_url'],
        action: 'mgr/coupon/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit()
            },
            scope: this
        }],
        cls: 'modx-window mspc-window',
        bodyCssClass: 'mspc-window-tabs',
    });
    msPromoCode.window.UpdateCoupon.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.window.UpdateCoupon, MODx.Window, {
    getFields: function (config) {
        var add = {
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        };
        var data = config.record ? config.record.object : null;

        var r = [{
            xtype: 'modx-tabs',
            id: 'modx-tabs-' + config.id,
            bodyStyle: {
                background: 'transparent'
            },
            border: true,
            deferredRender: false,
            autoHeight: true,
            autoScroll: false,
            anchor: '100% 100%',
            // stateEvents: ['tabchange'],
            // getState: function () {
            //     return {
            //         activeTab: this.items.indexOf(this.getActiveTab()),
            //     };
            // },
            items: [
                msPromoCode.window.tabCouponMain(config, add),
            ],
        }];

        if (config.record.object.action_id == '') {
            r[0].items.push([
                msPromoCode.window.tabCouponConditions(config),
            ]);

            if (!data || (data && !data['allcart'])) {
                r[0].items.push({
                    id: 'links-tab-' + config.id,
                    title: _('mspromocode_tab_links'),
                    layout: 'form',
                    cls: 'modx-panel modx-panel-subtabs',
                    autoHeight: true,
                    anchor: '100% 100%',
                    labelWidth: 100,
                    items: [{
                        xtype: 'modx-tabs',
                        id: 'modx-subtabs-' + config.id,
                        bodyStyle: {
                            background: 'transparent'
                        },
                        border: true,
                        deferredRender: false,
                        autoHeight: true,
                        autoScroll: false,
                        anchor: '100% 100%',
                        // stateEvents: ['tabchange'],
                        // getState: function () {
                        //     return {
                        //         activeTab: this.items.indexOf(this.getActiveTab()),
                        //     };
                        // },
                        items: [
                            msPromoCode.window.tabCouponProducts(config),
                            msPromoCode.window.tabCouponCategories(config),
                        ],
                    }],
                });
            }
        }

        r[0].items.push([
            msPromoCode.window.tabCouponOrders(config),
        ]);

        return r;
    },
});
Ext.reg('mspromocode-coupon-window-update', msPromoCode.window.UpdateCoupon);

msPromoCode.window.tabCouponMain = function (config, add) {
    add = add || {};
    var data = config.record ? config.record.object : null;
    var actions = ('record' in config && 'object' in config.record && config.record.object.action_id > 0 && config.record.object.action != '');

    // console.log('config.record', config.record);

    var r = {
        id: 'main-tab-' + config.id,
        title: _('mspromocode_tab_main'),
        layout: 'form',
        cls: 'modx-panel',
        autoHeight: true,
        anchor: '100% 100%',
        labelWidth: 100,
        items: [],
    };

    if (actions) {
        var action_name = config.record.object.action;
        r.items.push({
            html: '<b>' + _('mspromocode_coupon_action') + ':</b> ' + action_name,
            style: 'margin: 0 0 10px; color: #555; font-size: 110%;',
        });
    }

    r.items.push({
        cls: 'panel-desc',
        items: [{
            fieldLabel: _('mspromocode_coupon_code_gen'),
            layout: 'column',
            border: false,
            anchor: '100%',
            style: {margin: '0'},
            items: [{
                columnWidth: .75,
                layout: 'form',
                style: {marginTop: '-10px', marginRight: '5px'},
                items: [{
                    xtype: 'textfield',
                    name: 'code_gen',
                    id: config.id + '-code-gen',
                    hideLabel: true,
                    anchor: '100%',
                    originalValue: msPromoCode.config['regexp_gen_code'],
                    //allowBlank: false,
                },],
            }, {
                columnWidth: .25,
                layout: 'form',
                style: {marginLeft: '5px'},
                items: [{
                    xtype: 'button',
                    id: config.id + '-code-gen-btn',
                    hideLabel: true,
                    text: _('mspromocode_coupon_code_gen_btn'),
                    cls: 'mspc-btn-primary3',
                    anchor: '100%',
                    style: 'padding:5px 5px 7px;',
                    listeners: {
                        click: {
                            fn: function () {
                                var codeGenCmp = Ext.getCmp(config.id + '-code-gen');
                                var codeCmp = Ext.getCmp(config.id + '-code');
                                msPromoCode.functions.codeGen(codeGenCmp, codeCmp)
                            },
                            scope: this
                        }
                    }
                },],
            }]
        },],
    });

    if (actions) {
        r.items.push({
            layout: 'column',
            border: false,
            anchor: '100%',
            style: {margin: '0'},
            items: [{
                columnWidth: .5,
                layout: 'form',
                style: {marginRight: '5px'},
                items: [{
                    xtype: 'textfield',
                    name: 'code',
                    id: config.id + '-code',
                    fieldLabel: _('mspromocode_coupon_code'),
                    emptyText: _('mspromocode_coupon_code_placeholder'),
                    anchor: '100%',
                    // disabled: config.record.object.activated,
                    allowBlank: false,
                }],
            }, {
                columnWidth: .5,
                layout: 'form',
                style: {marginLeft: '5px'},
                items: [{
                    layout: 'column',
                    border: false,
                    anchor: '100%',
                    style: {margin: '0'},
                    items: [{
                        columnWidth: .5,
                        layout: 'form',
                        style: {marginRight: '5px'},
                        items: [{
                            xtype: 'textfield',
                            name: 'discount',
                            id: config.id + '-discount',
                            fieldLabel: _('mspromocode_coupon_discount'),
                            anchor: '100%',
                            originalValue: '0%',
                        }],
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        style: {marginLeft: '5px'},
                        items: [{
                            xtype: 'textfield',
                            name: 'count',
                            id: config.id + '-count',
                            fieldLabel: _('mspromocode_coupon_count'),
                            emptyText: _('mspromocode_coupon_count_unlimit'),
                            anchor: '100%',
                            originalValue: '',
                            disabled: actions,
                        }],
                    }],
                }],
            }],
        });
    } else {
        r.items.push({
            layout: 'column',
            border: false,
            anchor: '100%',
            style: {margin: '0'},
            items: [{
                columnWidth: .5,
                layout: 'form',
                style: {marginRight: '5px'},
                items: [{
                    xtype: 'textfield',
                    name: 'code',
                    id: config.id + '-code',
                    fieldLabel: _('mspromocode_coupon_code'),
                    emptyText: _('mspromocode_coupon_code_placeholder'),
                    anchor: '100%',
                    // disabled: config.record.object.activated,
                    allowBlank: false,
                }],
            }, {
                columnWidth: .5,
                layout: 'form',
                style: {marginLeft: '5px'},
                items: [{
                    xtype: 'textfield',
                    name: 'count',
                    id: config.id + '-count',
                    fieldLabel: _('mspromocode_coupon_count'),
                    emptyText: _('mspromocode_coupon_count_unlimit'),
                    anchor: '100%',
                    originalValue: '',
                    disabled: actions,
                }],
            }]
        });

        r.items.push({
            layout: 'column',
            border: false,
            anchor: '100%',
            style: {margin: '0'},
            items: [{
                columnWidth: .5,
                layout: 'form',
                style: {marginRight: '5px'},
                items: [{
                    xtype: 'textfield',
                    name: 'discount',
                    id: config.id + '-discount',
                    fieldLabel: _('mspromocode_coupon_discount'),
                    anchor: '100%',
                    originalValue: '0%',
                }],
            }, {
                columnWidth: .5,
                layout: 'form',
                style: {marginLeft: '5px'},
                items: [{
                    xtype: 'xcheckbox',
                    name: 'allcart',
                    id: config.id + '-allcart',
                    fieldLabel: _('mspromocode_coupon_allcart'),
                    boxLabel: _('mspromocode_coupon_allcart_desc'),
                    checked: false,
                    disabled: (data ? true : false),
                }],
            }]
        });
    }

    r.items.push({
        layout: 'column',
        border: false,
        anchor: '100%',
        style: {margin: '0'},
        items: [{
            columnWidth: .5,
            layout: 'form',
            style: {marginRight: '5px'},
            items: [{
                xtype: 'minishop2-xdatetime',
                name: 'begins',
                id: config.id + '-begins',
                fieldLabel: _('mspromocode_coupon_begins'),
                anchor: '100%',
            }],
        }, {
            columnWidth: .5,
            layout: 'form',
            style: {marginLeft: '5px'},
            items: [{
                xtype: 'minishop2-xdatetime',
                name: 'ends',
                id: config.id + '-ends',
                fieldLabel: _('mspromocode_coupon_ends'),
                anchor: '100%',
            }],
        }]
    });

    if (!actions) {
        r.items.push({
            xtype: 'textarea',
            name: 'description',
            id: config.id + '-description',
            fieldLabel: _('mspromocode_coupon_description'),
            height: 70,
            anchor: '100%',
        });
    }

    if (actions) {
        r.items.push({
            xtype: 'xcheckbox',
            name: 'freeze',
            id: config.id + '-freeze',
            fieldLabel: _('mspromocode_coupon_freeze'),
            boxLabel: _('mspromocode_coupon_freeze_desc'),
            checked: false,
        });
    } else {
        var tmp = {
            layout: 'column',
            border: false,
            anchor: '100%',
            style: {margin: '0'},
            items: [{
                columnWidth: .5,
                layout: 'form',
                style: {marginRight: '5px'},
                items: [{
                    xtype: 'xcheckbox',
                    name: 'active',
                    id: config.id + '-active',
                    fieldLabel: _('mspromocode_coupon_active'),
                    boxLabel: _('mspromocode_coupon_active_desc'),
                    checked: true,
                }],
            }],
        };
        if (!data || !data['allcart']) {
            tmp.items.push({
                columnWidth: .5,
                layout: 'form',
                style: {marginLeft: '5px'},
                items: [{
                    xtype: 'xcheckbox',
                    name: 'oldprice',
                    id: config.id + '-oldprice',
                    fieldLabel: _('mspromocode_coupon_oldprice'),
                    boxLabel: _('mspromocode_coupon_oldprice_desc'),
                    checked: false,
                }],
            });
        }
        r.items.push(tmp);
    }

    r.items.push(add);

    return r;
};

msPromoCode.window.tabCouponConditions = function (config) {
    return msPromoCode.functions.tabConditions(config);
};

msPromoCode.window.tabCouponProducts = function (config) {
    var type = ['product', 'products'];
    return msPromoCode.functions.tabResources(config, type);
};

msPromoCode.window.tabCouponCategories = function (config) {
    var type = ['category', 'categories'];
    return msPromoCode.functions.tabResources(config, type);
};

msPromoCode.window.tabCouponOrders = function (config) {
    return msPromoCode.functions.tabOrders(config);
};