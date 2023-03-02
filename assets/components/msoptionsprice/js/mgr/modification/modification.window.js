msoptionsprice.window.modification = function (config) {
	config = config || {record: {}};

	Ext.applyIf(config, {
		update: false,
		title: _('create'),
		width: 650,
		autoHeight: true,
		url: msoptionsprice.config.connector_url,
		action: 'mgr/modification/update',
		fields: this.getFields(config),
		keys: this.getKeys(config),
		listeners: this.getListeners(config),
		cls: 'msoptionsprice-panel-modification',
		bodyStyle:'padding:0px;',
	});

	msoptionsprice.window.modification.superclass.constructor.call(this, config);

    this.on('afterrender', function () {

        Ext.each(this.fp.getForm().items.items, function (t) {
            if (!t.name || t.name === "description") {
                return true;
            }
            if (
                msoptionsprice.config.window_modification_fields.indexOf(t.name) >= 0 ||
                msoptionsprice.config.window_modification_fields.indexOf(t.name.replace(/(_)/, "")) >= 0
            ) {
                return true;
            }
            else {
                t.disable().hide();
            }
        });

    });

};
Ext.extend(msoptionsprice.window.modification, MODx.Window, {

	getKeys: function (config) {
		return [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}];
	},

	getModification: function (config) {
		return [{
			layout: 'column',
			border: false,
			items: [{
				columnWidth: 1,
				layout: 'form',
				defaults: {border: false, anchor: '100%'},
				items: [{
					xtype: 'textfield',
					fieldLabel: _('msoptionsprice_name'),
					name: 'name',
					allowBlank: true
				}]
			}]
		},{
			layout: 'column',
			border: false,
			items: [{
				columnWidth: .33,
				layout: 'form',
				defaults: {border: false, anchor: '100%'},
				items: [{
					layout: 'column',
					border: false,
					items: [{
						columnWidth: .3,
						layout: 'form',
						defaults: {border: false, anchor: '100%'},
						items: [{
							xtype: 'msoptionsprice-combo-modification-type',
							fieldLabel: _('msoptionsprice_type'),
							name: 'type',
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
						}]
					}, {
						columnWidth: .7,
						layout: 'form',
						defaults: {border: false, anchor: '100%'},
						items: [{
							xtype: 'textfield',
							fieldLabel: _('msoptionsprice_price'),
							name: 'price',
							maskRe: /[0123456789\.\-]/,
							allowBlank: false
						}]
					}]
				}, {
					xtype: 'msoptionsprice-combo-product-image',
					fieldLabel: _('msoptionsprice_image'),
					name: 'image',
					rid: config.record.rid,
					custm: true,
					clear: true,
					allowBlank: true
				}]
			}, {
				columnWidth: .33,
				layout: 'form',
				defaults: {border: false, anchor: '100%'},
				items: [{
					xtype: 'textfield',
					fieldLabel: _('msoptionsprice_old_price'),
					name: 'old_price',
					maskRe: /[0123456789\.\-]/,
					allowBlank: true
				}, {
					xtype: 'textfield',
					fieldLabel: _('msoptionsprice_article'),
					name: 'article',
					allowBlank: true
				}]
			}, {
				columnWidth: .34,
				layout: 'form',
				defaults: {border: false, anchor: '100%'},
				items: [{
					xtype: 'numberfield',
					decimalPrecision: 3,
					fieldLabel: _('msoptionsprice_weight'),
					name: 'weight',
					allowBlank: true
				}, {
					xtype: 'numberfield',
					decimalPrecision: 0,
					fieldLabel: _('msoptionsprice_count'),
					name: 'count',
					allowBlank: true
				}]
			}]
		}, {
			xtype: 'msoptionsprice-grid-option',
			record: config.record
		}, {
			xtype: 'checkboxgroup',
			fieldLabel: '',
			hideLabel: true,
			columns: 2,
			items: [{
				xtype: 'xcheckbox',
				boxLabel: _('msoptionsprice_active'),
				name: 'active',
				checked: config.record.active
			}]
		}];
	},

    getGallery: function (config) {
        return [{
            xtype: 'msoptionsprice-panel-gallery',
            resource: msoptionsprice.config.resource,
            record: config.record,
            pageSize: 9,
            border: false,
        }];
    },

    getDescription: function (config) {
        return [{
            xtype: 'textarea',
            name: 'description',
            fieldLabel: _('msoptionsprice_description'),
            cls: 'modx-richtext',
            anchor: '100%',
            height: '250px',
            allowBlank: true,
			listeners: {
                afterrender: function () {
                    if (typeof TinyMCERTE != 'undefined') {
                        TinyMCERTE.loadForTVs();
                    }
                }
			}
        }];
    },

    getTabs: function (config) {
		var tabs = [];
		var add = {};

		if (config.update) {
			add = {
				modification: {
					layout: 'form',
					items: this.getModification(config)
				},
				gallery: {
					layout: 'form',
					items: this.getGallery(config)
				},
                description: {
                    layout: 'form',
                    items: this.getDescription(config)
                }
			};
		}
		else {
			add = {
				modification: {
					layout: 'form',
					items: this.getModification(config)
				},
                description: {
                    layout: 'form',
                    items: this.getDescription(config)
                }
			};
		}
		msoptionsprice.config.window_modification_tabs.filter(function(tab) {
			if (add[tab]) {
				Ext.applyIf(add[tab], {
					title: _('msoptionsprice_tab_' + tab)
				});
				tabs.push(add[tab]);
			}
		});

		return tabs;
	},

	getFields: function (config) {
		return [{
			xtype: 'hidden',
			name: 'id'
		}, {
			xtype: 'hidden',
			name: 'rid'
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			activeTab: 0,
			autoHeight: true,
			items: this.getTabs(config)
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
		var _type = f.findField('type');
		var _price = f.findField('price');

		switch (_type.getValue()) {
			case 1:
				_price.maskRe = /[0123456789\.\-]/;
				break;
			case 2:
			case 3:
				_price.maskRe = /[0123456789\.\-%]/;
				break;
		}
	},

	loadDropZones: function () {

	}

});
Ext.reg('msoptionsprice-window-modification', msoptionsprice.window.modification);
