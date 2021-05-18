Ext.onReady(function () {
    Ext.ComponentMgr.onAvailable('modx-panel-tv', function (config) {
        Ext.ComponentMgr.onAvailable('modx-tv-form', function () {
            this.items[1].items[1].items.push({
                title: _('polylang_tv_inject_fieldset'),
                xtype: 'fieldset',
                cls: 'polylang-fieldset',
                collapsible: true,
                stateId: 'polylang-tv-inject-fieldset',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'xcheckbox',
                    boxLabel: _('polylang_tv_enabled'),
                    description: _('polylang_tv_enabled_desc'),
                    name: 'polylang_enabled',
                    id: 'modx-tv-polylang-enabled',
                    inputValue: 1,
                    hideLabel: true,
                    checked: config.record.polylang_enabled || false
                },{
                    xtype: 'xcheckbox',
                    boxLabel: _('polylang_tv_translate'),
                    description: _('polylang_tv_translate_desc'),
                    name: 'polylang_translate',
                    id: 'modx-tv-polylang-translate',
                    inputValue: 1,
                    hideLabel: true,
                    checked: config.record.polylang_translate || false
                }]
            });
        });
    });
});