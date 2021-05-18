Polylang.combo.ComboBox = function (config) {
    Ext.applyIf(config, {
        hiddenName: config.name || '',
        ctCls: 'polylang-field',
        triggerConfig: {
            tag: 'span',
            cls: 'x-field-combo-btns',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger',
                    trigger: ''
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-clear',
                    trigger: 'clear',
                }
            ]
        }
    });
    Polylang.combo.ComboBox.superclass.constructor.call(this, config);
    this.addEvents('clear');
};
Ext.extend(Polylang.combo.ComboBox, MODx.combo.ComboBox, {
    onTriggerClick: function (event, btn) {
        if (this.disabled) return;
        switch (btn.getAttribute('trigger')) {
            case 'clear':
                this.clearValue();
                this.fireEvent('clear', this);
                break;
            default:
                MODx.combo.ComboBox.superclass.onTriggerClick.call(this);
        }
    }
});
Ext.reg('polylang-combo', Polylang.combo.ComboBox);

Polylang.field.Field = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'polylang-field',
        msgTarget: 'under',
        triggerAction: 'all',
        onTrigger1Click: this._triggerClear
    });
    Polylang.field.Field.superclass.constructor.call(this, config);
    this.addEvents('clear');
};
Ext.extend(Polylang.field.Field, Ext.form.TwinTriggerField, {
    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns one',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-clear'
                }
            ]
        };
    },
    _triggerClear: function () {
        Ext.form.TwinTriggerField.superclass.setValue.call(this, '');
        this.fireEvent('clear', this);
    }
});
Ext.reg('polylang-field', Polylang.field.Field);

Polylang.field.TextEditor = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'textarea',
        cls: 'polylang-richtext polylang-text-editor',
        width: '100%',
        height: Polylang.config.editorHeight,
    });
    Polylang.field.TextEditor.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.field.TextEditor, Ext.form.TextArea);
Ext.reg('polylang-text-editor', Polylang.field.TextEditor);

Polylang.field.CodeEditor = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
        cls: 'polylang-code-editor',
        mimeType: 'text/plain',
        height: Polylang.config.editorHeight,

    });
    Polylang.field.CodeEditor.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.field.CodeEditor, Ext.ComponentMgr.types['modx-texteditor'] ? MODx.ux.Ace : Ext.form.TextArea);
Ext.reg('polylang-code-editor', Polylang.field.CodeEditor);

Polylang.combo.Boolean = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                [_('yes'), 1],
                [_('no'), 0],
            ]
        }),
        displayField: 'd',
        valueField: 'v',
        hiddenName: config.name || '',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        forceSelection: true,
        listeners: {
            afterrender: function (combo) {
                var val = this.getValue();
                if (val == 'false' || val == false) {
                    val = 0;
                } else if (val == 'true' || val == true) {
                    val = 1;
                }
                this.setValue(val);
            }
        }
    });
    Polylang.combo.Boolean.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.Boolean, Polylang.combo.ComboBox);
Ext.reg('polylang-combo-boolean', Polylang.combo.Boolean);

