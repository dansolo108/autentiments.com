msProductRemains.grid.ProductRemains = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'mspr-grid-productremains'
        ,url: msProductRemains.config.connector_url
        ,baseParams: {
            action: 'mgr/product/getlist'
            ,product_id: MODx.request.id
        }
        ,fields: msProductRemains.config.product_grid_fields
        ,save_action: 'mgr/remains/updatefromgrid'
        ,autosave: true
        ,save_callback: this.updateRow
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,sm: this.sm
        ,columns: this.getColumns()
    });
    msProductRemains.grid.ProductRemains.superclass.constructor.call(this,config);
};
Ext.extend(msProductRemains.grid.ProductRemains,MODx.grid.Grid,{
    windows: {}

    ,getColumns: function() {
        var all = {
            id: {hidden: true, sortable: true, width: 40}
            ,store_id: {hidden: true, header: _('ms2_product_store_id'), sortable: true, width: 50}
            ,remains: {header: _('mspr_product_remains'), sortable: true, width: 50, editor: {xtype:'numberfield'}}
            ,store_name: {header: 'Склад', sortable: true, width: 50}
            ,size: {header: _('mspr_product_size'), width: 100, renderer: msProductRemains.utils.defined}
            ,color: {header: _('mspr_product_color'), width: 100, renderer: msProductRemains.utils.defined}
            ,price: {header: _('ms2_product_price'), width: 100, renderer: msProductRemains.utils.defined}
            ,old_price: {header: _('ms2_product_old_price'), width: 100, renderer: msProductRemains.utils.defined}
            ,hide: {header: 'Скрыть', sortable: true, width: 50, renderer: msProductRemains.utils.bool, editor: {xtype:'combo-boolean'}}
        };
        for (var i in msProductRemains.plugin) {
            if (typeof(msProductRemains.plugin[i]['getColumns']) == 'function') {
                var add = msProductRemains.plugin[i].getColumns();
                Ext.apply(all, add);
            }
        }

        var options = miniShop2.config.option_fields;
        for (var i = 0; i < options.length; i++) {
            Ext.apply(all, {[options[i].key]: {header: options[i].caption, width: 100, renderer: msProductRemains.utils.defined}});
        }

        var columns = [this.sm];
        for(var i=0; i < msProductRemains.config.product_grid_fields.length; i++) {
            var field = msProductRemains.config.product_grid_fields[i];
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

});
Ext.reg('mspr-grid-productremains',msProductRemains.grid.ProductRemains);
