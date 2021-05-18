Ext.namespace('msProductRemains.combo');

msProductRemains.combo.Category = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'mspr-combo-section'
		,fieldLabel: _('mspr_filter_category')
		,fields: ['id','pagetitle','parents']
		,valueField: 'id'
		,displayField: 'pagetitle'
		,name: 'parent-cmb'
		,hiddenName: 'parent-cmp'
		,allowBlank: false
		,url: msProductRemains.config.ms2_connector_url
		,baseParams: {
			action: 'mgr/category/getcats'
			,combo: 1
			,id: config.value
		}
		,tpl: new Ext.XTemplate(''
			+'<tpl for="."><div class="x-combo-list-item mspr-category-list-item">'
			+'<tpl if="parents">'
					+'<span class="parents">'
						+'<tpl for="parents">'
							+'<nobr><small>{pagetitle} / </small></nobr>'
						+'</tpl>'
					+'</span>'
			+'</tpl>'
			+'<span><small>({id})</small> <b>{pagetitle}</b></span>'
			+'</div></tpl>',{
			compiled: true
		})
		,itemSelector: 'div.mspr-category-list-item'
		,pageSize: 20
		,editable: true
	});
	msProductRemains.combo.Category.superclass.constructor.call(this,config);
};
Ext.extend(msProductRemains.combo.Category,MODx.combo.ComboBox);
Ext.reg('mspr-combo-category',msProductRemains.combo.Category);

msProductRemains.combo.Vendor = function(config) {
	config = config || {};

	Ext.applyIf(config,{
		name: config.name || 'vendor'
		,fieldLabel: _('mspr_filter_vendor')
		,hiddenName: config.name || 'vendor'
		,displayField: 'name'
		,valueField: 'id'
		,anchor: '99%'
		,fields: ['name','id']
		,pageSize: 20
		,url: msProductRemains.config.ms2_connector_url
		,typeAhead: true
		,editable: true
		,allowBlank: true
		,emptyText: _('no')
		,baseParams: {
			action: 'mgr/settings/vendor/getlist'
			,combo: 1
			,id: config.value
		}
	});
	msProductRemains.combo.Vendor.superclass.constructor.call(this,config);
};
Ext.extend(msProductRemains.combo.Vendor,MODx.combo.ComboBox);
Ext.reg('mspr-combo-vendor',msProductRemains.combo.Vendor);