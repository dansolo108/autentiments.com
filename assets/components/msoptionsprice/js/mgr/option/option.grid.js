msoptionsprice.grid.option = function (config) {
	config = config || {};

	/*console.log('----');
	 console.log(config);*/


	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false,
		enableCaching: false,
		tpl: new Ext.XTemplate(
			'<tpl for=".">',

			'<table class="msoptionsprice-expander"><tbody>',

			'<tpl if="description">',
			'<tr>',
			'<td><b>' + _('msoptionsprice_description') + ': </b>{description}</td>',
			'</tr>',
			'</tpl>',

			' </tbody></table>',

			'</tpl>',
			{
				compiled: true,
			}
		),
		renderer: function (v, p, record) {
			return !!record.json['description'] ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';
		}
	});

	this.exp.on('beforeexpand', function (rowexpander, record, body, rowIndex) {
		record['data']['json'] = record['json'];
		record['data'] = Ext.applyIf(record['data'], record['json']);
		return true;
	});

	Ext.applyIf(config, {
		id: 'msoptionsprice-grid-option',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/option/getlist',
			mid: config.record.id || 0,
			rid: config.record.rid || 0,
			//sort: 'rank',
			dir: 'asc'
		},
		fields: this.getFields(config),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),
		listeners: this.getListeners(config),

		plugins: [this.exp],
		paging: true,
		pageSize: 5,
		remoteSort: true,
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			autoFill: true,
			showPreview: true,
			scrollOffset: 0
		},
		autoHeight: true,
		cls: 'msoptionsprice-grid',
		bodyCssClass: 'grid-with-buttons',
		stateful: false,
	});
	msoptionsprice.grid.option.superclass.constructor.call(this, config);
	this.exp.addEvents('beforeexpand', 'beforecollapse');

};
Ext.extend(msoptionsprice.grid.option, MODx.grid.Grid, {
	windows: {},

	getFields: function (config) {
		var fields = msoptionsprice.config.grid_option_fields;

		return fields;
	},

	getTopBar: function (config) {
		var tbar = [];

		var component = ['create', 'left', 'description', 'spacer'];

		var add = {
			create: {
				text: '<i class="icon icon-plus"></i>',
				handler: this.create,
				record: config.record,
				scope: this
			},
			left: '->',

			spacer: {
				xtype: 'spacer',
				style: 'width:1px;'
			},

			bigspacer: {
				xtype: 'spacer',
				style: 'width:5px;'
			},

			description: {
				xtype: 'spacer',
				cls: 'modx-page-header',
				html: ''
			}
		};

		component.filter(function (item) {
			if (add[item]) {
				tbar.push(add[item]);
			}
		});

		var items = [];
		if (tbar.length > 0) {
			items.push(new Ext.Toolbar(tbar));
		}

		return new Ext.Panel({items: items});
	},

	getColumns: function (config) {
		var columns = [this.exp];
		var add = {
			id: {
				width: 5,
				hidden: true,
			},
			key: {
				width: 10,
				sortable: true,
				renderer: function (value, metaData, record) {
					var title = record['json']['caption'] || value;
					title = _('msoptionsprice_' + title) || _('ms2_product_' + title) || title;
					if (title != value) {
						title = String.format('{0} </small>({1})</small>', title, value);
					}
					return title;
				}
			},
			value: {
				width: 20,
				sortable: true,
			},
			actions: {
				width: 10,
				sortable: false,
				id: 'actions',
				renderer: msoptionsprice.tools.renderActions,

			}
		};

		var fields = this.getFields();
		fields.filter(function (field) {
			if (add[field]) {
				Ext.applyIf(add[field], {
					header: _('msoptionsprice_header_' + field),
					tooltip: _('msoptionsprice_tooltip_' + field),
					dataIndex: field
				});
				columns.push(add[field]);
			}
		});

		return columns;
	},

	getListeners: function (config) {
		return Ext.applyIf(config.listeners, {});
	},

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();
		var row = grid.getStore().getAt(rowIndex);
		var menu = msoptionsprice.tools.getMenu(row.data['actions'], this, ids);
		this.addContextMenuItem(menu);
	},

	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('id', row.id);
					return this._showMenu(this, ri, e);
				} else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e);
				}
			}
		}
		return this.processEvent('click', e);
	},

	setAction: function (method, field, value) {
		var ids = this._getSelectedIds();
		if (!ids.length && (field !== 'false')) {
			return false;
		}
		MODx.Ajax.request({
			url: msoptionsprice.config.connector_url,
			params: {
				action: 'mgr/option/multiple',
				method: method,
				field_name: field,
				field_value: value,
				ids: Ext.util.JSON.encode(ids)
			},
			listeners: {
				success: {
					fn: function (response) {
						this.refresh();
					},
					scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					},
					scope: this
				}
			}
		})
	},

	remove: function () {
		Ext.MessageBox.confirm(
			_('msoptionsprice_action_remove'),
			_('msoptionsprice_confirm_remove'),
			function (val) {
				if (val == 'yes') {
					this.setAction('remove');
				}
			},
			this
		);
	},

	removeOption: function () {
		var ids = this._getSelectedIds();
		if (!ids.length && (field !== 'false')) {
			return false;
		}
		Ext.MessageBox.confirm(
			_('msoptionsprice_action_remove'),
			_('msoptionsprice_confirm_remove'),
			function (val) {
				if (val == 'yes') {
					MODx.Ajax.request({
						url: msoptionsprice.config.connector_url,
						params: {
							action: 'mgr/option/remove',
							field_name: true,
							field_value: null,
							ids: Ext.util.JSON.encode(ids)
						},
						listeners: {
							success: {
								fn: function (response) {
									this.refresh();

									if (response.object && !msoptionsprice.tools.empty(response.object)) {
										Ext.getCmp('modx-panel-resource').setOptionValues(response.object);
									}
								},
								scope: this
							},
							failure: {
								fn: function (response) {
									MODx.msg.alert(_('error'), response.message);
								},
								scope: this
							}
						}
					})
				}
			},
			this
		);
	},

	create: function (btn, e) {
		var record = {
			mid: this.config.record.id || 0,
			rid: this.config.record.rid || 0,
			active: 1,
		};

		var w = MODx.load({
			xtype: 'msoptionsprice-window-option',
			action: 'mgr/option/create',
			record: record,
			listeners: {
				success: {
					fn: function (response) {
						this.refresh();

						if (response.a.result && !msoptionsprice.tools.empty(response.a.result.object)) {
							Ext.getCmp('modx-panel-resource').setOptionValues(response.a.result.object);
						}

					}, scope: this
				}
			}
		});
		w.reset();
		w.setValues(record);
		w.show(e.target);
	},

	_updateRow: function () {
		this.refresh();
	},

	_getSelectedIds: function () {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push([selected[i]['json']['mid'], selected[i]['json']['rid'], selected[i]['json']['key'], selected[i]['json']['value']]);
		}

		return ids;
	}

});
Ext.reg('msoptionsprice-grid-option', msoptionsprice.grid.option);