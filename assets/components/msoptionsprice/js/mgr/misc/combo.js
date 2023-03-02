//
// /* TODO REMOVE FIX */
// miniShop2.combo.Options = function (config) {
// 	config = config || {};
// 	Ext.applyIf(config, {
// 		xtype: 'superboxselect',
// 		allowBlank: true,
// 		msgTarget: 'under',
// 		allowAddNewData: true,
// 		addNewDataOnBlur: true,
// 		pinList: false,
// 		resizable: true,
// 		lazyInit: false,
// 		name: config.name || 'tags',
// 		anchor: '100%',
// 		minChars: 1,
// 		pageSize: 10,
// 		store: new Ext.data.JsonStore({
// 			id: (config.name || 'tags') + '-store',
// 			root: 'results',
// 			autoLoad: false,
// 			autoSave: false,
// 			totalProperty: 'total',
// 			fields: ['value'],
// 			url: miniShop2.config['connector_url'],
// 			baseParams: {
// 				action: 'mgr/product/getoptions',
// 				key: config.name
// 			}
// 		}),
// 		mode: 'remote',
// 		displayField: 'value',
// 		valueField: 'value',
// 		triggerAction: 'all',
// 		extraItemCls: 'x-tag',
// 		expandBtnCls: 'x-form-trigger',
// 		clearBtnCls: 'x-form-trigger',
// 		displayFieldTpl: config.displayFieldTpl || '{value}',
// 		// fix for setValue
// 		addValue : function(value){
// 			if(Ext.isEmpty(value)){
// 				return;
// 			}
// 			var values = value;
// 			if(!Ext.isArray(value)){
// 				value = '' + value;
// 				values = value.split(this.valueDelimiter);
// 			}
// 			Ext.each(values,function(val){
// 				var record = this.findRecord(this.valueField, val);
// 				if(record){
// 					this.addRecord(record);
// 				}
// 				this.remoteLookup.push(val);
// 			},this);
// 			if(this.mode === 'remote'){
// 				var q = this.remoteLookup.join(this.queryValuesDelimiter);
// 				this.doQuery(q,false, true);
// 			}
// 		},
// 		// fix similar queries
// 		shouldQuery : function(q){
// 			if(this.lastQuery){
// 				return (q !== this.lastQuery);
// 			}
// 			return true;
// 		},
// 	});
// 	config.name += '[]';
//
// 	Ext.apply(config, {
// 		listeners: {
// 			newitem: function(bs, v) {
// 				bs.addNewItem({value: v});
// 			},
// 			beforequery: {
// 				fn: function (o) {
// 					// reset sort
// 					o.combo.store.sortInfo = '';
// 					if (o.forceAll !== false) {
// 						exclude = o.combo.getValue().split(o.combo.valueDelimiter);
// 					}else {
// 						exclude = [];
// 					}
// 					o.combo.store.baseParams.exclude = Ext.util.JSON.encode(exclude);
// 				},
// 				scope: this
// 			}
// 		},
// 	});
//
// 	miniShop2.combo.Options.superclass.constructor.call(this, config);
// };
// Ext.extend(miniShop2.combo.Options, Ext.ux.form.SuperBoxSelect);
// Ext.reg('minishop2-combo-options', miniShop2.combo.Options);



Ext.namespace('msoptionsprice.combo');


msoptionsprice.combo.optionKey = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-option-key-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-option-key-clear'
			});
		}
		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'key',
		hiddenName: config.name || 'key',
		displayField: 'key',
		valueField: 'key',
		editable: true,
		fields: ['key', 'caption'],
		pageSize: 10,
		emptyText: _('msoptionsprice_combo_select'),
		hideMode: 'offsets',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/misc/option/getkeys',
			combo: true,
			iskey: true,
			rid: config.rid || 0,
            addall: config.addall || 0
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
            '<tpl if="key"><b>{key}</b><br/></tpl>',
			'<small>{caption:this.renderCaption}</small>',
			'</div></tpl>',
			{
				compiled: true,
				renderCaption: function (value, record) {
					var title = value || record['key'];
					title = _('msoptionsprice_' + title) || _('ms2_product_' + title) || title;

					return title;
				}
			}),
		cls: 'input-combo-msoptionsprice-option-key',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
			this.getStore().reload();

			if (!!this.pageTb) {
				this.pageTb.show();
			}
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.clearValue();
		}
	});
	msoptionsprice.combo.optionKey.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.combo.optionKey, MODx.combo.ComboBox);
Ext.reg('msoptionsprice-combo-option-key', msoptionsprice.combo.optionKey);


