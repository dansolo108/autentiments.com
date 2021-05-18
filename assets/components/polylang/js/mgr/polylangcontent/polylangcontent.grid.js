Polylang.grid.PolylangContent = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'polylang-grid-polylangcontent';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/polylangcontent/getList',
            content_id: config.content_id,
            sort: 'Language.rank',
            dir: 'ASC',
        },
        autoExpandColumn: 'id',
        save_action: 'mgr/polylangcontent/updateFromGrid',
    });

    Polylang.grid.PolylangContent.superclass.constructor.call(this, config)
};
Ext.extend(Polylang.grid.PolylangContent, Polylang.grid.Default, {
    getFields: function () {
        return ['id', 'content_id', 'culture_key', 'language_name', 'class_key', 'pagetitle', 'seotitle', 'keywords', 'longtitle', 'menutitle', 'introtext', 'description', 'content', 'active', 'actions'];
    },
    getColumns: function () {
        return [{
            header: _('id'),
            dataIndex: 'id',
            sortable: true,
            hidden: true,
        }, {
            header: _('polylang_content_header_culture_key'),
            dataIndex: 'culture_key',
            sortable: true,
            width: 80,
            renderer: function (value, props, row) {
                return row.data.language_name + ' (' + value + ')';
            }
        }, {
            header: _('polylang_content_header_pagetitle'),
            dataIndex: 'pagetitle',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_content_header_seotitle'),
            dataIndex: 'seotitle',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_content_header_menutitle'),
            dataIndex: 'menutitle',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('polylang_content_header_actions'),
            dataIndex: 'actions',
            renderer: Polylang.utils.renderActions,
            width: 60

        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('polylang_content_btn_create'),
            handler: this.addItem,
            cls: 'primary-button',
            scope: this
        });
        tbar.push({
            text: '<i class="fa fa-cogs"></i> ',
            menu: [{
                text: '<i class="fa fa-globe"></i> ' + _('polylang_content_btn_generate'),
                cls: 'polylang-cogs',
                handler: this.generateItems,
                scope: this
            }]
        });
        tbar.push('->', this.getSearchField());

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
                action: 'mgr/polylangcontent/multiple',
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
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        MODx.Ajax.request({
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/polylangcontent/render',
                rid: Polylang.config.rid,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('polylang-window-polylangcontent');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'polylang-window-polylangcontent',
                            id: 'polylang-window-polylangcontent',
                            record: r.object,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                },
                            }
                        });
                        //  w.fp.getForm().reset();
                        w.fp.getForm().setValues(r.object.data);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
    generateItems: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        this.getEl().mask(_('loading'), 'x-mask-loading');
        MODx.Ajax.request({
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/polylangcontent/generate',
                rid: Polylang.config.rid,
                translate: true,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.getEl().unmask();
                        this.refresh();
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
    updateItem: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        MODx.Ajax.request({
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/polylangcontent/render',
                rid: Polylang.config.rid,
                id: this.menu.record.id,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('polylang-window-polylangcontent');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'polylang-window-polylangcontent',
                            id: 'polylang-window-polylangcontent',
                            record: r.object,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                },
                            }
                        });
                        // w.fp.getForm().reset();
                        w.fp.getForm().setValues(r.object.data);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
    enableItem: function () {
        this.actionItem('enable');
    },
    disableItem: function () {
        this.actionItem('disable');
    },
    removeItem: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('polylang_content_title_win_remove'),
            ids.length > 1
                ? _('polylang_content_confirm.multiple_remove')
                : _('polylang_content_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionItem('remove');
                }
            }, this
        );
    }

});
Ext.reg('polylang-grid-polylangcontent', Polylang.grid.PolylangContent);


