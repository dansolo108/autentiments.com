Polylang.grid.PolylangTvTmplvars = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'polylang-grid-polylangtvtmplvars';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/polylangtvtmplvars/getList',
            sort: 'id',
            dir: 'DESC',
        },
        autoExpandColumn: 'id',
        save_action: 'mgr/polylangtvtmplvars/updateFromGrid',
    });

    Polylang.grid.PolylangTvTmplvars.superclass.constructor.call(this, config)
};
Ext.extend(Polylang.grid.PolylangTvTmplvars, Polylang.grid.Default, {
    getFields: function () {
        return ['id', 'culture_key', 'language_name', 'tmplvarid', 'tv_name', 'tv_caption', 'values', 'default_text', 'actions'];
    },
    getColumns: function () {
        return [{
            header: _('id'),
            dataIndex: 'id',
            sortable: true,
            hidden: true,
        }, {
            header: _('polylang_tvtmplvars_header_culture_key'),
            dataIndex: 'language_name',
            sortable: true,
        }, {
            header: _('polylang_tvtmplvars_header_tv_name'),
            dataIndex: 'tv_name',
            sortable: true,
        }, {
            header: _('polylang_tvtmplvars_header_tv_caption'),
            dataIndex: 'tv_caption',
            sortable: true,
        }, {
            header: _('polylang_tvtmplvars_header_values'),
            dataIndex: 'values',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_tvtmplvars_header_default_text'),
            dataIndex: 'default_text',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_tvtmplvars_header_actions'),
            dataIndex: 'actions',
            renderer: Polylang.utils.renderActions,
            width: 60

        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('polylang_tvtmplvars_btn_create'),
            handler: this.addItem,
            cls: 'primary-button',
            scope: this
        });
        /*tbar.push({
            text: '<i class="fa fa-cogs"></i> ',
            menu: [{
                text: '<i class="fa fa-plus"></i> ' + _('polylang_tvtmplvars_btn_create'),
                cls: 'polylang-cogs',
                handler: this.addItem,
                scope: this
            }, '-', {
                text: '<i class="fa fa-refresh"></i> ' + _('polylang_tvtmplvars_btn_update'),
                cls: 'polylang-cogs',
                handler: this.updateItem,
                scope: this
            }]
        });*/
        return tbar;
    },
    actionItem: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/polylangtvtmplvars/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (response) {
                        this.refresh();
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        })
    },
    addItem: function (btn, e, row) {
        var record = {};
        var w = Ext.getCmp('polylang-window-polylangtvtmplvars-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'polylang-window-polylangtvtmplvars-create',
            id: 'polylang-window-polylangtvtmplvars-create',
            record: record,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(record);
        w.show(e.target);
    },
    updateItem: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/polylangtvtmplvars/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('polylang-window-polylangtvtmplvars-update');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'polylang-window-polylangtvtmplvars-update',
                            id: 'polylang-window-polylangtvtmplvars-update',
                            record: r.object,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                },
                            }
                        });
                        w.fp.getForm().reset();
                        w.fp.getForm().setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
    removeItem: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('polylang_tvtmplvars_title_win_remove'),
            ids.length > 1
                ? _('polylang_tvtmplvars_confirm.multiple_remove')
                : _('polylang_tvtmplvars_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionItem('remove');
                }
            }, this
        );
    }

});
Ext.reg('polylang-grid-polylangtvtmplvars', Polylang.grid.PolylangTvTmplvars);


