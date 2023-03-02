/*
 Ext.ComponentMgr.onAvailable('minishop2-product-tabs', function () {
 var productTabs = this;
 productTabs.on('beforerender', function () {

 var initialize = productTabs.findById('msoptionsprice-grid-modification');
 if (initialize) {
 return;
 }

 productTabs.add({
 title: _('msoptionsprice_modifications'),
 hideMode: 'offsets',
 items: [{xtype: 'msoptionsprice-panel-resource-inject'}]
 });

 });
 });*/


Ext.override(miniShop2.panel.Product, {

	msoptionspriceOriginals: {
		getFields: miniShop2.panel.Product.prototype.getFields,
	},

	getFields: function (config) {
		var fields = this.msoptionspriceOriginals.getFields.call(this, config);

		for (var i in fields) {
			if (!fields.hasOwnProperty(i)) {
				continue;
			}
			var item = fields[i];
			if (item.id == "modx-resource-tabs") {
				for (var i2 in item.items) {
					if (!item.items.hasOwnProperty(i2)) {
						continue;
					}
					var tab = item.items[i2];
					if (tab.id == "minishop2-product-tab" && tab.items[0]) {
						tab.items[0].items.push({
							title: _('msoptionsprice_modifications'),
							hideMode: 'offsets',
							items: [{xtype: 'msoptionsprice-panel-resource-inject'}]
						});
					}
				}
			}
		}

		return fields;
	},

	setOptionValues: function (optionValues) {

		var extraFields = miniShop2.config.extra_fields;
		var optionFields = miniShop2.config.option_fields;
		var allOptions = [];

		for (i in extraFields) {
			if (!extraFields.hasOwnProperty(i)) {
				continue;
			}
			var key = extraFields[i];
			allOptions.push({'key': key, 'id': 'modx-resource-' + key});
		}

		for (i in optionFields) {
			if (!optionFields.hasOwnProperty(i)) {
				continue;
			}
			item = optionFields[i];
			allOptions.push({'key': item.key, 'id': 'modx-resource-' + item.key});
		}

		var optionFormValues = {};

		for (i in allOptions) {
			if (!allOptions.hasOwnProperty(i)) {
				continue;
			}

			item = allOptions[i];
			if (!optionValues.hasOwnProperty(item.key)) {
				continue;
			}

			field = this.getForm().findField(item.id);
			if (!field) {
				continue;
			}

			optionFormValues[field.name] = optionValues[item.key];
		}

		this.getForm().setValues(optionFormValues);
	},

	setOptionsValues: function () {

		var extraFields = miniShop2.config.extra_fields;
		var optionFields = miniShop2.config.option_fields;
		var allOptions = [];

		for (i in extraFields) {
			if (!extraFields.hasOwnProperty(i)) {
				continue;
			}
			key = extraFields[i];
			allOptions.push({'key': key, 'id': 'modx-resource-' + key});
		}

		for (i in optionFields) {
			if (!optionFields.hasOwnProperty(i)) {
				continue;
			}
			item = optionFields[i];
			allOptions.push({'key': item.key, 'id': 'modx-resource-' + item.key});
		}

		MODx.Ajax.request({
			url: msoptionsprice.config.connector_url,
			params: {
				action: 'mgr/misc/option/getvalues',
				rid: this.config.record.id || 0,
				limit: 0,

			},
			listeners: {
				success: {
					fn: function (response) {

						var optionValues = response.results || {};
						var optionFormValues = {};

						for (i in allOptions) {
							if (!allOptions.hasOwnProperty(i)) {
								continue;
							}

							item = allOptions[i];
							if (!optionValues.hasOwnProperty(item.key)) {
								continue;
							}

							field = this.getForm().findField(item.id);
							if (!field) {
								continue;
							}

							optionFormValues[field.name] = optionValues[item.key];

							/*if ((field.name.indexOf('[]') + 1)) {
							 optionFormValues[field.name] = optionValues[item.key];
							 }
							 else {
							 optionFormValues[field.name] = optionValues[item.key];
							 }*/
						}
						this.getForm().setValues(optionFormValues);
					},
					scope: this
				}
			}
		});

	},

});
