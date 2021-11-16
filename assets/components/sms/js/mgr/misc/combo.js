sms.combo.User = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'modx-combo',
        name: config.name,
        hiddenName: config.name,
        allowBlank: true,
        msgTarget: 'under',
        resizable: false,
        anchor: '100%',
        minChars: 1,
        editable: true,
        valueField: 'id',
        displayField: 'fullname',
        mode: 'remote',
        listWidth: 320,
        pageSize: 20,
        autoLoad: false,
        autoSave: false,
        fields: ['id', 'fullname'],
        url: sms.config.connector_url,
        baseParams: {
            action: 'mgr/load/users',
            filter: config.filter,
        },
    });
    sms.combo.User.superclass.constructor.call(this, config);
};
Ext.extend(sms.combo.User, MODx.combo.ComboBox);
Ext.reg('sms-combo-user', sms.combo.User);

sms.combo.Service = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'modx-combo',
        name: config.name,
        hiddenName: config.name,
        allowBlank: true,
        msgTarget: 'under',
        anchor: '100%',
        minChars: 1,
        editable: true,
        valueField: 'service',
        displayField: 'service',
        mode: 'remote',
        listWidth: config.width || 320,
        pageSize: 20,
        autoLoad: false,
        autoSave: false,
        fields: ['service'],
        url: sms.config.connector_url,
        baseParams: {
            action: 'mgr/load/services',
            filter: config.filter,
        },
    });
    sms.combo.Service.superclass.constructor.call(this, config);
};
Ext.extend(sms.combo.Service, MODx.combo.ComboBox);
Ext.reg('sms-combo-service', sms.combo.Service);