Polylang.window.PolylangContent = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: config.record.data.id ? _('polylang_content_title_win_update') : _('polylang_content_title_win_create'),
        width: 1000,
        height: 800,
        autoHeight: false,
        autoScroll: true,
        baseParams: {
            action: config.record.data.id ? 'mgr/polylangcontent/update' : 'mgr/polylangcontent/create'
        }
    });
    Polylang.window.PolylangContent.superclass.constructor.call(this, config);
    this.on('activate', function () {
        this.setup(config);
    }, this);
    this.on('beforeSubmit', this.beforeSubmit, this);
};
Ext.extend(Polylang.window.PolylangContent, Polylang.window.Default, {
    idEditors: [],
    setup: function (config) {
        if (!this.initialized) {
            this.initialized = true;
            Ext.iterate(config.record.data || {}, function (key, val) {
                var item = Ext.getCmp('polylang-' + key);
                if (item) {
                    item.setValue(val || '');
                }
            }, this);
            Ext.getCmp('polylang-polylangcontent-culture_key').on('select', function (combo) {
                var items = this.fp.getForm().items;
                items.each(function (item) {
                    if (item.hasOwnProperty('culture_key')) {
                        item.culture_key = combo.getValue();
                    }
                });
                this.fireEvent('culture-key-changed', combo);
            }, this);
            this.loadEditors();
        }
        this.injectFieldsBtnTranslate();
    },
    getFields: function (config) {
        if (config.record.tvs) {
            config.record.items.push({
                title: _('polylang_content_tab_tvs'),
                id: 'polylang-window-polylangcontent-tab-tvs',
                forceLayout: true,
                deferredRender: false,
                autoLoad: {
                    url: Polylang.config.connector_url,
                    params: {
                        action: 'mgr/polylangtv/render',
                        id: config.record.data.id,
                        rid: Polylang.config.rid,
                    },
                    scripts: true,
                    scope: this
                }
            });
        }
        return [{
            xtype: 'hidden',
            name: 'id',
        }, {
            xtype: 'modx-tabs',
            id: 'polylang-window-polylangcontent-tabs',
            forceLayout: true,
            deferredRender: false,
            defaults: {border: true, autoHeight: true},
            stateEvents: ['tabchange'],
            /* getState: function () {
                 // return {activeTab: this.items.indexOf(this.getActiveTab())};
              },*/
            items: config.record.items
        }];

    },
    getButtons: function (config) {
        return [{
            text: '<i class="icon icon-close"></i> ' + _('cancel'),
            scope: this,
            handler: function () {
                config.closeAction !== 'close'
                    ? this.hide()
                    : this.close();
            }
        }, {
            text: '<i class="icon icon-language"></i> ' + _('polylang_content_btn_translate_all'),
            scope: this,
            handler: this.translateAll
        }, {
            text: '<i class="icon icon-floppy-o"></i> ' + _('save'),
            cls: 'primary-button',
            scope: this,
            handler: this.submit
        }];
    },
    unloadEditor: function (id) {
        if (MODx.unloadRTE) {
            MODx.unloadRTE(id);
        }
        if (window.hasOwnProperty('tinyMCE')) {
            tinyMCE.execCommand("mceRemoveEditor", true, id);
        }
    },
    getIdEditors: function (cls) {
        cls = cls || 'polylang-text-editor';
        var ids = [],
            useEditor = MODx.config.use_editor;
        if (Polylang.config.useResourceEditorStatus == 1) {
            var richtext = Ext.getCmp('modx-resource-richtext');
            if (richtext) {
                useEditor = richtext.getValue() == 1;
            }
        }
        if (
            useEditor &&
            MODx.loadRTE
        ) {
            Ext.select('#' + this.id + ' .' + cls).each(function (el) {
                ids.push(el.id);
            }, this);
        }
        return ids;
    },
    loadEditors: function (cls) {
        this.ids = this.getIdEditors(cls);
        if (this.ids.length) {
            Ext.each(this.ids, function (id) {
                this.idEditors.push(id);
                this.unloadEditor(id);
                MODx.loadRTE(id);
            }, this);
            Ext.select('#' + this.id + ' .tox-tinymce').setStyle({
                height: Polylang.config.editorHeight + 'px',
            });
        }
    },
    unloadEditors: function () {
        if (this.idEditors.length) {
            Ext.each(this.idEditors, function (id) {
                this.unloadEditor(id);
            }, this);
        }
    },
    beforeSubmit: function () {
        if (window.hasOwnProperty('tinyMCE')) {
            tinyMCE.triggerSave();
        } else if (window.hasOwnProperty('CKEDITOR')) {
            if (this.idEditors.length) {
                Ext.each(this.idEditors, function (id) {
                    CKEDITOR.instances[id].updateElement();
                }, this);
            }
        }
    },
    beforeDestroy: function () {
        this.unloadEditors();
        Polylang.window.PolylangContent.superclass.beforeDestroy.call(this);
    },
    injectFieldsBtnTranslate: function () {
        var cls = 'polylang-translate';
        if (Polylang.config.showTranslateBtn == 1) {
            cls += ' show';
        }
        this.fp.getForm().items.each(function (item) {
            if (item.translate == 1 && item.hasOwnProperty('label') && !item.btnTranslate) {
                var trigger = '<a id="polylang-translate-' + item.key + '-' + item.source + '" class="' + cls + '" title="' + _('polylang_translator_translate') + '" ' +
                    'data-key="' + item.key + '" ' +
                    'data-target="' + item.id + '"' +
                    'data-source="' + item.source + '">' +
                    '</a>';
                Ext.DomHelper.append(item.label, trigger);
                item.btnTranslate = true;
            }
        });
        Ext.select('.polylang-translate').on('click', function (e, t, o) {
            if (this.isEnableBtnTranslate(t.id)) {
                var item = this.getBtnTranslateData(t);
                if (parseInt(Polylang.config.disallowTranslationCompletedField)) {
                    if (this.getFieldValue(item.target)) {
                        var label = this.getFieldLabel(item.target),
                            warning = _('polylang_translator_warning_translation_nonempty_field_prohibited', {label: label});
                        MODx.msg.alert(_('warning'), warning);
                        return;
                    }
                }
                this.translate([item]);
            }
        }, this);
    },
    getBtnTranslateData: function (el) {
        var data = {};
        if (!el) return data;
        return {
            id: el.id,
            key: el.dataset.key,
            target: el.dataset.target,
            source: el.dataset.source
        }
    },
    setBtnTranslateNote: function (id, note) {
        var el = Ext.get(id);
        if (el) {
            el.update('<span>' + note + '</span>');
        }
    },
    enableBtnTranslate: function (id, enable) {
        var el = Ext.get(id);
        if (el) {
            if (enable) {
                el.removeClass('disabled');
            } else {
                el.addClass('disabled');
            }
        }
    },
    isEnableBtnTranslate: function (id) {
        var el = Ext.get(id);
        if (el) {
            return !el.hasClass('disabled');
        }
        return false;
    },
    setTranslate: function (items) {
        Ext.iterate(items || {}, function (item) {
            if (!item.hasOwnProperty('text') || item.text == '') {
                this.setBtnTranslateNote(item.id, _('polylang_translator_note_original_field_empty'));
                this.enableBtnTranslate(item.id, false);
                return true;
            }
            var target = Ext.getCmp(item.target);
            if (!target) {
                if (target = Ext.get('tv' + item.target)) {
                    target.dom.value = item.text;
                }
            } else if (target) {
                if (MODx.loadRTE) {
                    if (window.hasOwnProperty('tinyMCE') && tinyMCE.getInstanceById(item.target)) {
                        tinyMCE.getInstanceById(item.target).setContent(item.text);
                    } else if (window.hasOwnProperty('CKEDITOR') && MODx.loadedRTEs[item.target] != undefined) {
                        MODx.loadedRTEs[item.target].editor.setData(item.text);
                    }
                }
                target.setValue(item.text);
            }
        }, this);
    },
    getFieldValue: function (id) {
        var val = '',
            field = this.getFieldById(id);
        if (field) {
            if (field.isTv) {
                val = field.dom.value;
            } else {
                val = field.getValue();
                if (MODx.loadRTE) {
                    if (window.hasOwnProperty('tinyMCE') && tinyMCE.getInstanceById(id)) {
                        val = tinyMCE.getInstanceById(id).getContent();
                    } else if (window.hasOwnProperty('CKEDITOR') && MODx.loadedRTEs[id] != undefined) {
                        val = MODx.loadedRTEs[id].editor.getData();
                    }
                }
            }
        }
        return val;
    },
    getFieldLabel: function (id) {
        var val = '',
            field = this.getFieldById(id);
        if (field) {
            if (field.isTv) {
                var label = field.up('.x-form-element').prev('label');
                if (label) {
                    val = label.dom.innerText
                }
            } else {
                val = field.fieldLabel;
            }
        }
        return val.trim();
    },
    getFieldById: function (id) {
        var field = Ext.getCmp(id);
        if (!field) {
            if (field = Ext.get('tv' + id)) {
                field.isTv = true;
            }
        }
        return field;
    },
    translate: function (items) {
        if (!items.length) return;
        var language = Ext.getCmp('polylang-polylangcontent-culture_key');
        this.getEl().mask(_('loading'), 'x-mask-loading');
        MODx.Ajax.request({
            url: Polylang.config.connector_url,
            params: {
                action: 'mgr/translator/translate',
                rid: Polylang.config.rid,
                items: Ext.encode(items),
                to: language.getValue(),
                from: Polylang.config.defaultLanguage,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.setTranslate(r.object);
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
    translateAll: function () {
        var items = [],
            hasValue = false,
            list = Ext.query('#' + this.id + ' .polylang-translate');
        Ext.each(list || [], function (el) {
            var data = this.getBtnTranslateData(el),
                val = this.getFieldValue(data.target);
            data.hasValue = false;
            if (val) {
                hasValue = true;
                data.hasValue = true;
            }
            items.push(data);
        }, this);
        if (hasValue) {
            Ext.MessageBox.confirm(
                _('confirm'),
                _('polylang_translator_confirm_ignore_translate'),
                function (val) {
                    if (val == 'yes') {
                        Ext.each(items, function (item, index) {
                            if (item && item.hasValue) {
                                items.splice(index, 1);
                            }
                        }, this);
                    }
                    this.translate(items);
                }, this
            );
        } else {
            this.translate(items);
        }
    }
});
Ext.reg('polylang-window-polylangcontent', Polylang.window.PolylangContent);