Polylang.combo.AutoComplete = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'superboxselect',
        allowBlank: true,
        msgTarget: 'under',
        allowAddNewData: true,
        addNewDataOnBlur: true,
        allowSorting: true,
        pinList: false,
        resizable: true,
        lazyInit: false,
        name: config.name || 'tags',
        anchor: '100%',
        minChars: 1,
        pageSize: 10,
        store: new Ext.data.JsonStore({
            id: (config.name || 'tags') + '-store',
            root: 'results',
            autoLoad: false,
            autoSave: false,
            totalProperty: 'total',
            fields: ['value'],
            url: Polylang.config.connector_url,
            baseParams: {
                key: config.name,
                culture_key: config.culture_key,
                action: 'mgr/polylangproductoption/getoptions',
            }
        }),
        mode: 'remote',
        displayField: 'value',
        valueField: 'value',
        triggerAction: 'all',
        extraItemCls: 'x-tag',
        expandBtnCls: 'x-form-trigger',
        clearBtnCls: 'x-form-trigger',
        displayFieldTpl: config.displayFieldTpl || '{value}',
        // fix for setValue
        addValue: function (value) {
            if (Ext.isEmpty(value)) {
                return;
            }
            var values = value;
            if (!Ext.isArray(value)) {
                value = '' + value;
                values = value.split(this.valueDelimiter);
            }
            Ext.each(values, function (val) {
                if (Ext.isObject(val) && val.hasOwnProperty('value')) {
                    val = val.value;
                }
                var record = this.findRecord(this.valueField, val);
                if (record) {
                    this.addRecord(record);
                } else {
                    this.remoteLookup.push(val);
                }
            }, this);
            if (this.mode === 'remote') {
                var q = this.remoteLookup.join(this.queryValuesDelimiter);
                this.doQuery(q, false, true);
            }
        },
        // fix similar queries
        shouldQuery: function (q) {
            if (this.lastQuery) {
                return (q !== this.lastQuery);
            }
            return true;
        },
        onRender: function (ct, position) {
            this.constructor.prototype.onRender.apply(this, arguments);
            if (config.allowSorting) {
                this.initSorting();
            }
        },
    });
    config.name += '[]';

    Ext.apply(config, {
        listeners: {
            beforequery: {
                fn: this.beforequery,
                scope: this
            },
            newitem: {
                fn: this.newitem,
                scope: this
            },
        }
    });
    Polylang.combo.AutoComplete.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.AutoComplete, Ext.ux.form.SuperBoxSelect, {
    beforequery: function (o) {
        // reset sort
        o.combo.store.sortInfo = '';
        if (o.forceAll !== false) {
            exclude = o.combo.getValue().split(o.combo.valueDelimiter);
        } else {
            exclude = [];
        }
        o.combo.store.baseParams.exclude = Ext.util.JSON.encode(exclude);
    },
    newitem: function (bs, v) {
        bs.addNewItem({value: v});
    },
    initSorting: function () {
        var _this = this;
        if (typeof Sortable != 'undefined') {
            var item = document.querySelectorAll("#" + this.outerWrapEl.id + " ul")[0];
            if (item) {
                item.setAttribute("data-xcomponentid", this.id);
                new Sortable(item, {
                    onEnd: function (evt) {
                        if (evt.target) {
                            var cmpId = evt.target.getAttribute("data-xcomponentid");
                            var cmp = Ext.getCmp(cmpId);
                            if (cmp) {
                                _this.refreshSorting(cmp);
                                MODx.fireResourceFormChange();
                            } else {
                                console.log("Unable to reference xComponentContext.");
                            }
                        }
                    }
                });
            } else {
                console.log("Unable to find select element");
            }
        } else {
            console.log("Sortable undefined");
        }
    },
    refreshSorting: function (cmp) {
        var viewList = cmp.items.items;
        var dataInputList = document.querySelectorAll("#" + cmp.outerWrapEl.dom.id + " .x-superboxselect-input");
        var getElementIndex = function (item) {
            var nodeList = Array.prototype.slice.call(item.parentElement.children);
            return nodeList.indexOf(item);
        };
        var getElementByIndex = function (index) {
            return nodeList[index];
        };
        var getElementByValue = function (val, list) {
            for (var i = 0; i < list.length; i += 1) {
                if (list[i].value == val) {
                    return list[i];
                }
            }
        };
        var sortElementsByListIndex = function (list, callback) {
            list.sort(compare);
            if (callback instanceof Function) {
                callback();
            }
        };
        var syncElementsByValue = function (list1, list2, callback) {
            var targetListRootElement = list2[0].parentElement;
            if (targetListRootElement) {
                for (var i = 0; i < list1.length; i += 1) {
                    var targetItemIndex;
                    var item = list1[i];
                    var targetItem = getElementByValue(item.value, list2);
                    var initialTargetElement = list2[i];
                    if (targetItem !== null && initialTargetElement !== undefined) {
                        targetListRootElement.insertBefore(targetItem, initialTargetElement);
                    }
                }
            } else {
                console.debug("syncElementsByValue(), Unable to reference list root element.");
                return false;
            }
            if (callback instanceof Function) {
                callback();
            }
        };
        var compare = function (a, b) {
            var aIndex = getElementIndex(a.el.dom);
            var bIndex = getElementIndex(b.el.dom);
            if (aIndex < bIndex) {
                return -1;
            }
            if (aIndex > bIndex) {
                return 1;
            }
            return 0;
        };
        sortElementsByListIndex(viewList);
        syncElementsByValue(viewList, dataInputList[0].children);
        cmp.value = cmp.getValue();
    },
});
Ext.reg('polylang-combo-auto-complete', Polylang.combo.AutoComplete);

Polylang.combo.ClassName = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name',
        hiddenName: config.name || 'class_name',
        valueField: 'key',
        fields: ['name', 'key'],
        pageSize: 20,
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        url: Polylang.config.connector_url,
        baseParams: {
            combo: true,
            action: 'mgr/polylangfield/classname/getlist'
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <b>{name}</b>\
                        <tpl if="key"> ({key})</tpl>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    Polylang.combo.ClassName.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.ClassName, Polylang.combo.ComboBox);
Ext.reg('polylang-combo-class-name', Polylang.combo.ClassName);

