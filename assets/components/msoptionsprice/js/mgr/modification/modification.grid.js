/*Ext.override(Ext.grid.RowExpander, {

 expandRow : function(row){
 if(typeof row == 'number'){
 row = this.grid.view.getRow(row);
 }
 var record = this.grid.store.getAt(row.rowIndex);
 var body = Ext.DomQuery.selectNode('tr:nth(2) div.x-grid3-row-body', row);
 if(this.beforeExpand(record, body, row.rowIndex)){
 this.state[record.id] = true;
 Ext.fly(row).replaceClass('x-grid3-row-collapsed', 'x-grid3-row-expanded');

 console.log(body);
 console.log(body.innerHTML);


 this.fireEvent('expand', this, record, body, row.rowIndex);
 }
 },

 });*/

msoptionsprice.grid.modification = function (config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        enableCaching: false,
        tpl: new Ext.XTemplate(
            '<tpl for=".">',

            '<table class="msoptionsprice-expander"><tbody>',

            '<tpl if="name">',
            '<tr>',
            '<td>',
            '<strong>{name}</strong>',
            '</td>',
            '</tr>',
            '</tpl>',

            '<tpl if="values">',
            '<tr>',
            '<td>',
            '{values:this.renderValues}',
            '</td>',
            '</tr>',
            '</tpl>',

            '<tpl if="thumbnail">',
            '<tr class="modx-pb-thumb msoptionsprice-grid-thumb">',
            '<td>',
            '<img src="{thumbnail}" ext:qtip="{thumbnail:this.renderThumb}" ext:qtitle="{image_name}" ext:qclass="msoptionsprice-qtip"/>',
            '</td>',
            '</tr>',
            '</tpl>',

            ' </tbody></table>',

            '</tpl>',
            {
                compiled: true,
                renderThumb: function (value, record) {
                    return String.format('<img src={0}>', value);
                },
                renderValues: function (value, record) {
                    var options = record.options || {};
                    var values = [];
                    var tmp = [];
                    var pf = '';

                    for (var i in options) {
                        if (!options.hasOwnProperty(i)) {
                            continue;
                        }

                        var name = i.split('.');
                        name = name[0];

                        switch (true) {
                            case i == pf + name + '.key':
                                var title = options[pf + name + '.caption'] || options[i];
                                title = _('msoptionsprice_' + title) || _('ms2_product_' + title) || title;
                                if (title != options[i]) {
                                    title = String.format('{0} </small>({1})</small>', title, options[i])
                                }
                                tmp.push({name: name, title: title});
                                break;
                            case i == pf + name && !!options[i] && typeof options[i] === 'object':
                                tmp.push({name: name, value: options[i].join(', ')});
                                break;
                            case i == pf + name + '.value' && tmp.length == 1:
                                tmp.push({name: name, value: options[i]});
                                break;
                        }

                        if (tmp.length == 2 && tmp[0]['name'] == tmp[1]['name']) {
                            if (tmp[0]['value'] && !!tmp[0]['value']) {
                                values.push(String.format('<tr><td><b>{0}: </b>{1}</td></tr>', tmp[1]['title'], tmp[0]['value']));
                            } else if (!!tmp[1]['value']) {
                                values.push(String.format('<tr><td><b>{0}: </b>{1}</td></tr>', tmp[0]['title'], tmp[1]['value']));
                            }
                            tmp = [];
                        }

                    }

                    /*var values = [];
                    var tmp = [];
                    var pf = MODx.config['msoptionsprice_field_prefix'] || 'option_';
                    var rx = new RegExp(pf.replace("", ""));

                    for (var i in record) {
                        if (!record.hasOwnProperty(i)) {
                            continue;
                        }

                        if (!rx.test(i)) {
                            continue;
                        }

                        var name = i.split('_');
                        name = name[1];
                        name = name.split('.');
                        name = name[0];

                        switch (true) {
                            case i == pf + name + '.key':
                                var title = record[pf + name + '.caption'] || record[i];
                                title = _('msoptionsprice_' + title) || _('ms2_product_' + title) || title;
                                if (title != record[i]) {
                                    title = String.format('{0} </small>({1})</small>', title, record[i])
                                }
                                tmp.push({name: name, title: title});
                                break;
                            case i == pf + name && !!record[i] && typeof record[i] === 'object':
                                tmp.push({name: name, value: record[i].join(', ')});
                                break;
                            case i == pf + name + '.value' && tmp.length == 1:
                                tmp.push({name: name, value: record[i]});
                                break;
                        }

                        if (tmp.length == 2 && tmp[0]['name'] == tmp[1]['name']) {
                            if (tmp[0]['value'] && !!tmp[0]['value']) {
                                values.push(String.format('<tr><td><b>{0}: </b>{1}</td></tr>', tmp[1]['title'], tmp[0]['value']));
                            }
                            else if (!!tmp[1]['value']) {
                                values.push(String.format('<tr><td><b>{0}: </b>{1}</td></tr>', tmp[0]['title'], tmp[1]['value']));
                            }
                            tmp = [];
                        }
                    }*/

                    return values.join('');
                }
            }
        ),
        /* renderer: function(v, p, record) {
             return record.data.options != '' && record.data.options != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';
         }*/

        renderer: function (v, p, record, rowIndex) {
            window.setTimeout(function () {
                this.fireEvent('render', this, record, rowIndex);
            }.bind(this), 150);

            p.cellAttr = 'rowspan="2"';
            return '<div class="x-grid3-row-expander">&#160;</div>';
        },

    });

    this.exp.on('render', function (rowexpander, record, rowIndex) {
        if (MODx.config['msoptionsprice_grid_modification_expand']) {
            var row;
            if (typeof rowIndex == 'number') {
                row = this.grid.view.getRow(rowIndex);
            }
            if (row) {
                this['expandRow'](row);
            }
        }

        return true;
    });

    this.exp.on('beforeexpand', function (rowexpander, record, body, rowIndex) {
        record['data']['json'] = record['json'];
        record['data'] = Ext.applyIf(record['data'], record['json']);

        return true;
    });

    this.dd = function (grid) {
        this.dropTarget = new Ext.dd.DropTarget(grid.container, {
            ddGroup: 'dd',
            copy: false,
            notifyDrop: function (dd, e, data) {
                var store = grid.store.data.items;
                var target = store[dd.getDragData(e).rowIndex].id;
                var source = store[data.rowIndex].id;
                if (target != source) {
                    dd.el.mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: msoptionsprice.config.connector_url,
                        params: {
                            action: config.action || 'mgr/modification/sort',
                            source: source,
                            target: target
                        },
                        listeners: {
                            success: {
                                fn: function (r) {
                                    dd.el.unmask();
                                    grid.refresh();
                                },
                                scope: grid
                            },
                            failure: {
                                fn: function (r) {
                                    dd.el.unmask();
                                },
                                scope: grid
                            }
                        }
                    });
                }
            }
        });
    };

    this.sm = new Ext.grid.CheckboxSelectionModel();

    Ext.applyIf(config, {
        id: 'msoptionsprice-grid-modification',
        url: msoptionsprice.config.connector_url,
        baseParams: {
            action: 'mgr/modification/getlist',
            rid: config.resource.id || 0,
            sort: 'rank',
            dir: 'asc'
        },
        save_action: 'mgr/modification/updatefromgrid',
        autosave: true,
        save_callback: this._updateRow,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),

        sm: this.sm,
        plugins: [this.exp],

        ddGroup: 'dd',
        enableDragDrop: true,

        paging: true,
        pageSize: 10,
        remoteSort: true,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0
        },
        autoHeight: true,
        cls: 'msoptionsprice-grid',
        bodyCssClass: 'grid-with-buttons',
        stateful: false,
    });
    msoptionsprice.grid.modification.superclass.constructor.call(this, config);
    this.exp.addEvents('beforeexpand', 'beforecollapse');

};
Ext.extend(msoptionsprice.grid.modification, MODx.grid.Grid, {
    windows: {},

    getFields: function (config) {
        var fields = msoptionsprice.config.grid_modification_fields;

        return fields;
    },

    getTopBar: function (config) {
        var tbar = [];

        var component = ['menu', 'update', 'left', 'option', 'spacer'];

        var add = {
            menu: {
                text: '<i class="icon icon-cogs"></i> ',
                menu: [{
                    text: '<i class="icon icon-plus"></i> ' + _('msoptionsprice_action_create'),
                    cls: 'msoptionsprice-cogs',
                    handler: this.create,
                    scope: this
                }, {
                    text: '<i class="icon icon-trash-o red"></i> ' + _('msoptionsprice_action_remove'),
                    cls: 'msoptionsprice-cogs',
                    handler: this.remove,
                    scope: this
                }, '-', {
                    text: '<i class="icon icon-toggle-on green"></i> ' + _('msoptionsprice_action_turnon'),
                    cls: 'msoptionsprice-cogs',
                    handler: this.active,
                    scope: this
                }, {
                    text: '<i class="icon icon-toggle-off red"></i> ' + _('msoptionsprice_action_turnoff'),
                    cls: 'msoptionsprice-cogs',
                    handler: this.inactive,
                    scope: this
                }]
            },
            update: {
                text: '<i class="icon icon-refresh"></i>',
                handler: this._updateRow,
                scope: this
            },
            left: '->',
            option: {
                xtype: 'msoptionsprice-combo-option-key',
                name: 'key',
                width: 200,
                custm: true,
                clear: true,
                addall: true,
                rid: config.resource.id || 0,
                value: '',
                listeners: {
                    select: {
                        fn: this._filterByCombo,
                        scope: this
                    },
                    afterrender: {
                        fn: this._filterByCombo,
                        scope: this
                    }
                }
            },
            spacer: {
                xtype: 'spacer',
                style: 'width:1px;'
            },

            bigspacer: {
                xtype: 'spacer',
                style: 'width:5px;'
            }

        };

        component.filter(function (item) {
            if (add[item]) {
                tbar.push(add[item]);
            }
        });

        var items = [];
        if (tbar.length > 0) {
            items.push(new Ext.Toolbar(tbar));
        }

        return new Ext.Panel({items: items});
    },

    getColumns: function (config) {
        var pf = MODx.config['msoptionsprice_field_prefix'] || 'option_';
        var columns = [this.exp, this.sm];

        var add = {
            id: {
                width: 5,
                hidden: true,
            },
            name: {
                width: 10,
                sortable: true,
                editor: {
                    xtype: 'textfield',
                    allowBlank: true
                }
            },
            type: {
                width: 5,
                sortable: true,
                editor: {
                    xtype: 'msoptionsprice-combo-modification-type',
                    fieldLabel: _('msoptionsprice_type'),
                    name: 'type',
                    allowBlank: false
                },
                renderer: function (value, metaData, record) {
                    return MODx.lang['msoptionsprice_modification_name_type_' + value] || value;
                }
            },
            price: {
                width: 10,
                sortable: true,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
            old_price: {
                width: 10,
                sortable: true,
                editor: {
                    xtype: 'textfield',
                    allowBlank: true
                }
            },
            article: {
                width: 10,
                sortable: true,
                editor: {
                    xtype: 'textfield',
                    allowBlank: true
                }
            },
            weight: {
                width: 10,
                sortable: true,
                editor: {
                    xtype: 'numberfield',
                    decimalPrecision: 3,
                    allowBlank: true
                }
            },
            count: {
                width: 10,
                sortable: true,
                editor: {
                    xtype: 'numberfield',
                    decimalPrecision: 0,
                    allowBlank: true
                }
            },
            image: {
                width: 15,
                sortable: true,
                editor: {
                    xtype: 'msoptionsprice-combo-product-image',
                    fieldLabel: _('msoptionsprice_image'),
                    name: 'image',
                    rid: config.resource.id || 0,
                    custm: true,
                    clear: true,
                    allowBlank: true
                },
                renderer: function (value, metaData, record) {
                    return msoptionsprice.tools.renderReplace(record['json']['image'], record['json']['image_name'])
                }
            },

            extra: {
                width: 15,
                sortable: false,
                renderer: function (value, metaData, record) {
                    return value;
                }
            },
            actions: {
                width: 10,
                sortable: false,
                id: 'actions',
                renderer: msoptionsprice.tools.renderActions,

            }
        };


        var fields = this.getFields();
        fields.filter(function (field) {
            if (add[field]) {
                Ext.applyIf(add[field], {
                    header: _('msoptionsprice_header_' + field),
                    tooltip: _('msoptionsprice_tooltip_' + field),
                    dataIndex: field
                });
                columns.push(add[field]);
            } else if (field.indexOf(pf) === 0) {
                var key = field.substr(pf.length);

                add[field] = {
                    header: _('msoptionsprice_header_' + key) || _('msoptionsprice_' + key) || _('ms2_product_' + key),
                    tooltip: _('msoptionsprice_tooltip_' + key) || _('msoptionsprice_' + key) || _('ms2_product_' + key),
                    dataIndex: field,
                    width: 10,
                    sortable: true
                };

                columns.push(add[field]);
            }
        });

        return columns;
    },

    getListeners: function (config) {
        return Ext.applyIf(config.listeners || {}, {
            render: {
                fn: this.dd,
                scope: this
            }
        });
    },

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = msoptionsprice.tools.getMenu(row.data['actions'], this, ids);
        this.addContextMenuItem(menu);
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof (row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    setAction: function (method, field, value) {
        var ids = this._getSelectedIds();
        if (!ids.length && (field !== 'false')) {
            return false;
        }
        MODx.Ajax.request({
            url: msoptionsprice.config.connector_url,
            params: {
                action: 'mgr/modification/multiple',
                method: method,
                field_name: field,
                field_value: value,
                ids: Ext.util.JSON.encode(ids)
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    },
                    scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    },
                    scope: this
                }
            }
        })
    },

    remove: function () {
        Ext.MessageBox.confirm(
            _('msoptionsprice_action_remove'),
            _('msoptionsprice_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.setAction('remove');
                }
            },
            this
        );
    },


    active: function (btn, e) {
        this.setAction('setproperty', 'active', 1);
    },

    inactive: function (btn, e) {
        this.setAction('setproperty', 'active', 0);
    },

    create: function (btn, e) {
        var record = {
            id: 0,
            rid: this.config.resource.id,
            type: MODx.config['msoptionsprice_modification_type_default'] || 1,
            price: 0,
            active: 1,
            weight: 0,
        };

        var w = MODx.load({
            xtype: 'msoptionsprice-window-modification',
            action: 'mgr/modification/create',
            record: record,
            update: false,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues(record);
        w.show(e.target);
    },

    update: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        } else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/modification/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var record = r.object;
                        var w = MODx.load({
                            xtype: 'msoptionsprice-window-modification',
                            title: _('msoptionsprice_action_update'),
                            action: 'mgr/modification/update',
                            record: record,
                            update: true,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(record);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },

    duplicate: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        } else if (!this.menu.record) {
            return false;
        }

        Ext.MessageBox.confirm(
            _('msoptionsprice_action_duplicate'),
            _('msoptionsprice_confirm_duplicate'),
            function (val) {
                if (val == 'yes') {
                    this.setAction('duplicate');
                }
            },
            this
        );
    },

    _filterByCombo: function (cb) {
        this.getStore().baseParams[cb.name] = cb.value;
        this.getBottomToolbar().changePage(1);
    },

    _doSearch: function (tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    _updateRow: function () {
        this.refresh();
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    }

});
Ext.reg('msoptionsprice-grid-modification', msoptionsprice.grid.modification);