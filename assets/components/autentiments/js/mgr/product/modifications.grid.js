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
    ,sorters: {
    field: 'sort_index',
        direction: 'ASC'
    },
    getMenu: function() {
        return [{
            text: _('autentiments_modification_remove')
            ,handler: this.remove
        }]
    },
    remove: function(btn,e) {
        MODx.msg.confirm({
            title: _('autentiments_modification_remove')+'?'
            ,text: _('autentiments_modification_remove_confirm')
            ,url: autentimentsPanel.config.connector_url
            ,params: {
                action: 'mgr/modification/remove'
                ,id: this.getSelectionModel().getSelected().id
                ,processors_path:autentimentsPanel.config.processors_path,
            }
            ,listeners: {
                'success': {
                    fn:this.refresh,scope:this
                }
            }
        });
    }
    ,getColumns: function() {

        var all = {
            id: {sortable: true, width: 30,header:'id'}
            ,preview:{sortable:false,width:40,header:_('autentiments_modification_preview'),renderer:(value)=>{
                    if (value == false || value == undefined) {
                        return `<span class="red">Отсутствует</span>`
                    }
                    return `<img src="${value}" style="height: auto;width: 100%"/>`;
                }}
            ,code:{width: 100,sortable: true,editor: {xtype:'textfield'},renderer:(value)=>{
                    if (value == false || value == undefined) {
                        return `<span class="red">Отсутствует</span>`
                    }
                    return value;
                }}
            ,price: {width: 100, editor: {xtype:'numberfield'}}
            ,old_price: {width: 100, editor: {xtype:'numberfield'}}
            ,hide: {sortable: true, width: 100,renderer: this.renderers.bool, editor: {xtype:'combo-boolean'}}
            ,hide_remains: {sortable: true, width: 100,renderer: this.renderers.bool, editor: {xtype:'combo-boolean'}}
            ,sort_index: {sortable: true, width: 100, editor: {xtype:'numberfield'}},
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
        console.log(options);
        if(options){
            options.forEach((value,key)=>{
                columns.push({
                    header:_(value['name']),
                    width: 100,
                    sortable: true,
                    dataIndex:'option:'+value['id'],
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
        console.log(columns);
        return columns;
    },
    renderers:{
        bool: function(value) {
            var color, text;
            if (value == 0 || value == false || value == undefined) {
                color = 'green';
                text = _('no');
            }
            else {
                color = 'red';
                text = _('yes');
            }

            return String.format('<span class="{0}">{1}</span>', color, text);
        }
    }

});
Ext.reg('mspr-grid-modification-remains',autentimentsPanel.grid.Modifications);
