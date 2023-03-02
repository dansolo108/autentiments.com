msoptionsprice.window.option = function (config) {
	config = config || {record: {}};

	if (!config.update) {
		config.update = true;
	}

	Ext.applyIf(config, {
		title: _('create'),
		width: 450,
		autoHeight: true,
		url: msoptionsprice.config.connector_url,
		action: 'mgr/option/update',
		fields: this.getFields(config),
		buttons: this.getButtons(config),
		keys: this.getKeys(config),
		listeners: this.getListeners(config),
		cls: 'msoptionsprice-panel-option',
	});
	msoptionsprice.window.option.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.window.option, MODx.Window, {

	getKeys: function (config) {
		return [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}];
	},

	getFields: function (config) {
		return [{
			layout: 'form',
			defaults: {border: false, anchor: '100%'},
			items: [{
				xtype: 'hidden',
				name: 'mid'
			}, {
				xtype: 'hidden',
				name: 'rid'
			}, {
				layout: 'column',
				border: false,
				items: [{
					columnWidth: 1,
					layout: 'form',
					defaults: {border: false, anchor: '100%'},
					items: [{
						xtype: 'msoptionsprice-combo-option-key',
						fieldLabel: _('msoptionsprice_key'),
						name: 'key',
						rid: config.record.rid || 0,
						allowBlank: false,
						listeners: {
							afterrender: {
								fn: function (r) {
									this.handleChangeType(0);
								},
								scope: this
							},
							select: {
								fn: function (r) {
									this.handleChangeType(1);
								},
								scope: this
							}
						}
					}, {
						xtype: 'msoptionsprice-combo-option-values',
						fieldLabel: _('msoptionsprice_value'),
						name: 'value',
						rid: config.record.rid,
						allowBlank: false
					}]
				}]
			}]
		}];

	},

	getButtons: function (config) {
		return [{
			text: config.cancelBtnText || _('cancel'),
			scope: this,
			handler: function () {
				config.closeAction !== 'close'
					? this.hide()
					: this.close();
			}
		}, {
			text: _('add'),
			cls: 'primary-button',
			scope: this,
			handler: this.submit,
		}];
	},

	getListeners: function (config) {
		return Ext.applyIf(config.listeners, {
			beforeSubmit: {
				fn: function () {
					//this.saveField();
				}, scope: this
			}
		});
	},

	handleChangeType: function (change) {
		var f = this.fp.getForm();
		var _key = f.findField('key');
		var _value = f.findField('value');

		_value.getStore().baseParams.key = _key.getValue();

		if (1 == change) {
			_value.setValue();
			_value.store.load();
		}
		if (!!_value.pageTb) {
			_value.pageTb.show();
		}
	},


	loadDropZones: function () {

	}

});
Ext.reg('msoptionsprice-window-option', msoptionsprice.window.option);
