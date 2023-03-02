msoptionsprice.panel.Images = function (config) {
	config = config || {};

	this.view = MODx.load({
		xtype: 'msoptionsprice-gallery-images-view',
		cls: 'msoptionsprice-gallery-images',
		containerScroll: true,
		pageSize: parseInt(config.pageSize || MODx.config.default_per_page),
		product_id: config.product_id,
		mid: config.mid,
	});

	Ext.applyIf(config, {
		cls: 'browser-view',
		border: false,
		items: [this.view],
		tbar: this.getTopBar(config),
		bbar: this.getBottomBar(config),
	});
	msoptionsprice.panel.Images.superclass.constructor.call(this, config);

	var dv = this.view;
	dv.on('render', function () {
		dv.dragZone = new miniShop2.DragZone(dv);
		dv.dropZone = new miniShop2.DropZone(dv);
	});

};
Ext.extend(msoptionsprice.panel.Images, MODx.Panel, {

	_doSearch: function (tf) {
		this.view.getStore().baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
	},

	_clearSearch: function () {
		this.view.getStore().baseParams.query = '';
		this.getBottomToolbar().changePage(1);
	},

	getTopBar: function (config) {
		return new Ext.Toolbar({
			items: [{
				xtype: 'msoptionsprice-combo-source',
				width: 220,
				value: config.resource.source,
				name: 'source',
				hiddenName: 'source',
				listeners: {
					select: {
						fn: this._filterBySource,
						scope: this
					},
					afterrender: {
						fn: this._filterBySource,
						scope: this
					}
				}
			}, '->', {
				xtype: 'msoptionsprice-field-search',
				width: 220,
				listeners: {
					search: {
						fn: function (field) {
							this._doSearch(field);
						}, scope: this
					},
					clear: {
						fn: function (field) {
							field.setValue('');
							this._clearSearch();
						}, scope: this
					},
				},
			}]
		})
	},

	getBottomBar: function (config) {
		return new Ext.PagingToolbar({
			pageSize: parseInt(config.pageSize || MODx.config.default_per_page),
			store: this.view.store,
			displayInfo: true,
			autoLoad: true,
			items: ['-',
				_('per_page') + ':',
				{
					xtype: 'textfield',
					value: parseInt(config.pageSize || MODx.config.default_per_page),
					width: 50,
					listeners: {
						change: {
							fn: function (tf, nv) {
								if (Ext.isEmpty(nv)) {
									return;
								}
								nv = parseInt(nv);
								this.getBottomToolbar().pageSize = nv;
								this.view.getStore().load({params: {start: 0, limit: nv}});
							}, scope: this
						},
						render: {
							fn: function (cmp) {
								new Ext.KeyMap(cmp.getEl(), {
									key: Ext.EventObject.ENTER,
									fn: function () {
										this.fireEvent('change', this.getValue());
										this.blur();
										return true;
									},
									scope: cmp
								});
							}, scope: this
						}
					}
				}
			]
		});
	},

	_filterBySource: function (cb) {
		if (cb.value == '' || cb.value == 0) {
			cb.value = MODx.config['default_media_source'] || 1;
			cb.setValue(cb.value);
		}
		this.view.getStore().baseParams[cb.name] = cb.value;
		this.getBottomToolbar().changePage(1);
	},

});
Ext.reg('msoptionsprice-gallery-images-panel', msoptionsprice.panel.Images);


