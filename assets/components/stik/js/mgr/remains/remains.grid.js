msProductRemains.grid.Remains = function(config) {
	config = config || {};
	this.sm = new Ext.grid.CheckboxSelectionModel();
	Ext.applyIf(config,{
		id: 'mspr-grid-remains'
		,url: msProductRemains.config.connector_url
		,baseParams: {
			action: 'mgr/remains/getlist'
		}
		,fields: msProductRemains.config.grid_fields
		,save_action: 'mgr/remains/updatefromgrid'
		,autosave: true
		,save_callback: this.updateRow
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,sm: this.sm
		,columns: this.getColumns()
		,tbar: [{
				text: '<i class="icon icon-list"></i> ' + _('mspr_bulk_actions')
				,menu: [{
					text: _('mspr_menu_remove_multiple')
					,handler: this.removeSelected
					,scope: this
				}]
			}
			,{xtype: 'spacer',style: 'width:50px;'}
			,{
				xtype: 'mspr-combo-category'
				,id: 'tbar-mspr-combo-category'
				,width: 200
				,addall: true
				,emptyText: _('mspr_filter_category')
				,listeners: {
					select: {fn: this.filterByCategory, scope:this}
				}
			}
			,{xtype: 'spacer',style: 'width:50px;'}
			,{
				xtype: 'mspr-combo-vendor'
				,id: 'tbar-mspr-combo-vendor'
				,width: 200
				,addall: true
				,emptyText: _('mspr_filter_vendor')
				,listeners: {
					select: {fn: this.filterByVendor, scope:this}
				}
			},'->',{
				xtype: 'textfield'
				,name: 'query'
				,width: 200
				,id: 'mspr-remains-search'
				,emptyText: _('mspr_search')
				,listeners: {
					render: {fn:function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER,function() {this.FilterByQuery(tf);},this);},scope:this}
				}
			},{
				xtype: 'button'
				,id: 'mspr-remains-clear'
				,text: '<i class="icon icon-times"></i>'
				,listeners: {
					click: {fn: this.clearFilter, scope: this}
				}
			}
		]
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
			}
		}
	});
	msProductRemains.grid.Remains.superclass.constructor.call(this,config);
};
Ext.extend(msProductRemains.grid.Remains,MODx.grid.Grid,{
	windows: {}

	,getMenu: function() {
		var m = [];
		m.push({
			text: _('mspr_menu_remove')
			,handler: this.removeOrder
		});
		this.addContextMenuItem(m);
	}

	,FilterByQuery: function(tf, nv, ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,filterByCategory: function(cb) {
		this.getStore().baseParams['parent'] = cb.value;
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,filterByVendor: function(cb) {
		this.getStore().baseParams['vendor'] = cb.value;
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function(btn,e) {
		var s = this.getStore();
		s.baseParams.query = '';
		s.baseParams['parent'] = '';
		s.baseParams['vendor'] = '';
		Ext.getCmp('tbar-mspr-combo-category').setValue('');
		Ext.getCmp('tbar-mspr-combo-vendor').setValue('');
		Ext.getCmp('mspr-remains-search').setValue('');
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,getColumns: function() {
		var all = {
			id: {hidden: true, sortable: true, width: 40}
			,product_id: {hidden: true, header: _('mspr_product_id'), sortable: true, width: 40}
			,options: {hidden: true, width: 100}
			,remains: {header: _('mspr_product_remains'), sortable: true, width: 50, editor: {xtype:'numberfield'}}
			,pagetitle: {header: _('mspr_product_name'), sortable: true, width: 100, renderer: msProductRemains.utils.productLink}
			,color: {header: _('mspr_product_color'), width: 100, renderer: msProductRemains.utils.defined}
			,size: {header: _('mspr_product_size'), width: 100, renderer: msProductRemains.utils.defined}
			,weight: {sortable: true, width: 50}
			,price: {sortable: true, width: 50}
			,article: {sortable: true, width: 50}
			,published: {sortable: true, width: 50, renderer: msProductRemains.utils.bool}
		};

		for (var i in msProductRemains.plugin) {
			if (typeof(msProductRemains.plugin[i]['getColumns']) == 'function') {
				var add = msProductRemains.plugin[i].getColumns();
				Ext.apply(all, add);
			}
		}

		var options = msProductRemains.config.ms2_option_fields;
		for (var i = 0; i < options.length; i++) {
			var field = miniShop2.utils.getExtField(msProductRemains.config, options[i].key, options[i], 'extra-column');
			if (field) {
				field[options[i].key].dataIndex = options[i].key;
				field[options[i].key].renderer = msProductRemains.utils.defined;
				Ext.apply(all, field);
			}
		}

		var columns = [this.sm];
		for(var i=0; i < msProductRemains.config.grid_fields.length; i++) {
			var field = msProductRemains.config.grid_fields[i];
			if (all[field]) {
				Ext.applyIf(all[field], {
					header: _('ms2_product_' + field)
					,dataIndex: field
				});
				columns.push(all[field]);
			}
		}

		return columns;
	}
	
	,updateRow: function(response) {
		var row = response.object;
		var items = this.store.data.items;

		for (var i = 0; i < items.length; i++) {
			var item = items[i];
			if (item.id == row.id)
				item.data.instock = (item.data.remains > 0) ? 1 : 0;
		}
	}

	,removeOrder: function(btn,e) {
		if (!this.menu.record) return;

		MODx.msg.confirm({
			title: _('mspr_menu_remove')
			,text: _('mspr_menu_remove_confirm')
			,url: msProductRemains.config.connector_url
			,params: {
				action: 'mgr/remains/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				success: {fn:function(r) { this.refresh();}, scope:this}
			}
		});
	}

	,removeSelected: function(btn,e) {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.msg.confirm({
			title: _('mspr_menu_remove_multiple')
			,text: _('mspr_menu_remove_multiple_confirm')
			,url: msProductRemains.config.connector_url
			,params: {
				action: 'mgr/remains/remove_multiple'
				,ids: cs
			}
			,listeners: {
				success: {fn:function(r) {
					this.getSelectionModel().clearSelections(true);
					this.refresh();
				},scope:this}
			}
		});
		return true;
	}
});
Ext.reg('mspr-grid-remains',msProductRemains.grid.Remains);
