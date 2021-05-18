msPromoCode.window.CreateAction = function (config) {
    msPromoCode.window.tmp = {};
    msPromoCode.window.tmp.exclude_ids = 0;

    config = config || {};
    if (!config.id) {
        config.id = 'mspromocode-action-window-create';
    }
    // msPromoCode.window.tmp.id = config.id;

    config.owner = 'action';
    config.actions = true;
    config.action_id = 0;
    config.mode = 'create';

    Ext.applyIf(config, {
        title: _('mspromocode_action_create'),
        bwrapCssClass: 'x-window-with-tabs',
        width: 700,
        autoHeight: true,
        modal: true,
        collapsible: false,
        url: msPromoCode.config.connector_url,
        action: 'mgr/action/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit()
            },
            scope: this
        }]
    });
    msPromoCode.window.CreateAction.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.window.CreateAction, MODx.Window, {
    getFields: function (config) {
        return [{
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
            items: [
                msPromoCode.window.tabActionMain(config),
                {
                    id: 'grid-coupons-tab-' + config.id,
                    title: _('mspromocode_tab_action_coupons'),
                    layout: 'form',
                    cls: 'modx-panel',
                    autoHeight: true,
                    forceLayout: true,
                    labelWidth: 100,
                    items: [{
                        html: _('mspromocode_msg_begin_save_object'),
                        cls: 'panel-desc',
                        style: {
                            fontSize: '170%',
                            textAlign: 'center',
                        },
                    }],
                }
            ],
        }];
    },

    loadDropZones: function () {
    }
});
Ext.reg('mspromocode-action-window-create', msPromoCode.window.CreateAction);


msPromoCode.window.UpdateAction = function (config) {
    msPromoCode.window.tmp = {};
    msPromoCode.window.tmp.exclude_ids = 0;

    config = config || {};
    if (!config.id) {
        config.id = 'mspromocode-action-window-update';
    }
    // msPromoCode.window.tmp.id = config.id;

    config.owner = 'action';
    config.actions = true;
    config.action_id = config.record.object.id;
    config.mode = 'update';

    Ext.applyIf(config, {
        title: _('mspromocode_action_update'),
        width: 900,
        autoHeight: true,
        modal: true,
        collapsible: false,
        url: msPromoCode.config.connector_url,
        action: 'mgr/action/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit()
            },
            scope: this
        }]
    });
    msPromoCode.window.UpdateAction.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.window.UpdateAction, MODx.Window, {
    getFields: function (config) {
        var add = {
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        };

        console.log('config.record.object.ref', config.record.object.ref);

        return [{
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
            stateEvents: ['tabchange'],
            getState: function () {
                return {
                    activeTab: this.items.indexOf(this.getActiveTab())
                }
            },
            items: [
                msPromoCode.window.tabActionMain(config, add),
                {
                    id: 'grid-coupons-tab-' + config.id,
                    title: _('mspromocode_tab_action_coupons'),
                    layout: 'form',
                    cls: 'modx-panel',
                    autoHeight: true,
                    // forceLayout: true,
                    labelWidth: 100,
                    items: [{
                        html: _('mspromocode_action_coupons_intro_msg'),
                        cls: 'panel-desc',
                        style: 'margin:0',
                    }, {
                        xtype: 'mspromocode-grid-coupons',
                        id: 'mspromocode-grid-coupons-' + config.id,
                        // cls: 'main-wrapper',
                        paging: true,
                        pageSize: 5,
                        remoteSort: true,
                        autoHeight: true,

                        owner: 'action',
                        actions: true,
                        action_id: config.record.object.id,
                        action_ref: config.record.object.ref,
                    }],
                },
                msPromoCode.window.tabActionProducts(config),
                msPromoCode.window.tabActionCategories(config),
            ],
        }];
    },

    loadDropZones: function () {
    }
});
Ext.reg('mspromocode-action-window-update', msPromoCode.window.UpdateAction);


msPromoCode.window.tabActionMain = function (config, add) {
    add = add || {};

    var r = {
        id: 'main-tab-' + config.id,
        title: _('mspromocode_tab_main'),
        layout: 'form',
        cls: 'modx-panel',
        autoHeight: true,
        anchor: '100% 100%',
        labelWidth: 100,
        items: [{
            layout: 'column',
            border: false,
            anchor: '100%',
            style: {
                margin: '10px 0 0 0'
            },
            items: [{
                columnWidth: .7,
                layout: 'form',
                items: [{
                    xtype: 'textfield',
                    name: 'name',
                    id: config.id + '-name',
                    fieldLabel: _('mspromocode_action_name'),
                    anchor: '100%',
                    // allowBlank: false,
                },],
            }, {
                columnWidth: .3,
                layout: 'form',
                style: {
                    margin: '0 0 0 10px'
                },
                items: [{
                    xtype: 'textfield',
                    name: 'discount',
                    id: config.id + '-discount',
                    fieldLabel: _('mspromocode_action_discount'),
                    anchor: '100%',
                    originalValue: '0%',
                    // allowBlank: false,
                },],
            }]
        }, {
            layout: 'column',
            border: false,
            anchor: '100%',
            style: {
                margin: '10px 0 0 0'
            },
            items: [{
                columnWidth: .5,
                layout: 'form',
                items: [{
                    xtype: 'minishop2-xdatetime',
                    name: 'begins',
                    id: config.id + '-begins',
                    fieldLabel: _('mspromocode_action_begins'),
                    anchor: '100%',
                }],
            }, {
                columnWidth: .5,
                layout: 'form',
                style: {
                    margin: '0 0 0 10px'
                },
                items: [{
                    xtype: 'minishop2-xdatetime',
                    name: 'ends',
                    id: config.id + '-ends',
                    fieldLabel: _('mspromocode_action_ends'),
                    anchor: '100%',
                }],
            }]
        }, {
            xtype: 'textarea',
            name: 'description',
            id: config.id + '-description',
            fieldLabel: _('mspromocode_action_description'),
            height: 70,
            anchor: '100%',
        }, {
            xtype: config.mode == 'create'
                ? 'xcheckbox'
                : 'hidden',
            name: 'ref',
            id: config.id + '-ref',
            fieldLabel: _('mspromocode_action_ref'),
            boxLabel: _('mspromocode_action_ref_desc'),
            checked: false,
        }, {
            xtype: 'xcheckbox',
            name: 'active',
            id: config.id + '-active',
            fieldLabel: _('mspromocode_action_active'),
            boxLabel: _('mspromocode_action_active_desc'),
            checked: true,
        }],
    };

    r.items.push(add);

    return r;
}

msPromoCode.window.tabActionProducts = function (config) {
    type = ['product', 'products'];
    return msPromoCode.functions.tabResources(config, type);
}

msPromoCode.window.tabActionCategories = function (config) {
    type = ['category', 'categories'];
    return msPromoCode.functions.tabResources(config, type);
}