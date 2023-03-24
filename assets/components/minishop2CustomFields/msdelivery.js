Ext.override(miniShop2.grid.Delivery, {
    getFields: function () {
        return [
            'id', 'name', 'price', 'weight_price', 'distance_price', 'rank', 'payments',
            'logo', 'active', 'class', 'description', 'requires','hidden_fields', 'actions', 'free_delivery_amount',
            'show_on_ru', 'show_on_en', 'free_delivery_rf'
        ];
    },
});


Ext.ComponentMgr.onAvailable('minishop2-window-delivery-update', function(config){
    var add = {
        border: false,
        layout: 'column',
        items: [
            {
                border: false,
                columnWidth: 1,
                autoHeight: true,
                layout: 'form',
                items: [{
                    xtype: 'xcheckbox',
                    boxLabel: 'Бесплатная доставка по РФ',
                    hideLabel: true,
                    name: 'free_delivery_rf',
                    id: config.id + '-free_delivery_rf',
                }, {
                    xtype: 'xcheckbox',
                    boxLabel: 'Показывать в русской версии',
                    hideLabel: true,
                    name: 'show_on_ru',
                    id: config.id + '-show_on_ru'
                },{
                    xtype: 'xcheckbox',
                    boxLabel: 'Показывать в английской версии',
                    hideLabel: true,
                    name: 'show_on_en',
                    id: config.id + '-show_on_en'
                }]
            }
        ],
        autoHeight: true,
    }
    this.fields[0].items[0].items.push(add);
});