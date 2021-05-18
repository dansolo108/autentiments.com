Ext.ComponentMgr.onAvailable('minishop2-window-order-update', function () {
    msPromoCode.order_id = this.record.id || 0;

    var add = [
        {
            id: 'mspromocode-code',
            columnWidth: 1,
            layout: 'form',
            cls: 'panel-desc',
            style: 'display: none; margin: 0;',
            items: [{
                html: '\
						<div><b>' + _('mspromocode_promocode') + '</b>: <span id="mspcCode-' + msPromoCode.order_id + '">' + '</span></div>\
						<div><b>' + _('mspromocode_discount_amount') + '</b>: <span id="mspcDiscount-' + msPromoCode.order_id + '">' + '</span></div>\
					',
                anchor: '100%',
                style: 'font-size: 1.1em;'
            }],
        },
        this.fields.items[0].items[0] // вставляем ID заказа, на чьё место мы ставим наш блок...
    ];

    this.fields.items[0].items[0] = add;

    MODx.Ajax.request({
        url: msPromoCode.config.connector_url,
        params: {
            action: 'mgr/order/get',
            // where: JSON.stringify({
            //     order_id: msPromoCode.order_id,
            // }),
            order_id: msPromoCode.order_id,
        },
        listeners: {
            success: {
                fn: function (r) {
                    var codeBlock = Ext.get('mspromocode-code');

                    if (typeof r.object.code != 'undefined' && r.object.code != '') {
                        codeBlock.show();
                        codeBlock.select('#mspcCode-' + msPromoCode.order_id).elements[0].innerHTML = r.object.code;
                        codeBlock.select('#mspcDiscount-' + msPromoCode.order_id).elements[0].innerHTML = r.object.discount_amount;
                    }
                },
                scope: this
            },
        }
    });
});