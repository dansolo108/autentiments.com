sms.window.SendView = function (config) {
    config = config || {};
    if (config.record) {
        if (config.record.user_id) {
            config.record.user_id = config.record.user_fullname ? config.record.user_fullname : config.record.user_email;
        }
        config.record.status = _('sms_send_field_status_' + config.record.status);
        config.record.send = config.record.send ? _('yes') : _('no');
    }
    Ext.applyIf(config, {
        title: _('sms_window_title_view'),
        width: 600,
        modal: true,
        autoHeight: true,
        fields: this.getFields(config),
        buttons: [{
            text: config.cancelBtnText || _('sms_btn_close')
            , scope: this
            , handler: function () {
                config.closeAction !== 'close' ? this.hide() : this.close();
            }
        }],
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.close();
            }, scope: this
        }],
    });
    sms.window.SendView.superclass.constructor.call(this, config);
};
Ext.extend(sms.window.SendView, MODx.Window, {
    getFields: function (config) {
        var fields = ['id', 'sms_id', 'user_id', 'phone', 'send', 'count', 'service', 'ip', 'createdon'];
        var col = 0;
        var data = [[], []];
        for (var i = 0; i < fields.length; i++) {
            var temp = {
                xtype: 'textfield',
                fieldLabel: _('sms_send_field_' + fields[i]),
                boxLabel: _('yes'),
                name: fields[i],
                id: config.id + '-' + fields[i],
                anchor: '99%',
                allowBlank: true,
                readOnly: true,
            };
            switch (fields[i]) {
                case 'id':
                    temp.xtype = 'hidden';
                    break;
            }
            data[col].push(temp);
            if (temp.xtype != 'hidden') {
                col = col == 1 ? 0 : 1;
            }
        }
        return [{
            layout: 'form',
            items: [{
                xtype: 'textarea',
                fieldLabel: _('sms_send_field_message'),
                name: 'message',
                readOnly: true,
                height: '50px',
                anchor: '99%',
            }]
        }, {
            layout: 'column',
            items: [{
                columnWidth: 0.5,
                layout: 'form',
                items: data[0],
            }, {
                columnWidth: 0.5,
                layout: 'form',
                items: data[1],
            }]
        }, {
            layout: 'form',
            items: [{
                xtype: 'textarea',
                fieldLabel: _('sms_send_field_client'),
                name: 'client',
                readOnly: true,
                anchor: '99%',
                height: '50px',
            }, {
                xtype: 'textarea',
                fieldLabel: _('sms_send_field_info'),
                name: 'info',
                readOnly: true,
                height: '50px',
                anchor: '99%',
            }, {
                xtype: 'textarea',
                fieldLabel: _('sms_send_field_properties'),
                name: 'properties',
                readOnly: true,
                anchor: '99%',
                height: '50px',
            }]
        }, {
            html: '&nbsp;',
        }];
    },
});
Ext.reg('sms-send-window-view', sms.window.SendView);