Polylang.window.CreatePolylangTvTmplvars = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: config.record.id ? _('polylang_tvtmplvars_title_win_update') : _('polylang_tvtmplvars_title_win_create'),
        baseParams: {
            action: config.record.id ? 'mgr/polylangtvtmplvars/update' : 'mgr/polylangtvtmplvars/create'
        }
    });
    Polylang.window.CreatePolylangTvTmplvars.superclass.constructor.call(this, config);
    this.on('afterrender', function () {
        this.injectFieldsBtnTranslate();
    }, this);
};
Ext.extend(Polylang.window.CreatePolylangTvTmplvars, Polylang.window.Default, {
    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
        }, {
            xtype: 'polylang-combo-language',
            id: 'polylang-tvtmplvars-language',
            fieldLabel: _('polylang_tvtmplvars_label_culture_key'),
            name: 'culture_key',
            allowBlank: false,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_tvtmplvars_label_culture_key_help'),
            cls: 'desc-under',
        }, {
            xtype: 'polylang-combo-tvs',
            fieldLabel: _('polylang_tvtmplvars_label_tmplvarid'),
            name: 'tmplvarid',
            onlyPolylang: 1,
            allowBlank: false,
            anchor: '100%',
            listeners: {
                select: {
                    fn: this.onTvChange,
                    scope: this
                }
            }
        }, {
            xtype: 'label',
            html: _('polylang_tvtmplvars_label_tmplvarid_help'),
            cls: 'desc-under',
        }, {
            xtype: 'textarea',
            id: 'polylang-tvtmplvars-values',
            fieldLabel: _('polylang_tvtmplvars_label_values'),
            name: 'values',
            translate: 1,
            allowBlank: true,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_tvtmplvars_label_values_help'),
            cls: 'desc-under',
        }, {
            xtype: 'textarea',
            id: 'polylang-tvtmplvars-default-text',
            fieldLabel: _('polylang_tvtmplvars_label_default_text'),
            name: 'default_text',
            translate: 1,
            allowBlank: true,
            anchor: '100%',
        }, {
            xtype: 'label',
            html: _('polylang_tvtmplvars_label_default_text_help'),
            cls: 'desc-under',
        }];
    },
    onTvChange: function (combo, records) {
        if (!this.record.id) {
            var f = this.fp.getForm();
            f.findField('values').setValue(records.data.elements);
            f.findField('default_text').setValue(records.data.default_text);
        }
    },
    translate: function (id) {
        var el = Ext.getCmp(id);
        if (!el || !el.getValue()) return;
        var language = Ext.getCmp('polylang-tvtmplvars-language');
        this.getEl().mask(_('loading'), 'x-mask-loading');
        MODx.Ajax.request({
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/translator/translate',
                text: el.getValue(),
                to: language.getValue(),
                from: Polylang.config.defaultLanguage,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        el.setValue(r.object.translate);
                        this.getEl().unmask();
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        this.getEl().unmask();
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        });
    },
    injectFieldsBtnTranslate: function () {
        var cls = 'polylang-translate';
        if (Polylang.config.showTranslateBtn == 1) {
            cls += ' show';
        }
        this.fp.getForm().items.each(function (item) {
            if (item.translate == 1 && item.hasOwnProperty('label')) {
                var trigger = '<a id="polylang-translate-' + item.id + '" class="' + cls + '" title="' + _('polylang_translator_translate') + '" ' +
                    'data-id="' + item.id + '" ' + '"></a>';
                Ext.DomHelper.append(item.label, trigger);
            }
        });
        Ext.select('.polylang-translate').on('click', function (e, t, o) {
            if (this.isEnableBtnTranslate(t.id)) {
                this.translate(t.dataset.id);
            }
        }, this);
    },
    isEnableBtnTranslate: function (id) {
        var el = Ext.get(id);
        if (el) {
            return !el.hasClass('disabled');
        }
        return false;
    },
});
Ext.reg('polylang-window-polylangtvtmplvars-create', Polylang.window.CreatePolylangTvTmplvars);

Polylang.window.UpdatePolylangTvTmplvars = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    Polylang.window.UpdatePolylangTvTmplvars.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.window.UpdatePolylangTvTmplvars, Polylang.window.CreatePolylangTvTmplvars);
Ext.reg('polylang-window-polylangtvtmplvars-update', Polylang.window.UpdatePolylangTvTmplvars);