Polylang.combo.InputTypes = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name',
        hiddenName: config.name || 'xtype',
        valueField: 'key',
        fields: ['name', 'key'],
        pageSize: 20,
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        url: Polylang.config.connector_url,
        baseParams: {
            combo: true,
            action: 'mgr/polylangfield/inputtypes/getlist'
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <b>{name}</b>\
                        <tpl if="key"> ({key})</tpl>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    Polylang.combo.InputTypes.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.InputTypes, Polylang.combo.ComboBox);
Ext.reg('polylang-combo-input-types', Polylang.combo.InputTypes);

Polylang.combo.Language = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name',
        hiddenName: config.name || 'culture_key',
        valueField: 'culture_key',
        fields: ['name', 'culture_key'],
        width: '100%',
        pageSize: 20,
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        url: Polylang.config.connector_url,
        baseParams: {
            combo: true,
            action: 'mgr/polylanglanguage/getlist'
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <b>{name}</b>\
                        <tpl if="culture_key"> ({culture_key})</tpl>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    Polylang.combo.Language.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.Language, Polylang.combo.ComboBox);
Ext.reg('polylang-combo-language', Polylang.combo.Language);

Polylang.combo.Currency = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'currency_name',
        valueField: 'cid',
        fields: ['cid', 'currency_name', 'currency_code', 'currency_symbol_left', 'currency_symbol_right', 'val'],
        hiddenName: config.name || 'currency_id',
        editable: true,
        minChars: 2,
        url: Polylang.config.currency.connector_url,
        baseParams: {
            action: 'mgr/multicurrencysetmember/getList',
            sid: Polylang.config.currency.baseCurrencySet,
            exclude: -1,
            combo: true
        },
        tpl: new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item"><span style="font-weight: bold">{currency_name}</span>'
            , '<tpl if="currency_code"> - <span style="font-style:italic">{currency_code}</span></tpl></div></tpl>')
    });
    Polylang.combo.Currency.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.Currency, Polylang.combo.ComboBox);
Ext.reg('polylang-combo-currency', Polylang.combo.Currency);


Polylang.combo.Tvs = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name',
        hiddenName: config.name || 'tmplvarid',
        valueField: 'id',
        fields: ['id', 'name', 'caption', 'elements', 'default_text'],
        width: '100%',
        pageSize: 20,
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        url: Polylang.config.connector_url,
        baseParams: {
            combo: true,
            type: config.type || '',
            onlyPolylang: config.onlyPolylang || 0,
            action: 'mgr/element/tv/getlist'
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <b>{name}</b>\
                        <tpl if="caption"> ({caption})</tpl>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    Polylang.combo.Tvs.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.Tvs, Polylang.combo.ComboBox);
Ext.reg('polylang-combo-tvs', Polylang.combo.Tvs);

Polylang.combo.Resource = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'pagetitle',
        hiddenName: config.name || 'resource',
        valueField: 'id',
        fields: ['id', 'pagetitle', 'parents', 'context_key'],
        pageSize: 20,
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        url: Polylang.config.connector_url,
        baseParams: {
            combo: true,
            class_key: config.class_key || 'modDocument',
            action: 'mgr/resource/getlist'
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item"><span style="font-weight: bold">{pagetitle:htmlEncode}</span>',
            ' - <span style="font-style:italic">{context_key:htmlEncode}</span>',
            '<tpl if="parents"><br>{parents:htmlEncode()}</tpl></div></tpl>'
        ),

    });
    Polylang.combo.Resource.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.Resource, Polylang.combo.ComboBox);
Ext.reg('polylang-combo-resource', Polylang.combo.Resource);

Polylang.combo.Clipboard = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        allowBlank: false,
        msgTarget: 'under',
        triggerAction: 'all',
        onTrigger1Click: this._triggerCopy
    });
    Polylang.combo.AccessToken.superclass.constructor.call(this, config);
};
Ext.extend(Polylang.combo.Clipboard, Ext.form.TwinTriggerField, {
    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns one',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-copy',
                    title: _('msimportexport_copy_to_clipboard')
                }
            ]
        };
    },

    _triggerCopy: function () {
        Clipboard.copy(this.getValue());
        MODx.msg.status({
            title: _('success'),
            message: _('msimportexport_copied_to_clipboard'),
            dontHide: false
        });
    }
});
Ext.reg('polylang-field-clipboard', Polylang.combo.Clipboard);

Polylang.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-combo-btn-clear',
        searchBtnCls: 'x-field-combo-btn-search',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear,
    });
    Polylang.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(Polylang.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns',
            cn: [
                {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
                {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
            ]
        };
    },

    _triggerSearch: function () {
        this.fireEvent('search', this);
    },

    _triggerClear: function () {
        this.fireEvent('clear', this);
    },

});
Ext.reg('polylang-combo-search', Polylang.combo.Search);
