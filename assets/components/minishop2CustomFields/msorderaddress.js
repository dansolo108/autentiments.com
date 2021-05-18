Ext.ComponentMgr.onAvailable('minishop2-window-order-update', function(){

    var fio = {
        border: false,
        layout: 'column',
        items: [
            {
                border: false,
                columnWidth: 0.33,
                autoHeight: true,
                layout: 'form',
                items: {
                    xtype: 'textfield',
                    name: 'addr_name',
                    fieldLabel: 'Имя',
                    anchor: '100%'
                }
            },
            {
                border: false,
                columnWidth: 0.33,
                autoHeight: true,
                layout: 'form',
                items: {
                    xtype: 'textfield',
                    name: 'addr_surname',
                    fieldLabel: 'Фамилия',
                    anchor: '100%'
                }
            }
        ],
        autoHeight: true,
    }
    
    var corpus = {
        border: false,
        columnWidth: 0.33,
        autoHeight: true,
        layout: 'form',
        items: {
            xtype: 'textfield',
            name: 'addr_corpus',
            fieldLabel: 'Корпус',
            anchor: '100%'
        }
    }
    
    var entrance = {
        border: false,
        columnWidth: 0.33,
        autoHeight: true,
        layout: 'form',
        items: {
            xtype: 'textfield',
            name: 'addr_entrance',
            fieldLabel: 'Подъезд',
            anchor: '100%'
        }
    }

    this.fields.items[2].items.unshift(fio);
    this.fields.items[2].items[5].items.push(corpus);
    this.fields.items[2].items[5].items.push(entrance);
});