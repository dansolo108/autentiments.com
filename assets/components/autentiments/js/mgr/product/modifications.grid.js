autentimentsPanel.grid.Modifications = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mspr-grid-modification-remains'
        ,url: autentimentsPanel.config.connector_url
        ,baseParams: {
            action: 'mgr/modification/getlist',
            processors_path:autentimentsPanel.config.processors_path,
            product_id: MODx.request.id
        }
        ,save_action: 'mgr/modification/updatefromgrid'
        ,fields: this.getColumns().map(value=>{return value['dataIndex']})
        ,autosave: true
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,sm: this.sm
        ,columns: this.getColumns()
    });
    autentimentsPanel.grid.Modifications.superclass.constructor.call(this,config);
};
Ext.extend(autentimentsPanel.grid.Modifications,MODx.grid.Grid,{
    windows: {}

    ,getColumns: function() {
        var all = {
            id: {hidden: true,sortable: true, width: 100}
            ,code:{width: 100,sortable: true,editor: {xtype:'textfield'}}
            ,price: {width: 100, editor: {xtype:'numberfield'}}
            ,old_price: {width: 100, editor: {xtype:'numberfield'}}
            ,hide: {sortable: true, width: 100,renderer: msProductRemains.utils.bool, editor: {xtype:'combo-boolean'}}
        };
        var columns = [];
        for(let key in all){
            Ext.applyIf(all[key], {
                header: _('autentiments_modification_' + key),
                dataIndex: key
            });
            columns.push(all[key]);
        }
        let options = autentimentsPanel.config.options;
        if(options){
            options.forEach((value,key)=>{
                columns.push({
                    header:value['name'],
                    width: 100,
                    sortable: true,
                    dataIndex:'option:'+value['name'],
                    editor: {xtype:'textfield'},
                    renderer:(value)=>{
                        if (value == false || value == undefined) {
                            return `<span class="red">Отсутствует</span>`
                        }
                        return value;
                    }
                })
            })
        }
        let stores = autentimentsPanel.config.stores;
        if(stores){
            stores.forEach((value,key)=>{
                columns.push({
                    header:value['name'],
                    width: 100,
                    sortable: true,
                    dataIndex:'store:'+value['id'],
                    editor: {xtype:'numberfield'}
                })
            })
        }
        return columns;
    }

});
Ext.reg('mspr-grid-modification-remains',autentimentsPanel.grid.Modifications);