msoptionsprice.combo.optionValues = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-option-values-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-option-values-clear'
			});
		}
		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'value',
		hiddenName: config.name || 'value',
		displayField: 'value',
		valueField: 'value',
		editable: true,//false,
		minChars: 1,
		fields: ['value'],
		pageSize: 10,
		emptyText: _('msoptionsprice_combo_select'),
		hideMode: 'offsets',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/misc/option/getvalues',
			combo: true,
			rid: config.rid || '',
			key: config.key || '',
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<b>{value}</b>',
			'</div></tpl>',
			{
				compiled: true
			}),
		cls: 'input-combo-msoptionsprice-option-values',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
			this.getStore().reload();

			if (!!this.pageTb) {
				this.pageTb.show();
			}
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.clearValue();
		}
	});
	msoptionsprice.combo.optionValues.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.combo.optionValues, MODx.combo.ComboBox);
Ext.reg('msoptionsprice-combo-option-values', msoptionsprice.combo.optionValues);


msoptionsprice.combo.modificationType = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-modification-type-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-modification-type-clear'
			});
		}
		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'type',
		hiddenName: config.name || 'type',
		displayField: 'name',
		valueField: 'id',
		editable: false,
		fields: ['id', 'name', 'description'],
		pageSize: 10,
		emptyText: _('msoptionsprice_combo_select'),
		hideMode: 'offsets',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/misc/modification/gettypes',
			combo: true,
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item" ext:qtip="{description}">',
			'<b>{name}</b>',
			'</div></tpl>',
			{
				compiled: true
			}),
		cls: 'input-combo-msoptionsprice-modification-type',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
			this.getStore().reload();

			if (!!this.pageTb) {
				this.pageTb.show();
			}
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.clearValue();
		}
	});
	msoptionsprice.combo.modificationType.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.combo.modificationType, MODx.combo.ComboBox);
Ext.reg('msoptionsprice-combo-modification-type', msoptionsprice.combo.modificationType);


msoptionsprice.combo.productImage = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-product-image-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-product-image-clear'
			});
		}
		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'type',
		hiddenName: config.name || 'type',
		displayField: 'name',
		valueField: 'id',
		editable: false,
		fields: ['id', 'name', 'thumbnail', 'url', 'description'],
		pageSize: 10,
		emptyText: _('msoptionsprice_combo_select'),
		hideMode: 'offsets',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/misc/product/getimages',
			combo: true,
			rid: config.rid || 0,
			parent: config.parent || 0
		},

		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<b>{name}</b>',
			'<tpl if="thumbnail">',
			'<div class="modx-pb-thumb msoptionsprice-thumb">',
			'<img src="{thumbnail}" ext:qtip="{url}" ext:qtitle="{name} {description}" ext:qclass="msoptionsprice-qtip"/>',
			'</div>',
			'</tpl>',
			'</div></tpl>',
			{
				compiled: true
			}),
		cls: 'input-combo-msoptionsprice-product-image',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
			this.getStore().reload();

			if (!!this.pageTb) {
				this.pageTb.show();
			}
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			if (this.pageTb) this.pageTb.show();
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.pageTb.show();
			this.clearValue();
		}
	});
	msoptionsprice.combo.productImage.superclass.constructor.call(this, config);

};
Ext.extend(msoptionsprice.combo.productImage, MODx.combo.ComboBox);
Ext.reg('msoptionsprice-combo-product-image', msoptionsprice.combo.productImage);


msoptionsprice.combo.Search = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		xtype: 'twintrigger',
		ctCls: 'x-field-search',
		allowBlank: true,
		msgTarget: 'under',
		emptyText: _('search'),
		name: 'query',
		triggerAction: 'all',
		clearBtnCls: 'x-field-search-clear',
		searchBtnCls: 'x-field-search-go',
		onTrigger1Click: this._triggerSearch,
		onTrigger2Click: this._triggerClear
	});
	msoptionsprice.combo.Search.superclass.constructor.call(this, config);
	this.on('render', function () {
		this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
			this._triggerSearch();
		}, this);
	});
	this.addEvents('clear', 'search');
};
Ext.extend(msoptionsprice.combo.Search, Ext.form.TwinTriggerField, {

	initComponent: function () {
		Ext.form.TwinTriggerField.superclass.initComponent.call(this);
		this.triggerConfig = {
			tag: 'span',
			cls: 'x-field-search-btns',
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger ' + this.searchBtnCls
			}, {
				tag: 'div',
				cls: 'x-form-trigger ' + this.clearBtnCls
			}]
		};
	},

	_triggerSearch: function () {
		this.fireEvent('search', this);
	},

	_triggerClear: function () {
		this.fireEvent('clear', this);
	}

});
Ext.reg('msoptionsprice-field-search', msoptionsprice.combo.Search);


msoptionsprice.combo.Source = function (config) {
	config = config || {};

	Ext.applyIf(config, {
		name: config.name || 'source-cmb',
		hiddenName: 'source-cmb',
		displayField: 'name',
		valueField: 'id',
		width: 220,
		allowBlank: false
	});
	msoptionsprice.combo.Source.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.combo.Source, MODx.combo.MediaSource);
Ext.reg('msoptionsprice-combo-source', msoptionsprice.combo.Source);