msoptionsprice.view.Images = function (config) {
	config = config || {};

	this._initTemplates();

	Ext.applyIf(config, {
		url: msoptionsprice.config['connector_url'],
		fields: [
			'id', 'product_id', 'name', 'description', 'url', 'createdon', 'createdby', 'file',
			'thumbnail', 'source', 'source_name', 'type', 'rank', 'active', 'properties', 'class',
			'add', 'alt', 'actions', 'over_actions', 'modification_image'
		],
		baseParams: {
			action: 'mgr/gallery/minishop2/getlist',
			product_id: config.product_id,
			mid: config.mid,
			parent: 0,
			type: 'image',
			limit: config.pageSize || MODx.config.default_per_page
		},
		enableDD: true,
		multiSelect: true,
		tpl: this.templates.thumb,
		itemSelector: 'div.modx-browser-thumb-wrap',
		listeners: {
			'mouseenter': {fn: this._showOverMenu, scope: this},
		},
		prepareData: this.formatData.createDelegate(this)
	});
	msoptionsprice.view.Images.superclass.constructor.call(this, config);

	this.addEvents('sort', 'select');
	this.on('sort', this.onSort, this);
	this.on('dblclick', this.onDblClick, this);

	var widget = this;
	this.getStore().on('beforeload', function () {
		var el = widget.getEl();
		if (el) {
			el.mask(_('loading'), 'x-mask-loading');
		}
	});
	this.getStore().on('load', function () {
		var el = widget.getEl();
		if (el) {
			/*el.unmask();*/
		}
	});

};
Ext.extend(msoptionsprice.view.Images, MODx.DataView, {

	templates: {},
	windows: {},

	_showOverMenu: function (v, i, n, e) {
		e.preventDefault();
		var data = this.lookup[n.id];
		var m = this.cm;
		m.removeAll();

		var menu = miniShop2.utils.getMenu(data['over_actions'], this, this._getSelectedIds());

		for (var item in menu) {
			if (!menu.hasOwnProperty(item)) {
				continue;
			}
			m.add(menu[item]);
		}

		m.defaultOffsets = [-18, -20];
		m.defaultAlign = 'tl-bl?';
		m.cls = 'msoptionsprice-over-menu';
		m.show(n, 'tl-c?');
		m.activeNode = n;
	},

	addFile: function () {

		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) {
			return;
		}

		MODx.Ajax.request({
			url: msoptionsprice.config['connector_url'],
			params: {
				action: 'mgr/misc/gallery/update',
				mode: 'add',
				mid: this.config.mid,
				image: data.id,
			},
			listeners: {
				success: {
					fn: function (r) {
						this.store.reload();
						this.updateImage(r.object);
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

	removeFile: function () {

		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) {
			return;
		}

		MODx.Ajax.request({
			url: msoptionsprice.config['connector_url'],
			params: {
				action: 'mgr/misc/gallery/update',
				mode: 'remove',
				mid: this.config.mid,
				image: data.id,
			},
			listeners: {
				success: {
					fn: function (r) {
						this.store.reload();
						this.updateImage(r.object);
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


	onSort: function (o) {
		var el = this.getEl();
		el.mask(_('loading'), 'x-mask-loading');
		MODx.Ajax.request({
			url: msoptionsprice.config['connector_url'],
			params: {
				action: 'mgr/misc/gallery/sort',
				product_id: this.config.product_id,
				mid: this.config.mid,
				source: o.source.id,
				target: o.target.id
			},
			listeners: {
				success: {
					fn: function (r) {
						el.unmask();
						this.store.reload();
						this.updateImage(r.object);
					}, scope: this
				}
			}
		});
	},

	onDblClick: function (e) {
		var node = this.getSelectedNodes()[0];
		if (!node) {
			return;
		}

		this.cm.activeNode = node;
		this.updateFile(node, e);
	},

	updateFile: function (btn, e) {
		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) {
			return;
		}

		var w = MODx.load({
			xtype: 'msoptionsprice-gallery-image',
			record: data,
			listeners: {
				success: {
					fn: function () {
						this.store.reload()
					}, scope: this
				}
			}
		});
		w.setValues(data);
		w.show(e.target);
	},

	showFile: function () {
		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) {
			return;
		}

		window.open(data.url);
	},

	fileAction: function (method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		this.getEl().mask(_('loading'), 'x-mask-loading');
		MODx.Ajax.request({
			url: miniShop2.config['connector_url'],
			params: {
				action: 'mgr/gallery/multiple',
				method: method,
				ids: Ext.util.JSON.encode(ids),
			},
			listeners: {
				success: {
					fn: function (r) {
						this.store.reload();
					}, scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					}, scope: this
				},
			}
		})
	},

	deleteFiles: function () {
		var ids = this._getSelectedIds();
		var title = ids.length > 1
			? 'ms2_gallery_file_delete_multiple'
			: 'ms2_gallery_file_delete';
		var message = ids.length > 1
			? 'ms2_gallery_file_delete_multiple_confirm'
			: 'ms2_gallery_file_delete_confirm';
		Ext.MessageBox.confirm(
			_(title),
			_(message),
			function (val) {
				if (val == 'yes') {
					this.fileAction('remove');
				}
			},
			this
		);
	},

	generateThumbs: function () {
		this.fileAction('generate');
	},

	updateImage: function (a) {
		if (!a.hasOwnProperty('image')) {
			return;
		}

		var f = this.findParentByType('form').getForm();
		var _image = f.findField('image');

		_image.setValue(a['image']);
	},

	run: function (p) {
		p = p || {};
		var v = {};
		Ext.apply(v, this.store.baseParams);
		Ext.apply(v, p);
		this.changePage(1);
		this.store.baseParams = v;
		this.store.load();
	},

	formatData: function (data) {
		data.shortName = Ext.util.Format.ellipsis(data.name, 20);
		data.createdon = miniShop2.utils.formatDate(data.createdon);
		data.size = (data.properties['width'] && data.properties['height'])
			? data.properties['width'] + 'x' + data.properties['height']
			: '';
		if (data.properties['size'] && data.size) {
			data.size += ', ';
		}
		data.size += data.properties['size']
			? miniShop2.utils.formatSize(data.properties['size'])
			: '';
		this.lookup['msoptionsprice-gallery-image-' + data.id] = data;
		return data;
	},

	_initTemplates: function () {
		this.templates.thumb = new Ext.XTemplate(
			'<tpl for=".">\
				<div class="modx-browser-thumb-wrap modx-pb-thumb-wrap msoptionsprice-gallery-thumb-wrap {class:this.renderClass}" id="msoptionsprice-gallery-image-{id}">\
					<div class="modx-browser-thumb modx-pb-thumb msoptionsprice-gallery-thumb">\
						<img src="{thumbnail}" title="{name}" />\
					</div>\
					<small>{rank}. {shortName}</small>\
				</div>\
			</tpl>',
			{
				compiled: true,
				renderClass: function (value, record) {
					if (record['modification_image']) {
						value += ' msoptionsprice-gallery-add'
					}
					return value;
				}
			});
	},

	_showContextMenu: function (v, i, n, e) {
		e.preventDefault();
		var data = this.lookup[n.id];
		var m = this.cm;
		m.removeAll();

		var menu = miniShop2.utils.getMenu(data.actions, this, this._getSelectedIds());
		for (var item in menu) {
			if (!menu.hasOwnProperty(item)) {
				continue;
			}
			m.add(menu[item]);
		}

		m.show(n, 'tl-c?');
		m.activeNode = n;
	},

	_getSelectedIds: function () {
		var ids = [];
		var selected = this.getSelectedRecords();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['id']);
		}

		return ids;
	},

});
Ext.reg('msoptionsprice-gallery-images-view', msoptionsprice.view.Images);