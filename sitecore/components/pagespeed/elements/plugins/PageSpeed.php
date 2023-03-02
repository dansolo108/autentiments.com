<?php
//
switch( $modx->event->name ) {
    case 'OnBeforeRegisterClientScripts' :
        if( isset( $modx->PageSpeed ) && $modx->PageSpeed instanceof \PageSpeed ) {
            /*
            if( $modx->resource->id && $modx->PageSpeed->properties[ 'enable' ] ) {
                if( $modContentType = $modx->getObject( 'modContentType', [
                    'name' => $name = 'HTML'
                ] ) ) {
                    if( $modx->resource->content_type === $modContentType->get( 'id' ) ) {
                        $modx->PageSpeed->val = [ $modx->sjscripts, $modx->jscripts, $modx->loadedjscripts ];
                        $modx->sjscripts = $modx->jscripts = $modx->loadedjscripts = [];
                    }
                } else
                    $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_modContentType', [ 'name' => $name ] ) );
            }
            */
            if( $modx->resource->id && $modx->resource->content_type === 1 && $modx->PageSpeed->properties[ 'enable' ] ) {
                $modx->PageSpeed->val = [ $modx->sjscripts, $modx->jscripts, $modx->loadedjscripts ];
                $modx->sjscripts = $modx->jscripts = $modx->loadedjscripts = [];
            }
        }
        break;
    case 'OnCacheUpdate' :
        if( isset( $modx->PageSpeed ) && $modx->PageSpeed instanceof \PageSpeed ) {
            if( isset( $results[ \PageSpeed::class ] ) && $results[ \PageSpeed::class ] ) {
                $properties = [];
                $modSystemSetting = $modx->getObject( 'modSystemSetting', [ 'key' => $key = \PageSpeed::class . '_path' ] );
                if( $modSystemSetting instanceof \modSystemSetting || $modSystemSetting instanceof \MODX\Revolution\modSystemSetting )
                    $properties[] = $modx->PageSpeed->replacePlaceholders( \PageSpeed::PCRE_MODX_CONFIG_PROPERTY, $modx->config, $modSystemSetting->get( 'value' ) );
                foreach( [ 'modContextSetting', 'modUserGroupSetting', 'modUserSetting' ] as $className )
                    foreach( $modx->getCollection( $className, [ 'key' => $key ] ) as $xPDOObject )
                        $properties[] = $modx->PageSpeed->replacePlaceholders( \PageSpeed::PCRE_MODX_CONFIG_PROPERTY, $modx->config, $xPDOObject->get( 'value' ) );
                $modx->PageSpeed->block();
                declare( ticks = 1 ) {
                    foreach( array_unique( $properties ) as $property )
                        if( is_dir( $property ) ) {
                            $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'refresh_PageSpeed' ) . $property );
                            $index = 1;
                            $value = 0;
                            foreach( new Class( new \FilesystemIterator( $property ) ) extends \SplHeap {
                                public function __construct( Iterator $iterator ) {
                                    foreach( $iterator as $value )
                                        $this->insert( $value );
                                }
                                public function compare( $value1, $value2 ) {
                                    return strcmp( $value2->getRealpath(), $value1->getRealpath() );
                                }
                            } as $SplFileInfo ) {
                                if( strlen( $property ) + \PageSpeed::CACHE_SPLIT_LENGTH === strlen( $path = $SplFileInfo->getRealPath() ) )
                                    $value = round( ( hexdec( preg_replace( sprintf( \PageSpeed::FORMAT_PCRE_START_OF_STRING, preg_quote( $property, \PageSpeed::PCRE_DELIMITER ) ), '', $path ) ) / ( pow( 16, \PageSpeed::CACHE_SPLIT_LENGTH ) - 1 ) ) * 100 );
                                if( $SplFileInfo->isDir() ) {
                                    foreach( new \RecursiveIteratorIterator(
                                                 new \RecursiveDirectoryIterator( $path, \RecursiveDirectoryIterator::SKIP_DOTS ),
                                                 \RecursiveIteratorIterator::CHILD_FIRST
                                             ) as $SplFileInfo )
                                        ( $SplFileInfo->isDir() ? 'rmdir' : 'unlink' )( $SplFileInfo->getRealPath() );
                                    rmdir( $path );
                                } else
                                    unlink( $path );
                                if( $value && microtime( TRUE ) - $modx->startTime > \PageSpeed::CACHE_LOG_INTERVAL * $index && $index++ )
                                    $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'refresh_PageSpeed' ) . sprintf( \PageSpeed::FORMAT_PROGRESS, $value ) );
                            }
                        } else
                            $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_is_dir', [ 'filename' => $property ] ) );
                }
            }
        }
        break;
    case 'OnHandleRequest' :
        if( isset( $modx->PageSpeed ) && $modx->PageSpeed instanceof \PageSpeed ) {
            if( isset( $_REQUEST[ $modx->event->name ] ) && $_REQUEST[ $modx->event->name ] === \PageSpeed::class && isset( $_REQUEST[ 'key' ] ) && is_string( $_REQUEST[ 'key' ] ) ) {
                $modx->resource = $modx->newObject( 'modResource' );
                if( $modx->PageSpeed->val = $modx->cacheManager->get( $modx->PageSpeed->key = $_REQUEST[ 'key' ], $modx->PageSpeed->options ) ) {
                    $modx->PageSpeed->properties = $modx->PageSpeed->val[ 'properties' ];
                    if( $modx->PageSpeed->properties[ 'WEBP' ] && $modx->PageSpeed->properties[ 'convert' ] === 'dynamic' ) {
                        if( isset( $_REQUEST[ 'convert' ] ) && is_string( $_REQUEST[ 'convert' ] ) ) {
                            if( $modContentType = $modx->getObject( 'modContentType', [
                                'name' => $name = 'WEBP',
                                'description' => \PageSpeed::class
                            ] ) ) {
                                if( $data = $modx->PageSpeed->data( $modx->PageSpeed->url( $_REQUEST[ 'convert' ] ) ) ) {
                                    foreach( [
                                                 sprintf( \PageSpeed::FORMAT_HTTP_HEADER, 'cache-control', $modx->PageSpeed->properties[ 'lifetime' ] === 0 ? 'public' : 'public, max-age=' . $modx->PageSpeed->properties[ 'lifetime' ] ),
                                                 sprintf( \PageSpeed::FORMAT_HTTP_HEADER, 'expires', gmdate( 'D, d M Y H:i:s', intval( $modx->PageSpeed->microtime ) + $modx->PageSpeed->properties[ 'lifetime' ] ) . ' GMT' ),
                                                 sprintf( \PageSpeed::FORMAT_HTTP_HEADER, 'pragma', 'cache' )
                                             ] as $header )
                                        header( $header );
                                    $modx->resource->fromArray( [
                                        'cacheable' => 1,
                                        'content_type' => $modContentType->id
                                    ] );
                                    $modx->resource->setProcessed( TRUE );
                                    $modx->resource->_content = $modx->PageSpeed->convert( $data );
                                }
                            } else
                                $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_modContentType', [ 'name' => $name ] ) );
                        }
                    }
                    if( $modx->PageSpeed->properties[ 'critical' ] ) {
                        if( isset( $_REQUEST[ 'critical' ] ) && is_string( $_REQUEST[ 'critical' ] ) ) {
                            if( isset( $modx->PageSpeed->val[ 'critical' ] ) )
                                $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_critical', [ 'key' => $modx->PageSpeed->key ] ) );
                            else {
                                $modx->PageSpeed->val[ 'critical' ] = $_REQUEST[ 'critical' ];
                                $modx->cacheManager->set( $modx->PageSpeed->key, $modx->PageSpeed->val, $modx->PageSpeed->properties[ 'lifetime' ], $modx->PageSpeed->options );
                            }
                        }
                    }
                    if( $modx->PageSpeed->properties[ 'resize' ] ) {
                        if( isset( $_REQUEST[ 'resize' ] ) && is_array( $_REQUEST[ 'resize' ] ) ) {
                            foreach( array_filter( $_REQUEST[ 'resize' ], function( $val ) {
                                return is_array( $val ) && count( $val ) === 3 &&
                                    isset( $val[ 'src' ] ) && is_string( $val[ 'src' ] ) &&
                                    isset( $val[ 'width' ] ) && is_string( $val[ 'width' ] ) && intval( $val[ 'width' ] ) > 0 &&
                                    isset( $val[ 'height' ] ) && is_string( $val[ 'height' ] ) && intval( $val[ 'height' ] ) > 0;
                            } ) as $value )
                                if( (
                                        in_array( $extension = $modx->PageSpeed->extension( $value[ 'src' ] ), array_merge( \PageSpeed::EXTENSION_IMAGE, \PageSpeed::EXTENSION_IMAGE_WEBP ) ) &&
                                        $filename = $modx->PageSpeed->filename( $value[ 'src' ], $modx->PageSpeed->properties[ 'path' ], $modx->PageSpeed->properties[ 'url' ] )
                                    ) || (
                                        in_array( $extension, \PageSpeed::EXTENSION_IMAGE_WEBP ) &&
                                        ( $filename = $modx->PageSpeed->filename( $value[ 'src' ] ) ) &&
                                        count( glob( sprintf( \PageSpeed::FORMAT_GLOB_PATTERN, pathinfo( $filename, PATHINFO_DIRNAME ) . DIRECTORY_SEPARATOR . pathinfo( $filename, PATHINFO_FILENAME ), implode( ',', array_merge( \PageSpeed::EXTENSION_IMAGE, \PageSpeed::EXTENSION_IMAGE_JPEG ) ) ), GLOB_BRACE ) ) === 1
                                    ) )
                                    try {
                                        if( $modx->PageSpeed->config[ 'Imagick' ] ) {
                                            if( $modx->PageSpeed->Imagick || $modx->PageSpeed->Imagick = new \Imagick() ) {
                                                $modx->PageSpeed->Imagick->clear();
                                                if( $modx->PageSpeed->Imagick->readImage( $filename ) ) {
                                                    $modx->PageSpeed->Imagick->resizeImage( $value[ 'width' ], $value[ 'height' ], $modx->PageSpeed->config[ 'imagick_filter' ], 0.9 );
                                                    $modx->PageSpeed->Imagick->writeImages( $filename, TRUE );
                                                } else
                                                    $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_method', [ 'name' => 'readImage' ] ) );
                                            } else
                                                $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => \Imagick::class ] ) );
                                        } else {
                                            if( is_resource( $image = imagecreatefromstring( file_get_contents( $filename ) ) ) || $image instanceof \GdImage ) {
                                                imagepalettetotruecolor( $image );
                                                imagealphablending( $image, FALSE );
                                                imagesavealpha( $image, TRUE );
                                                if( is_resource( $resource = imagescale( $image, $value[ 'width' ], $value[ 'height' ], $modx->PageSpeed->config[ 'gd_method' ] ) ) || $image instanceof \GdImage )
                                                    switch( $extension ) {
                                                        case 'gif' :
                                                            imagegif( $resource, $filename );
                                                            break;
                                                        case 'jpeg' :
                                                            imagejpeg( $resource, $filename, $modx->PageSpeed->properties[ 'quality' ] );
                                                            break;
                                                        case 'png' :
                                                            //imagepng( $resource, $filename, min( floor( $modx->PageSpeed->properties[ 'quality' ] / 10 ), 9 ) );
                                                            imagepng( $resource, $filename, 9 );
                                                            break;
                                                        case 'webp' :
                                                            imagewebp( $resource, $filename, $modx->PageSpeed->properties[ 'quality' ] );
                                                            break;
                                                    }
                                                else
                                                    $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_method', [ 'name' => 'imagescale' ] ) );
                                                if( is_resource( $image ) )
                                                    imagedestroy( $image );
                                            } else
                                                $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_method', [ 'name' => 'imagecreatefromstring' ] ) );
                                        }
                                    } catch( Exception $Exception ) {
                                        $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed', [ 'message' => $Exception->getMessage() ] ) );
                                    }
                                else
                                    $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_cache', [ 'filename' => $value[ 'src' ] ] ) );
                        }
                    }
                } else
                    $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_key', [ 'key' => $modx->PageSpeed->key ] ) );
            } else {
                $properties = [];
                foreach( $modx->PageSpeed->properties as $key => $value )
                    $properties[ $key ] = $modx->getOption( \PageSpeed::class . '_' . $key, NULL, $value );
                $modx->PageSpeed->configure( $properties );
                $modx->PageSpeed->properties[ 'WEBP' ] = ( $modx->PageSpeed->config[ 'GD' ] || $modx->PageSpeed->config[ 'Imagick' ] ) && (
                        isset( $_SERVER[ 'HTTP_ACCEPT' ] ) && preg_match( \PageSpeed::PCRE_HEADER_HTTP_ACCEPT_WEBP, $_SERVER[ 'HTTP_ACCEPT' ] ) ||
                        isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) && preg_match( \PageSpeed::PCRE_HEADER_HTTP_USER_AGENT_WEBP, $_SERVER[ 'HTTP_USER_AGENT' ] )
                    );
                $modx->PageSpeed->properties = array_merge( $modx->PageSpeed->properties, $modx->getOption( \PageSpeed::MODX_OPTION ) );
                $modx->PageSpeed->properties = array_merge( $modx->PageSpeed->properties, [
                    'format_url_convert' => $modx->PageSpeed->properties[ 'site_url' ] . '?' . $modx->event->name . '=' . \PageSpeed::class . '&key=%1$s&convert=%2$s',
                    'mime_db_filename' => $modx->PageSpeed->properties[ 'core_path' ] . 'components' . DIRECTORY_SEPARATOR . \PageSpeed::class . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mime-db.json',
                    'path' => $modx->PageSpeed->properties[ 'assets_path' ] . \PageSpeed::class . DIRECTORY_SEPARATOR,
                    'subresources_beacon' => [ 'script' => [
                        [ 'url' => $modx->PageSpeed->properties[ 'assets_url' ] . 'components/' . \PageSpeed::class . '/default.js' ]
                    ] ],
                    'subresources_ajaxform' => [ 'script' => [
                        [ 'url' => $modx->PageSpeed->properties[ 'assets_url' ] . 'components/ajaxform/js/lib/jquery.form.min.js' ],
                        [ 'url' => $modx->PageSpeed->properties[ 'assets_url' ] . 'components/ajaxform/js/lib/jquery.jgrowl.min.js' ]
                    ] ],
                    'upload_files' => explode( ',', $modx->PageSpeed->properties[ 'upload_files' ] ),
                    'url' => $modx->PageSpeed->properties[ 'url_scheme' ] . $modx->PageSpeed->properties[ 'http_host' ] . $modx->PageSpeed->properties[ 'assets_url' ] . \PageSpeed::class . '/',
                ] );
            }
        }
        break;
    case 'OnMODXInit' :
        //if( $modx->context->key !== 'mgr' && $_SERVER[ 'HTTP_USER_AGENT' ] !== \PageSpeed::class && ( $modx->getOption( 'site_status' ) || $modx->user->isMember( 'Administrator' ) ) ) {
        if( $_SERVER[ 'HTTP_USER_AGENT' ] !== \PageSpeed::class && ( $modx->getOption( 'site_status' ) || $modx->user->isMember( 'Administrator' ) ) ) {
            if( $modx->getService( \PageSpeed::class, \PageSpeed::class, ( $core_path = $modx->getOption( 'core_path' ) . 'components' . DIRECTORY_SEPARATOR . \PageSpeed::class . DIRECTORY_SEPARATOR ) . 'model' . DIRECTORY_SEPARATOR ) instanceof \PageSpeed ) {
                if( is_file( $filename = $core_path . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php' ) )
                    require_once $filename;
                else
                    $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_require_once', [ 'filename' => $filename ] ) );
            } else
                $modx->log( \xPDO::LOG_LEVEL_WARN, \PageSpeed::class );
        }
        break;
    case 'OnWebPagePrerender' :
        if( isset( $modx->PageSpeed ) && $modx->PageSpeed instanceof \PageSpeed ) {
            if( $modx->resource->id && $modx->resource->content_type === 1 && $modx->PageSpeed->properties[ 'enable' ] ) {
                if( isset( $_SERVER[ 'HTTP_ORIGIN' ] ) && isset( $_SERVER[ 'REQUEST_URI' ] ) )
                    $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_url', [
                        'url' => $_SERVER[ 'HTTP_ORIGIN' ] . $_SERVER[ 'REQUEST_URI' ], 'mode' => PHP_SAPI
                    ] ) );
                list( $modx->sjscripts, $modx->jscripts, $modx->loadedjscripts ) = $modx->PageSpeed->val;
                $modx->resource->_output = $modx->PageSpeed->parseHTML( $modx->resource->_output );
            }
        }
        break;
    default :
        //
        break;
}