Polylang.panel.PolylangTvTmplvarsFilter = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'polylang-polylangtvtmplvars-filter';
    }
    Ext.applyIf(config, {
        border: false,
        items: this.getFields(config),
        listeners: this.getListeners(config),
        // buttons: this.getButtons(config),
        keys: this.getKeys(config),
    });
    Polylang.panel.PolylangTvTmplvarsFilter.superclass.constructor.call(this, config);
    this.setupEvents(config);
};
Ext.extend(Polylang.panel.PolylangTvTmplvarsFilter, MODx.FormPanel, {
    grid: null,
    getListeners: function (config) {
        return {};
    },
    setupEvents: function (config) {
        this.addEvents('reset');
        this.on('beforerender', function () {
            this.grid = Ext.getCmp('polylang-grid-polylangtvtmplvars');
        }, this);
        this.on('change', function () {
            this.submit();
        }, this);
    },
    getFields: function (config) {
        return [{
            xtype: 'fieldset',
            cls: 'container',
            style: 'margin-bottom: 0;',
            title: _('polylang_filter'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'polylang-polylangtvtmplvars-filter-fieldset',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                layout: 'column',
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    items: this.getLeftFields(config),
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    items: this.getRightFields(config),
                }],
            }]
        }];
    },
    getLeftFields: function (config) {
        return [{
            layout: 'column',
            items: [{
                columnWidth: .5,
                layout: 'form',
                defaults: {
                    anchor: '100%',
                    hideLabel: true,
                },
                items: [{
                    xtype: 'polylang-combo-language',
                    emptyText: _('polylang_polylangtvtmplvars_filter_language'),
                    width: 250,
                    listeners: {
                        select: {
                            fn: function () {
                                this.fireEvent('change');
                            }, scope: this
                        },
                        clear: {
                            fn: function (field) {
                                field.setValue('');
                                this.fireEvent('change');
                            }, scope: this
                        },
                    }
                }],
            }, {
                columnWidth: .5,
                layout: 'form',
                defaults: {
                    anchor: '100%',
                    hideLabel: true,
                },
                items: [{
                    xtype: 'polylang-combo-tvs',
                    emptyText: _('polylang_polylangtvtmplvars_filter_tvs'),
                    onlyPolylang: 1,
                    width: 250,
                    listeners: {
                        select: {
                            fn: function () {
                                this.fireEvent('change');
                            }, scope: this
                        },
                        clear: {
                            fn: function (field) {
                                field.setValue('');
                                this.fireEvent('change');
                            }, scope: this
                        },
                    }
                }],
            }],
        }];
    },
    getCenterFields: function (config) {
        return [];
    },
    getRightFields: function (config) {
        return [{
            layout: 'column',
            items: [{
                columnWidth: 1,
                layout: 'form',
                defaults: {
                    anchor: '100%',
                    hideLabel: true,
                },
                items: [{
                    xtype: 'polylang-combo-search',
                    emptyText: _('polylang_field_filter_search'),
                    width: 250,
                    listeners: {
                        search: {
                            fn: function (field) {
                                this.fireEvent('change');
                            }, scope: this
                        },
                        clear: {
                            fn: function (field) {
                                field.setValue('');
                                this.fireEvent('change');
                            }, scope: this
                        },
                    }
                }]
            }]
        }];
    },
    getButtons: function (config) {
        return [{
            text: '<i class="icon icon-times"></i> ' + _('polylang_filter_reset'),
            handler: this.reset,
            scope: this,
            iconCls: 'x-btn-small',
        }, {
            text: '<i class="icon icon-check"></i> ' + _('polylang_filter_submit'),
            handler: this.submit,
            scope: this,
            cls: 'primary-button',
            iconCls: 'x-btn-small',
        }];
    },
    getKeys: function (config) {
        return [{
            key: Ext.EventObject.ENTER,
            fn: function () {
                this.submit();
            },
            scope: this
        }];
    },
    submit: function () {
        var store = this.grid.getStore();
        var form = this.getForm();
        var values = form.getFieldValues();
        for (var i in values) {
            if (i != undefined && values.hasOwnProperty(i)) {
                store.baseParams[i] = values[i];
            }
        }
        this.refresh();
    },
    reset: function () {
        var store = this.grid.getStore();
        var form = this.getForm();

        form.items.each(function (f) {
            if (f.name === 'time') return true;
            if (typeof (f['clearValue']) === 'function') {
                f.clearValue();
            } else {
                f.reset();
            }
        });

        var values = form.getValues();
        for (var i in values) {
            if (values.hasOwnProperty(i)) {
                store.baseParams[i] = '';
            }
        }
        this.refresh();
        this.fireEvent('reset', this);
    },
    refresh: function () {
        this.grid.getBottomToolbar().changePage(1);
    },
});
Ext.reg('polylang-polylangtvtmplvars-filter', Polylang.panel.PolylangTvTmplvarsFilter);