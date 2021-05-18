Ext.override(miniShop2.window.CreateOption, {
    polylangOriginals: {
        getForm: miniShop2.window.CreateOption.prototype.getForm
    },
    getForm: function (config) {
        var form = this.polylangOriginals.getForm.call(this, config);
        form.push({
            title: _('polylang_option_inject_fieldset'),
            xtype: 'fieldset',
            cls: 'polylang-fieldset',
            collapsible: true,
            stateId: 'polylang-option-inject-fieldset',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'xcheckbox',
                boxLabel: _('polylang_option_enabled'),
                description: _('polylang_option_enabled_desc'),
                name: 'polylang_enabled',
                id: config.id + '-polylang-enabled',
                inputValue: 1,
                hideLabel: true,
                checked: config.record.polylang_enabled || false
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('polylang_option_translate'),
                description: _('polylang_option_translate_desc'),
                name: 'polylang_translate',
                id: config.id + '-polylang-translate',
                inputValue: 1,
                hideLabel: true,
                checked: config.record.polylang_translate || false
            }]
        });
        return form;
    },
});