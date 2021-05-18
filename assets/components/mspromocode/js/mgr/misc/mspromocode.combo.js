Ext.namespace('msPromoCode.combo');


msPromoCode.combo.ConditionType = function(config)
{
    config = config || {};
    config.name = config.name || 'condition_type';
    config.hiddenName = config.hiddenName || config.name;

    Ext.applyIf(config,
    {
        id: 'mspromocode-combo-condition-type',
        name: config.name,
        hiddenName: config.hiddenName,
        fieldLabel: _('mspromocode_field_conditions'),
        emptyText: _('mspromocode_field_conditions_select'),
        listEmptyText: '<div style="padding:10px">'+ _('mspromocode_field_conditions_select_empty') +'</div>',
        anchor: '100%',
        store: new Ext.data.JsonStore(
        {
            id: 0,
            root: 'results',
            totalProperty: 'total',
            autoLoad: true,
            fields: ['value', 'display'],
            url: msPromoCode.config.connector_url,
            baseParams: {
                action: 'mgr/misc/condition/gettypes',
                filter: config.filter || 0,
                combo: 1,
                exclude: config.exclude || '[]',
            },
            // listeners: {
            //     'load': { fn: function(obj, recs, opts) { console.log(recs) }, scope:this },
            // },
        }),
        valueField: 'value',
        displayField: 'display',
    });
    msPromoCode.combo.ConditionType.superclass.constructor.call(this, config);
};
Ext.extend(msPromoCode.combo.ConditionType, MODx.combo.ComboBox);
Ext.reg('mspromocode-combo-condition-type', msPromoCode.combo.ConditionType);


msPromoCode.combo.Product = function(config)
{
    config = config || {};
    config.name = config.name || 'product';
    config.hiddenName = config.hiddenName || config.name;

    Ext.applyIf(config,
    {
        id: 'mspromocode-combo-product',
        fieldLabel: _('mspromocode_field_products'),
        fields: ['id','pagetitle','parents','published'],
        valueField: 'id',
        displayField: 'pagetitle',
        name: config.name,
        hiddenName: config.hiddenName,
        allowBlank: true,
        url: msPromoCode.config.connector_url,
        baseParams: {
            action: 'mgr/misc/product/getlist',
            combo: 1,
            exclude_ids: config.exclude_ids || 0,
        },
        tpl: new Ext.XTemplate(''
            +'<tpl for="."><div class="x-combo-list-item minishop2-product-list-item">'
                +'<tpl if="parents">'
                    +'<span class="parents">'
                        +'<tpl for="parents">'
                            +'<nobr><small>{pagetitle} / </small></nobr>'
                        +'</tpl>'
                    +'</span>'
                +'</tpl>'
                +'<span class="{[values.published % 2 === 0 ? "mspc-combo-list-unpublished" : ""]}"><small>({id})</small> <b>{pagetitle}</b></span>'
            +'</div></tpl>',
            {
                compiled: true
            }
        ),
        itemSelector: 'div.minishop2-product-list-item',
        pageSize: 20,

        anchor: '100%',
        minChars: 2,

        emptyText: _('mspromocode_field_products_select'),
        listEmptyText: '<div style="padding:10px">'+ _('mspromocode_field_products_select_empty') +'</div>',
        //typeAhead: true,
        editable: true,
    });
    msPromoCode.combo.Product.superclass.constructor.call(this,config);
};
Ext.extend(msPromoCode.combo.Product, MODx.combo.ComboBox);
Ext.reg('mspromocode-combo-product', msPromoCode.combo.Product);


msPromoCode.combo.Category = function(config)
{
    config = config || {};
    config.name = config.name || 'category';
    config.hiddenName = config.hiddenName || config.name;

    Ext.applyIf(config,
    {
        id: 'mspromocode-combo-category',
        fieldLabel: _('mspromocode_field_categories'),
        fields: ['id','pagetitle','parents','published'],
        valueField: 'id',
        displayField: 'pagetitle',
        name: config.name,
        hiddenName: config.hiddenName,
        allowBlank: true,
        url: msPromoCode.config.connector_url,
        baseParams: {
            action: 'mgr/misc/category/getlist',
            combo: 1,
            exclude_ids: config.exclude_ids || 0,
        },
        tpl: new Ext.XTemplate(''
            +'<tpl for="."><div class="x-combo-list-item minishop2-category-list-item">'
                +'<tpl if="parents">'
                    +'<span class="parents">'
                        +'<tpl for="parents">'
                            +'<nobr><small>{pagetitle} / </small></nobr>'
                        +'</tpl>'
                    +'</span>'
                +'</tpl>'
                +'<span class="{[values.published % 2 === 0 ? "mspc-combo-list-unpublished" : ""]}"><small>({id})</small> <b>{pagetitle}</b></span>'
            +'</div></tpl>',
            {
                compiled: true
            }
        ),
        itemSelector: 'div.minishop2-category-list-item',
        pageSize: 20,

        anchor: '100%',
        minChars: 2,

        emptyText: _('mspromocode_field_categories_select'),
        listEmptyText: '<div style="padding:10px">'+ _('mspromocode_field_categories_select_empty') +'</div>',
        //typeAhead: true,
        editable: true,
    });
    msPromoCode.combo.Category.superclass.constructor.call(this,config);
};
Ext.extend(msPromoCode.combo.Category, MODx.combo.ComboBox);
Ext.reg('mspromocode-combo-category', msPromoCode.combo.Category);