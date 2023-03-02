miniShop2.plugin.hexcolor = {
    // Изменение полей для панели товара
    getFields: function () {
        // console.log('getFields');
        return {
            hexcolor: {
                xtype: 'minishop2-combo-hexcolor',
                description: '<b>[[+hexcolor]]</b>'
            }
        }
    },
    // Изменение колонок таблицы товаров в категории
    getColumns: function () {
        return {
            hexcolor: {
                width: 50,
                sortable: false,
                editor: {
                    xtype: 'minishop2-combo-hexcolor',
                    name: 'hexcolor'
                }
            }
        }
    }
};

miniShop2.combo.HexColor = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: config.name || 'hexcolor',
        fieldLabel: msOptionHexColor.config.hexcolor_title,
        hiddenName: config.name || 'hexcolor',
        displayField: 'name',
        valueField: 'id',
        anchor: '100%',
        fields: ['name', 'id'],
        pageSize: 20,
        url: msOptionHexColor.config.connector_url,
        typeAhead: true,
        editable: true,
        allowBlank: true,
        emptyText: _('no'),
        minChars: 1,
        baseParams: {
            action: 'mgr/color/getlist',
            combo: true,
            id: config.value,
        }
    });
    miniShop2.combo.HexColor.superclass.constructor.call(this, config);
    this.on('expand', function () {
        if (!!this.pageTb) {
            this.pageTb.show();
        }
    });
};
Ext.extend(miniShop2.combo.HexColor, MODx.combo.ComboBox);
Ext.reg('minishop2-combo-hexcolor', miniShop2.combo.HexColor);

Ext.ComponentMgr.onAvailable("minishop2-product-tabs", function () {
    var pp = 2;
    for (var i = 0; i < this.items.length; i++) {
        if (this.items[i].title == _('ms2_tab_product_data')) {
            pp = i;
        }
    }
    this.items[pp].items[0].items[0].items.push({
        xtype: 'minishop2-combo-hexcolor',
        description: '<b>[[+hexcolor]]</b>',
    });
});