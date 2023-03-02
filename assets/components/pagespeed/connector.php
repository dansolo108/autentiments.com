<?php
//
    /*
        ( function refresh( console ) {
            MODx.Ajax.request( {
                url : MODx.config.assets_url + 'components/PageSpeed/connector.php',
                params : {
                    action : 'refresh',
                    register : 'mgr' ,
                    topic : '/PageSpeed/refresh/'
                },
                listeners : {
                    success : { fn : function( response ) {
                        if( response.message === 'refresh' )
                            refresh( console );
                        else {
                            console.fireEvent( 'complete' );
                            console = null;
                        }
                    }, scope : this }
                },
                timeout : 60000
            } );
        } )( MODx.load( {
            xtype : 'modx-console',
            register : 'mgr',
            topic : '/PageSpeed/refresh/',
            show_filename : 0
        } ).show( Ext.getBody() ) ); 
        return false;
    */
    require_once dirname( __DIR__, 3 ) . '/config.core.php';
    require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
    require_once MODX_CONNECTORS_PATH . 'index.php';
    $modx->request->handleRequest( [
        'processors_path' => $modx->getOption( 'processorsPath', NULL, MODX_CORE_PATH . 'components/' . PageSpeed::class . '/processors/' )
    ] );
