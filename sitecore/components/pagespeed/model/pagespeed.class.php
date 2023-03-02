<?php
//
    class PageSpeed {
        const CACHE_HASH_ALGO = 'fnv164';
        const CACHE_LOG_INTERVAL = 10;
        /*
            2^16 - 1 (65,535) nodes per level
        */
        const CACHE_SPLIT_LENGTH = 4;
        const CACHE_SPLIT_LEVELS = 2;
        const CURLOPT = [
            CURLOPT_CONNECTTIMEOUT => \PageSpeed::SUBRESOURCES_HTTP_TIMEOUT,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_USERAGENT => \PageSpeed::class,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => \PageSpeed::SUBRESOURCES_HTTP_TIMEOUT
        ];
        const EXTENSION_IMAGE = [ 'gif', 'jpeg', 'png' ];
        const EXTENSION_IMAGE_JPEG = [ 'jfi', 'jfif', 'jif', 'jpe', 'jpeg', 'jpg' ];
        const EXTENSION_IMAGE_WEBP = [ 'webp' ];
        const EXTENSION_JAVASCRIPT = [ 'js' ];
        const EXTENSION_STYLESHEET = [ 'css' ];
        const FORMAT_CONFIG = 'var PageSpeed = { config : %1$s };';
        const FORMAT_DATA_URI_JAVASCRIPT = 'data:text/javascript,%1$s';
        const FORMAT_DATA_URI_STYLESHEET = 'data:text/css,%1$s';
        const FORMAT_GLOB_PATTERN = '%1$s.{%2$s}';
        const FORMAT_HTML_CHARSET = '<meta http-equiv="content-type" content="text/html; charset=%1$s" />';
        const FORMAT_HTTP_HEADER = '%1$s: %2$s';
        const FORMAT_HTTP_HEADER_PRELOAD = 'link: <%1$s>; rel=preload; as=%2$s; nopush';
        const FORMAT_HTTP_HEADER_PRELOAD_CROSSORIGIN = 'link: <%1$s>; rel=preload; as=%2$s; crossorigin=%3$s; nopush';
        const FORMAT_MICROTIME = '%2.4f s';
        const FORMAT_PCRE_START_OF_STRING = '/^%1$s/';
        const FORMAT_PROGRESS = '<strong>%1$3d%%</strong>';
        const FORMAT_SUBRESOURCES_MEDIA = '@media %1$s { %2$s }';
        const FORMAT_SUBRESOURCES_RULESET = \PageSpeed::class . ' { %1$s }';
        const FORMAT_MODX_TIMING_TAG = '[^%1$s^]';
        const FORMAT_URL_CDNJS_API = 'https://api.cdnjs.com/libraries/%1$s?fields=version,filename';
        const FORMAT_URL_CDNJS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/%1$s/%2$s/%3$s';
        const HTML_ATTRIBUTE_LINK = [ 'crossorigin', 'integrity', 'media' ];
        const HTML_ATTRIBUTE_SCRIPT = [ 'async', 'crossorigin', 'defer', 'integrity', 'nomodule' ];
        const HTML_ATTRIBUTE_VALUE_AS = [ 'font', 'image', 'script', 'style' ];
        const HTML_ATTRIBUTE_VALUE_AS_EXTENSION = [ 'script', 'style' ];
        const HTML_HEAD_TAG = [ 'base', 'link', 'meta', 'noscript', 'style', 'script', 'title' ];
        const HTTP_TIMEOUT = 40;
        const MODX_OPTION = [ 'assets_path', 'assets_url', 'base_path', 'core_path', 'http_host', 'site_url', 'upload_files', 'url_scheme' ];
        const MODX_TIMING_TAG = [ 'qt', 'q', 'p', 't', 's', 'm' ];
        const PCRE_DELIMITER = '/';
        const PCRE_HEADER_HTTP_ACCEPT_WEBP = '/\bimage\/webp\b/i';
        const PCRE_HEADER_HTTP_USER_AGENT_WEBP = '/\bChrome-Lighthouse\b/i';
        const PCRE_HTML_CHARSET = '/\<meta(?!\s*(?:name|value)\s*\=)(?:[^\>]*?content\s*\=[\s"\']*)?(?:[^\>]*?)[\s"\';]*charset\s*\=[\s"\']*(?P<charset>[^\s"\'\/\>]*)[^\>]*?\>/i';
        const PCRE_HTML_DOCTYPE = '/^(\<\!DOCTYPE[^\>]+\>)\s*/';
        const PCRE_HTML_ENTITY = '/&\#[0-9]+;/';
        const PCRE_HTML_WHITESPACE = '/\s+/';
        const PCRE_HTML_WHITESPACE_PRESERVE = '/\/(code|kbd|pre|samp|script|style|textarea|var)/';
        const PCRE_MODX_CONFIG_PROPERTY = '/\{(?P<key>[^\}]+?)\}/';
        const PCRE_SUBRESOURCES_AJAXFORM = '/AjaxForm\.initialize/';
        /*
            /^\/\*(.(?!\/\*))*?\*\//s
        */
        const PCRE_SUBRESOURCES_COMMENT_LEADING = '/^(\/\*.*?\*\/\s*)+/s';
        const PCRE_SUBRESOURCES_JSCRIPTS = '/(AjaxForm|Looked)\.initialize/';
        const PCRE_SUBRESOURCES_SJSCRIPTS = '/(mse2|mse2Form|Office|PromoDs)Config/';
        const PCRE_SUBRESOURCES_SOURCE_MAPPING = '/^\/[\/\*]\# sourceMappingURL\=.*$/im';
        const PCRE_URL_BASE = [ '/^\/\//', '/^\//' ];
        const PCRE_URL_DATA = '/^data:(?P<mediatype>(?:\w+\/(?:(?!;).)+)?)((?:;[\w\W]*?[^;])*),(.*)$/';
        const PCRE_URL_GOOGLE_FONTS = '/^https?:\/\/fonts\.googleapis\.com\/css/';
        /*
            /.(?:(?R)|.?)./
        */
        const PCRE_URL_SLASH = [ '/(?<!:)\/{2,}/', '/\/\.\//', '/(?<!^)[^\/]+\/(?R)?\.\.\//' ];
        const PCRE_URL_SLASH_LEADING = '/^\//';
        const PROPERTIES_BUNDLE = [ 'link', 'script' ];
        const PROPERTIES_CACHE_CONVERT = [ 'persistent', 'static' ];
        const PROPERTIES_CACHE_IMAGE = [ 'WEBP', 'convert', 'quality' ];
        const PROPERTIES_CACHE_STYLESHEET = [ 'WEBP', 'convert', 'display', 'quality' ];
        const PROPERTIES_CONVERT = [ 'disable', 'dynamic', 'persistent', 'static' ];
        const PROPERTIES_CROSSORIGIN = [ '', 'anonymous', 'use-credentials' ];
        const PROPERTIES_DISPLAY = [ 'auto', 'block', 'fallback', 'optional', 'swap' ];
        const PROPERTIES_INTEGRITY = [ 'sha256', 'sha384', 'sha512' ];
        const PROPERTIES_LOADING = [ 'auto', 'eager', 'lazy' ];
        const PROPERTIES_MINIFY = [ 'css', 'html', 'js', 'json', 'link', 'script' ];
        const PROPERTIES_SCRIPT = [ '', 'async', 'defer' ];
        const SUBRESOURCES_TAG = [ 'link', 'script' ];
        const SUBRESOURCES_HTTP_TIMEOUT = 10;
        /*
            https://google-webfonts-helper.herokuapp.com/fonts/exo-2?subsets=latin,latin-ext,cyrillic
            https://github.com/majodev/google-webfonts-helper
            https://github.com/majodev/google-webfonts-helper/blob/master/server/logic/conf.js
        */
        const USERAGENT_GOOGLE_FONTS = [
            //'embedded-opentype' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)',
            'woff' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0',
            'woff2' => 'Mozilla/5.0 (Windows NT 6.3; rv:39.0) Gecko/20100101 Firefox/39.0',
            //'svg' => 'Mozilla/4.0 (iPad; CPU OS 4_0_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/4.1 Mobile/9A405 Safari/7534.48.3',
            //'truetype' => 'Mozilla/5.0 (Unknown; Linux x86_64) AppleWebKit/538.1 (KHTML, like Gecko) Safari/538.1 Daum/4.1'
        ];
        const XPATH_COMMENT = '
            // comment()
        ';
        const XPATH_CSS = '
            // style [
                (
                    not( @type ) or
                    ( php:functionString( "strtolower", @type ) = "text/css" ) or
                    ( php:functionString( "strtolower", @type ) = "application/x-pointplus" )
                ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] |
            // @style [
                count( preceding::comment() [
                    php:functionString( "strtolower", string() ) = "ignore"
                ] ) <= count( preceding::comment() [
                    php:functionString( "strtolower", string() ) = "/ignore"
                ] )
            ]
        ';
        const XPATH_IMPORT = '
            // link [
                ( php:functionString( "strtolower", @rel ) = "stylesheet" ) and
                ( @href ) and (
                    not( @type ) or
                    ( php:functionString( "strtolower", @type ) = "text/css" )
                ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] |
            // style [
                (
                    not( @type ) or
                    ( php:functionString( "strtolower", @type ) = "text/css" ) or
                    ( php:functionString( "strtolower", @type ) = "application/x-pointplus" )
                ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] |
            // script [
                (
                    not( @type ) or
                    ( php:functionString( "strtolower", @type ) = "application/x-javascript" ) or
                    ( php:functionString( "strtolower", @type ) = "application/javascript" ) or
                    ( php:functionString( "strtolower", @type ) = "application/ecmascript" ) or
                    ( php:functionString( "strtolower", @type ) = "text/javascript" ) or
                    ( php:functionString( "strtolower", @type ) = "text/ecmascript" )
                ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ]
        ';
        const XPATH_JS = '
            // script [
                not( @src ) and (
                    not( @type ) or
                    ( php:functionString( "strtolower", @type ) = "application/x-javascript" ) or
                    ( php:functionString( "strtolower", @type ) = "application/javascript" ) or
                    ( php:functionString( "strtolower", @type ) = "application/ecmascript" ) or
                    ( php:functionString( "strtolower", @type ) = "text/javascript" ) or
                    ( php:functionString( "strtolower", @type ) = "text/ecmascript" )
                ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ]
        ';
        const XPATH_JSON = '
            // script [
                (
                    ( php:functionString( "strtolower", @type ) = "application/json" ) or
                    ( php:functionString( "strtolower", @type ) = "application/ld+json" )
                ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ]
        ';
        /*
            https://developer.mozilla.org/en-US/docs/Web/HTML/Preloading_content
        */
        const XPATH_LINK = '
            // link [
                (
                    ( php:functionString( "strtolower", @rel ) = "prefetch" ) or
                    ( php:functionString( "strtolower", @rel ) = "preload" )
                ) and
                ( @href ) and
                ( @as ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ]
        ';
        const XPATH_LOADING = '
            // img [
                not( @loading ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] |
            // iframe [
                not( @loading ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ]
        ';
        const XPATH_META_HTTP_EQUIV = '
            // meta [
                ( @http-equiv ) and
                ( @content ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ]
        ';
        const XPATH_SRC = '
            // img [
                ( @src ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] / @src |
            // * [
                ( @data-src ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] / @data-src
        ';
        const XPATH_SRCSET = '
            // img [
                ( @srcset ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] / @srcset |
            // picture / source [
                ( @srcset ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] / @srcset |
            // * [
                ( @data-srcset ) and (
                    count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "ignore"
                    ] ) <= count( preceding::comment() [
                        php:functionString( "strtolower", string() ) = "/ignore"
                    ] )
                )
            ] / @data-srcset
        ';
        const XPATH_TEXT = '
            // text() [
                count( preceding::comment() [
                    php:functionString( "strtolower", string() ) = "ignore"
                ] ) <= count( preceding::comment() [
                    php:functionString( "strtolower", string() ) = "/ignore"
                ] )
            ]
        ';
        public $DOMDocument;
        public $DOMXPath;
        public $Repository;
        public $Imagick;
        public $body;
        public $ch;
        public $config;
        public $context;
        public $properties = [
            'bundle' => [ 'link', 'script' ],
            'convert' => 'static',
            'critical' => TRUE,
            'crossorigin' => 'anonymous',
            'display' => 'swap',
            'enable' => TRUE,
            'integrity' => [ 'sha256' ],
            'lifetime' => 604800,
            'loading' => 'lazy',
            'minify' => [ 'html', 'link', 'script' ],
            'path' => MODX_ASSETS_PATH . \PageSpeed::class . DIRECTORY_SEPARATOR,
            'quality' => 80,
            'resize' => TRUE,
            'script' => 'defer',
            'subresources' => [],
            'url' => MODX_URL_SCHEME . MODX_HTTP_HOST . MODX_ASSETS_URL . \PageSpeed::class . '/'
        ];
        public $head;
        public $jscripts = [];
        public $key;
        public $microtime;
        public $modx;
        public $options = [ \xPDO::OPT_CACHE_KEY => \PageSpeed::class ];
        public $resource;
        public $sjscripts = [];
        public $val;
        public function __construct( modX &$modx ) {
            $this->modx = &$modx;
            $this->modx->lexicon->load( \PageSpeed::class . ':default' );
            $this->config = [
                'GD' => extension_loaded( 'GD' ) && gd_info()[ 'WebP Support' ],
                'Imagick' => extension_loaded( 'Imagick' ) && in_array( 'WEBP', \Imagick::queryFormats() ),
                'cURL' => extension_loaded( 'cURL' ),
                'sysvsem' => extension_loaded( 'sysvsem' )
            ];
            /*
                \Imagick::FILTER_UNDEFINED
                \Imagick::FILTER_POINT
                \Imagick::FILTER_BOX
                \Imagick::FILTER_TRIANGLE
                \Imagick::FILTER_HERMITE
                \Imagick::FILTER_HANNING
                \Imagick::FILTER_HAMMING
                \Imagick::FILTER_BLACKMAN
                \Imagick::FILTER_GAUSSIAN
                \Imagick::FILTER_QUADRATIC
                \Imagick::FILTER_CUBIC
                \Imagick::FILTER_CATROM
                \Imagick::FILTER_MITCHELL
                \Imagick::FILTER_LANCZOS
                \Imagick::FILTER_BESSEL
                \Imagick::FILTER_SINC
            */
            if( $this->config[ 'Imagick' ] )
                $this->config[ 'imagick_filter' ] = \Imagick::FILTER_LANCZOS;
            /*
                IMG_BICUBIC
                IMG_BICUBIC_FIXED
                IMG_BILINEAR_FIXED
                IMG_NEAREST_NEIGHBOUR
            */
            if( $this->config[ 'GD' ] )
                $this->config[ 'gd_method' ] = IMG_BILINEAR_FIXED;
            $this->config[ 'use_errors' ] = libxml_use_internal_errors( TRUE );
        }
        public function __destruct() {
            if( is_resource( $this->ch ) )
                curl_close( $this->ch );
            if( $this->Imagick )
                $this->Imagick->clear();
            libxml_use_internal_errors( $this->config[ 'use_errors' ] );
        }
        public function block() {
            if( $this->config[ 'sysvsem' ] ) {
                $this->resource = sem_get( ftok( __FILE__, chr( 0 ) ) );
                sem_acquire( $this->resource );
            }
            register_tick_function( function() {
                if( microtime( TRUE ) - $this->modx->startTime > \PageSpeed::HTTP_TIMEOUT )
                    exit( empty( $this->modx->resource ) ? json_encode( [
                        'success' => TRUE, 'message' => 'refresh', 'total' => 0, 'data' => [], 'object' => []
                    ], JSON_FORCE_OBJECT ) : '<meta http-equiv="refresh" content="0" />' );
            } );
        }
        public function cache( string $filename, string $data ) {
            if( is_dir( $directory = dirname( $filename ) ) || @mkdir( $directory, 0777, TRUE ) ) {
                if( is_resource( $fp = fopen( $filename, 'w' ) ) )
                    return flock( $fp, LOCK_EX ) && fwrite( $fp, $data ) && flock( $fp, LOCK_UN ) && fclose( $fp );
                else
                    $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_fopen', [ 'filename' => $filename ] ) );
            } else
                $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_mkdir', [ 'directory' => $directory ] ) );
        }
        public function configure( array $properties ) {
            if( isset( $properties[ 'bundle' ] ) ) {
                $properties[ 'bundle' ] = array_unique( array_filter( array_map( 'strtolower', is_array( $properties[ 'bundle' ] ) ? $properties[ 'bundle' ] : explode( ' ', $properties[ 'bundle' ] ) ), function( $val ) {
                    return in_array( $val, \PageSpeed::PROPERTIES_BUNDLE );
                } ) );
                sort( $properties[ 'bundle' ] );
                $this->properties[ 'bundle' ] = $properties[ 'bundle' ];
            }
            if( isset( $properties[ 'convert' ] ) ) {
                $properties[ 'convert' ] = strtolower( $properties[ 'convert' ] );
                if( in_array( $properties[ 'convert' ], \PageSpeed::PROPERTIES_CONVERT ) )
                    $this->properties[ 'convert' ] = $properties[ 'convert' ];
            }
            if( isset( $properties[ 'critical' ] ) )
                $this->properties[ 'critical' ] = filter_var( $properties[ 'critical' ], FILTER_VALIDATE_BOOLEAN );
            if( isset( $properties[ 'crossorigin' ] ) ) {
                $properties[ 'crossorigin' ] = strtolower( $properties[ 'crossorigin' ] );
                if( in_array( $properties[ 'crossorigin' ], \PageSpeed::PROPERTIES_CROSSORIGIN ) )
                    $this->properties[ 'crossorigin' ] = $properties[ 'crossorigin' ];
            }
            if( isset( $properties[ 'display' ] ) ) {
                $properties[ 'display' ] = strtolower( $properties[ 'display' ] );
                if( in_array( $properties[ 'display' ], \PageSpeed::PROPERTIES_DISPLAY ) )
                    $this->properties[ 'display' ] = $properties[ 'display' ];
            }
            if( isset( $properties[ 'enable' ] ) )
                $this->properties[ 'enable' ] = filter_var( $properties[ 'enable' ], FILTER_VALIDATE_BOOLEAN );
            if( isset( $properties[ 'integrity' ] ) ) {
                $properties[ 'integrity' ] = array_unique( array_filter( array_map( 'strtolower', is_array( $properties[ 'integrity' ] ) ? $properties[ 'integrity' ] : explode( ' ', $properties[ 'integrity' ] ) ), function( $val ) {
                    return in_array( $val, \PageSpeed::PROPERTIES_INTEGRITY );
                } ) );
                sort( $properties[ 'integrity' ] );
                $this->properties[ 'integrity' ] = $properties[ 'integrity' ];
            }
            if( isset( $properties[ 'lifetime' ] ) )
                $this->properties[ 'lifetime' ] = intval( $properties[ 'lifetime' ] );
            if( isset( $properties[ 'loading' ] ) ) {
                $properties[ 'loading' ] = strtolower( $properties[ 'loading' ] );
                if( in_array( $properties[ 'loading' ], \PageSpeed::PROPERTIES_LOADING ) )
                    $this->properties[ 'loading' ] = $properties[ 'loading' ];
            }
            if( isset( $properties[ 'minify' ] ) ) {
                $properties[ 'minify' ] = array_unique( array_filter( array_map( 'strtolower', is_array( $properties[ 'minify' ] ) ? $properties[ 'minify' ] : explode( ' ', $properties[ 'minify' ] ) ), function( $val ) {
                    return in_array( $val, \PageSpeed::PROPERTIES_MINIFY );
                } ) );
                sort( $properties[ 'minify' ] );
                $this->properties[ 'minify' ] = $properties[ 'minify' ];
            }
            if( isset( $properties[ 'path' ] ) )
                $this->properties[ 'path' ] = $this->replacePlaceholders( \PageSpeed::PCRE_MODX_CONFIG_PROPERTY, $this->modx->config, $properties[ 'path' ] );
            if( isset( $properties[ 'quality' ] ) )
                $this->properties[ 'quality' ] = max( 0, min( 100, intval( $properties[ 'quality' ] ) ) );
            if( isset( $properties[ 'resize' ] ) )
                $this->properties[ 'resize' ] = filter_var( $properties[ 'resize' ], FILTER_VALIDATE_BOOLEAN );
            if( isset( $properties[ 'script' ] ) ) {
                $properties[ 'script' ] = strtolower( $properties[ 'script' ] );
                if( in_array( $properties[ 'script' ], \PageSpeed::PROPERTIES_SCRIPT ) )
                    $this->properties[ 'script' ] = $properties[ 'script' ];
            }
            if( isset( $properties[ 'subresources' ] ) ) {
                if( is_string( $properties[ 'subresources' ] ) ) {
                    $properties[ 'subresources' ] = json_decode( $properties[ 'subresources' ], TRUE );
                    if( json_last_error() )
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_json_last_error_msg', [ 'message' => json_last_error_msg() ] ) );
                }
                if( is_array( $properties[ 'subresources' ] ) )
                    $this->properties[ 'subresources' ] = $properties[ 'subresources' ];
            }
            if( isset( $properties[ 'url' ] ) )
                $this->properties[ 'url' ] = $this->replacePlaceholders( \PageSpeed::PCRE_MODX_CONFIG_PROPERTY, $this->modx->config, $properties[ 'url' ] );
        }
        public function convert( $data ) {
            try {
                if( $this->config[ 'Imagick' ] ) {
                    if( $this->Imagick || $this->Imagick = new \Imagick() ) {
                        $this->Imagick->clear();
                        if( $this->Imagick->readImageBlob( $data ) ) {
                            $this->Imagick->stripImage();
                            $this->Imagick->setImageFormat( 'webp' );
                            $this->Imagick->setCompressionQuality( $this->properties[ 'quality' ] );
                            return $this->Imagick->getImagesBlob();
                        } else
                            $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_method', [ 'name' => 'readImageBlob' ] ) );
                    } else
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => \Imagick::class ] ) );
                } else {
                    if( is_resource( $image = imagecreatefromstring( $data ) ) || $image instanceof \GdImage ) {
                        imagepalettetotruecolor( $image );
                        imagealphablending( $image, FALSE );
                        imagesavealpha( $image, TRUE );
                        ob_start();
                        if( imagewebp( $image, NULL, $this->properties[ 'quality' ] ) )
                            return ob_get_clean();
                        else {
                            $modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_method', [ 'name' => 'imagewebp' ] ) );
                            ob_end_clean();
                        }
                        if( is_resource( $image ) )
                            imagedestroy( $image );
                    } else
                        //$modx->log( \xPDO::LOG_LEVEL_WARN, $modx->lexicon( 'message_PageSpeed_method', [ 'name' => 'imagecreatefromstring' ] ) );
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => \GdImage::class ] ) );
                }
            } catch( Exception $Exception ) {
                $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed', [ 'message' => $Exception->getMessage() ] ) );
            }
        }
        public function createElement( DOMNode $parentNode, string $name, string $value = NULL, array $attributes = [] ) {
    		if( $DOMElement = $this->DOMDocument->createElement( $name, $value ) ) {
    		    foreach( $attributes as $name => $value )
                    $DOMElement->setAttribute( $name, $value );
                $parentNode->appendChild( $DOMElement );
    		} else
        		$this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => \DOMElement::class ] ) );
        }
        public function data( string $url ) {
            if( parse_url( $url, PHP_URL_SCHEME ) === 'data' )
                return file_get_contents( strtok( $url, '#' ) );
            if( $filename = $this->filename( $url ) )
                return file_get_contents( $filename );
            if( $this->config[ 'cURL' ] ) {
                //if( is_resource( $this->ch ) || $this->ch instanceof \CurlHandle || ( ( is_resource( $this->ch = curl_init() ) || $this->ch instanceof \CurlHandle ) && curl_setopt_array( $this->ch, \PageSpeed::CURLOPT ) ) ) {
                if( $this->ch || ( $this->ch = curl_init() ) && curl_setopt_array( $this->ch, \PageSpeed::CURLOPT ) ) {
                    curl_setopt( $this->ch, CURLOPT_URL, $url );
                    $data = curl_exec( $this->ch );
                    if( curl_errno( $this->ch ) )
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_curl_error', [ 'message' => curl_error( $this->ch ), 'url' => $url ] ) );
                    else {
                        if( in_array( $code = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE ), [ 200, 301, 302, 303, 304, 307, 308 ] ) )
                            return $data;
                        else
                            $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_HTTP', [ 'code' => $code, 'url' => $url ] ) );
                    }
                } else
                    $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => \CurlHandle::class ] ) );
            } else {
                if( is_resource( $this->context ) || $this->stream_context_create( \PageSpeed::class ) ) {
                    set_error_handler( [ $this, 'error_handler' ] );
                    try {
                        return file_get_contents( $url, FALSE, $this->context );
                    } catch( Exception $Exception ) {
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed', [ 'message' => $Exception->getMessage() ] ) );
                    } finally {
                        restore_error_handler();
                    }
                } else
                    $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => 'stream_context' ] ) );
            }
        }
        public function error_handler( $errno, string $errstr, string $errfile, int $errline ) {
            throw new \Exception( $errstr, $errno );
        }
        public function extension( string $url ) {
            if( parse_url( $url, PHP_URL_SCHEME ) === 'data' ) {
                if( preg_match( \PageSpeed::PCRE_URL_DATA, strtok( $url, '#' ), $matches ) ) {
                    if( $this->Repository || $this->Repository = new \MimeTyper\Repository\ExtendedRepository( [
                        new \MimeTyper\Repository\MimeDbRepository( $this->properties[ 'mime_db_filename' ] )
                    ] ) )
                        return $this->Repository->findExtension( $matches[ 'mediatype' ] );
                    else
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => MimeTyper\Repository\ExtendedRepository::class ] ) );
                }
            } else
                return in_array( $extension = strtolower( pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) ), \PageSpeed::EXTENSION_IMAGE_JPEG ) ? 'jpeg' : $extension;
        }
        public function filename( string $url, string $base_path = NULL, string $base_url = NULL ) {
            if( parse_url( $url, PHP_URL_SCHEME ) === 'data' )
                return;
            $base_path = $base_path ?? $this->properties[ 'base_path' ];
            $base_url = $base_url ?? $this->properties[ 'site_url' ];
            if( preg_match(
                sprintf( \PageSpeed::FORMAT_PCRE_START_OF_STRING, preg_quote( $base_url, \PageSpeed::PCRE_DELIMITER ) ), $url
            ) && is_file( $filename = str_replace( '/', DIRECTORY_SEPARATOR, preg_replace(
                sprintf( \PageSpeed::FORMAT_PCRE_START_OF_STRING, preg_quote( $base_url, \PageSpeed::PCRE_DELIMITER ) ), $base_path, strtok( $url, '?' ) ) )
            ) )
                return $filename;
        }
        public function getAllURLs( \Sabberworm\CSS\CSSList\CSSBlockList $CSSBlockList ) {
            $value = [];
            foreach( $CSSBlockList->getAllValues() as $Value )
                if( $Value instanceof \Sabberworm\CSS\Value\URL )
                    $value[] = $Value->getURL()->getString();
            return $value;
        }
        public function importDOMDocument( DOMNode $parentNode, DOMDocument $DOMDocument ) {
            foreach( $DOMDocument->documentElement->childNodes as $DOMNode )
                foreach( $DOMNode->childNodes as $DOMNode )
                    //$parentNode->appendChild( $this->DOMDocument->importNode( $DOMNode, TRUE ) );
                    if( $parentNode === $this->head && ! in_array( $DOMNode->nodeName, \PageSpeed::HTML_HEAD_TAG ) )
                        $this->body->insertBefore( $this->DOMDocument->importNode( $DOMNode, TRUE ), $this->body->firstChild );
                    else
                        $parentNode->appendChild( $this->DOMDocument->importNode( $DOMNode, TRUE ) );
        }
        public function importDOMXPath( DOMXPath $DOMXPath ) {
            foreach( $DOMXPath->query( \PageSpeed::XPATH_IMPORT ) as $DOMNode ) {
                switch( $DOMNode->nodeName ) {
                    case 'link' :
                        $value = [ 'url' => $DOMNode->getAttribute( 'href' ) ];
                        foreach( \PageSpeed::HTML_ATTRIBUTE_LINK as $name )
                            if( $DOMNode->hasAttribute( $name ) )
                                $value[ $name ] = $DOMNode->getAttribute( $name );
                        $this->properties[ 'subresources' ] = array_merge_recursive( $this->properties[ 'subresources' ], [ 'link' => [ $value ] ] );
                    break;
                    case 'style' :
                        $this->properties[ 'subresources' ] = array_merge_recursive( $this->properties[ 'subresources' ], [ 'link' => [
                            [ 'url' => sprintf( \PageSpeed::FORMAT_DATA_URI_STYLESHEET, rawurlencode( $DOMNode->nodeValue ) ) ]
                        ] ] );
                    break;
                    case 'script' :
                        if( $DOMNode->hasAttribute( 'src' ) ) {
                            $value = [ 'url' => $DOMNode->getAttribute( 'src' ) ];
                            foreach( \PageSpeed::HTML_ATTRIBUTE_SCRIPT as $name )
                                if( $DOMNode->hasAttribute( $name ) )
                                    $value[ $name ] = $DOMNode->getAttribute( $name );
                            $this->properties[ 'subresources' ] = array_merge_recursive( $this->properties[ 'subresources' ], [ 'script' => [ $value ] ] );
                        } else {
                            if( preg_match( \PageSpeed::PCRE_SUBRESOURCES_SJSCRIPTS, $DOMNode->nodeValue ) ) {
                                $value = [ 'src' => sprintf( \PageSpeed::FORMAT_DATA_URI_JAVASCRIPT, rawurlencode( $DOMNode->nodeValue ) ) ];
                                if( $this->properties[ 'crossorigin' ] )
                                    $value[ 'crossorigin' ] = $this->properties[ 'crossorigin' ];
                                if( $this->properties[ 'integrity' ] )
                                    $value[ 'integrity' ] = $this->integrity( $DOMNode->nodeValue );
                                if( $this->properties[ 'script' ] )
                                    $value[ $this->properties[ 'script' ] ] = TRUE;
                                $this->sjscripts[] = $value;
                            } elseif( preg_match( \PageSpeed::PCRE_SUBRESOURCES_JSCRIPTS, $DOMNode->nodeValue ) ) {
                                if( preg_match( \PageSpeed::PCRE_SUBRESOURCES_AJAXFORM, $DOMNode->nodeValue ) )
                                    $this->properties[ 'subresources' ] = array_merge_recursive( $this->properties[ 'subresources' ], $this->properties[ 'subresources_ajaxform' ] );
                                $value = [ 'src' => sprintf( \PageSpeed::FORMAT_DATA_URI_JAVASCRIPT, rawurlencode( $DOMNode->nodeValue ) ) ];
                                if( $this->properties[ 'crossorigin' ] )
                                    $value[ 'crossorigin' ] = $this->properties[ 'crossorigin' ];
                                if( $this->properties[ 'integrity' ] )
                                    $value[ 'integrity' ] = $this->integrity( $DOMNode->nodeValue );
                                if( $this->properties[ 'script' ] )
                                    $value[ $this->properties[ 'script' ] ] = TRUE;
                                $this->jscripts[] = $value;
                            } else
                                $this->properties[ 'subresources' ] = array_merge_recursive( $this->properties[ 'subresources' ], [ 'script' => [
                                    [ 'url' => sprintf( \PageSpeed::FORMAT_DATA_URI_JAVASCRIPT, rawurlencode( $DOMNode->nodeValue ) ) ]
                                ] ] );
                        }
                    break;
                }
                $DOMNode->parentNode->removeChild( $DOMNode );
            }
        }
        public function integrity( $data, $integrity = NULL ) {
            if( isset( $integrity ) ) {
                foreach( explode( ' ', $integrity ) as $integrity ) {
                    list( $algo, $hash ) = explode( '-', $integrity );
                    if( in_array( $algo, \PageSpeed::PROPERTIES_INTEGRITY ) && base64_encode( hash( $algo, $data, TRUE ) ) === $hash )
                        return TRUE;
                }
                return FALSE;
            } else {
                $integrity = [];
                foreach( $this->properties[ 'integrity' ] as $algo )
                    $integrity[] = $algo . '-' . base64_encode( hash( $algo, $data, TRUE ) );
                return implode( ' ', $integrity );
            }
        }
        public function loadHTML( string $source, int $options = 0 ) {
            if( strlen( $source ) ) {
                if( $DOMDocument = new \DOMDocument() ) {
                    $DOMDocument->loadHTML( $source, $options );
                    /*
                        LibXMLError Object
                        (
                            [level] => 0
                            [code] => 801
                            [column] => 0
                            [message] => Tag name invalid
    
                            [file] => 
                            [line] => 0
                        )
                    */
                    foreach( libxml_get_errors() as $value )
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_libxml_get_errors', get_object_vars( $value ) ) );
                    libxml_clear_errors();
                    if( $DOMXPath = new \DOMXPath( $DOMDocument ) ) {
                        $DOMXPath->registerNamespace( 'php', 'http://php.net/xpath' );
                        $DOMXPath->registerPhpFunctions();
                        return [ $DOMDocument, $DOMXPath ];
                    } else
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => \DOMXPath::class ] ) );
                } else
                    $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => \DOMDocument::class ] ) );
            }
        }
        public function parseCSSBlockList( \Sabberworm\CSS\CSSList\CSSBlockList $CSSBlockList, string $base_url ) {
            foreach( $CSSBlockList->getContents() as $CSSBlock ) {
                if( $CSSBlock instanceof \Sabberworm\CSS\Property\Import ) {
                    $atRuleArgs = $CSSBlock->atRuleArgs();
                    if( strtok( $base_url, '#' ) === strtok( $url = $this->url( $atRuleArgs[ 0 ]->getUrl()->getString(), $base_url ), '#' ) )
                        $CSSBlockList->replace( $CSSBlock, [] );
                    else {
                        $Document = ( new \Sabberworm\CSS\Parser( $this->subresource( $url, TRUE, 'css' ) ) )->parse();
                        if( isset( $atRuleArgs[ 1 ] ) ) {
                            $AtRuleBlockList = new \Sabberworm\CSS\CSSList\AtRuleBlockList( 'media', $atRuleArgs[ 1 ] );
                            $AtRuleBlockList->setContents( $Document->getContents() );
                            $CSSBlockList->replace( $CSSBlock, [ $AtRuleBlockList ] );
                        } else
                            $CSSBlockList->replace( $CSSBlock, $Document->getContents() );
                    }
                }
                if( $CSSBlock instanceof \Sabberworm\CSS\CSSList\CSSBlockList )
                    $this->parseCSSBlockList( $CSSBlock, $base_url );
                if( in_array( 'link', $this->properties[ 'minify' ] ) && (
                    $CSSBlock instanceof \Sabberworm\CSS\CSSList\CSSBlockList && empty( $CSSBlock->getContents() ) ||
                    $CSSBlock instanceof \Sabberworm\CSS\RuleSet\DeclarationBlock && empty( $CSSBlock->getRules() )
                ) )
                    $CSSBlockList->remove( $CSSBlock );
            }
        }
        public function parseHTML( string $source, int $options = 0 ) {
            if( strlen( $source ) ) {
                $this->microtime = microtime( TRUE );
                list( $this->DOMDocument, $this->DOMXPath ) = $this->loadHTML( preg_replace_callback( \PageSpeed::PCRE_HTML_CHARSET, function( $matches ) {
                    return sprintf( \PageSpeed::FORMAT_HTML_CHARSET, strtoupper( $matches[ 'charset' ] ) );
                }, $source ), $options );
                if( $this->DOMDocument instanceof \DOMDocument && $this->DOMXPath instanceof \DOMXPath ) {
                    $DOMNodeList = $this->DOMDocument->getElementsByTagName( 'head' );
                    if( $DOMNodeList->length === 0 )
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_tag', [ 'name' => 'head' ] ) );
                    else {
                        $this->head = $DOMNodeList->item( 0 );
                        $DOMNodeList = $this->DOMDocument->getElementsByTagName( 'body' );
                        if( $DOMNodeList->length === 0 )
                            $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_tag', [ 'name' => 'body' ] ) );
                        else {
                            $this->body = $DOMNodeList->item( 0 );
                            if( empty( $this->properties[ 'subresources' ] ) ) {
                                list( $DOMDocument, $DOMXPath ) = $this->loadHTML( implode( $this->modx->sjscripts ) );
                                if( $DOMDocument instanceof \DOMDocument && $DOMXPath instanceof \DOMXPath )
                                    $this->importDOMDocument( $this->head, $DOMDocument );
                                list( $DOMDocument, $DOMXPath ) = $this->loadHTML( implode( $this->modx->jscripts ) );
                                if( $DOMDocument instanceof \DOMDocument && $DOMXPath instanceof \DOMXPath )
                                    $this->importDOMDocument( $this->body, $DOMDocument );
                                $this->importDOMXPath( $this->DOMXPath );
                            } else {
                                list( $DOMDocument, $DOMXPath ) = $this->loadHTML( implode( $this->modx->sjscripts ) );
                                if( $DOMDocument instanceof \DOMDocument && $DOMXPath instanceof \DOMXPath ) {
                                    $this->importDOMXPath( $DOMXPath );
                                    $this->importDOMDocument( $this->head, $DOMDocument );
                                }
                                list( $DOMDocument, $DOMXPath ) = $this->loadHTML( implode( $this->modx->jscripts ) );
                                if( $DOMDocument instanceof \DOMDocument && $DOMXPath instanceof \DOMXPath ) {
                                    $this->importDOMXPath( $DOMXPath );
                                    $this->importDOMDocument( $this->body, $DOMDocument );
                                }
                            }
                            foreach( $this->DOMXPath->query( \PageSpeed::XPATH_META_HTTP_EQUIV ) as $DOMNode )
                                header( sprintf( \PageSpeed::FORMAT_HTTP_HEADER, $DOMNode->getAttribute( 'http-equiv' ), $DOMNode->getAttribute( 'content' ) ) );
                            $this->properties[ 'subresources' ] = array_merge_recursive( $this->properties[ 'subresources' ], $this->properties[ 'subresources_beacon' ] );
                            if( ! $this->val = $this->modx->cacheManager->get( $this->key = hash( \PageSpeed::CACHE_HASH_ALGO, serialize( $this->properties ) ), $this->options ) ) {
                                $this->val = [ 'link' => [], 'properties' => $this->properties, 'script' => [] ];
                                $this->block();
                                declare( ticks = 1 ) {
                                    foreach( $this->properties[ 'subresources' ] as $tag => $subresources )
                                        if( in_array( $tag, \PageSpeed::SUBRESOURCES_TAG ) && is_array( $subresources ) ) {
                                            $extension = $tag === 'link' ? 'css' : 'js';
                                            foreach( $subresources as $value ) {
                                                if( is_array( $value ) ) {
                                                    if( isset( $value[ 'name' ] ) ) {
                                                        if( ! ( isset( $value[ 'version' ] ) && isset( $value[ 'filename' ] ) ) ) {
                                                            $data = json_decode( $this->data( $url = sprintf( \PageSpeed::FORMAT_URL_CDNJS_API, $value[ 'name' ] ) ), TRUE );
                                                            if( json_last_error() )
                                                                $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_json_last_error_msg', [ 'message' => json_last_error_msg() ] ) );
                                                            else
                                                                $value = array_merge( $data, $value );
                                                        }
                                                        if( isset( $value[ 'name' ] ) && isset( $value[ 'version' ] ) && isset( $value[ 'filename' ] ) )
                                                            $value[ 'url' ] = sprintf( \PageSpeed::FORMAT_URL_CDNJS_URL, $value[ 'name' ], $value[ 'version' ], $value[ 'filename' ] );
                                                        else
                                                            $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed', [ 'message' => 'api.cdnjs.com ' . print_r( $value, TRUE ) ] ) );
                                                    }
                                                } else
                                                    $value = [ 'url' => strval( $value ) ];
                                                if( isset( $value[ 'url' ] ) && is_string( $value[ 'url' ] ) ) {
                                                    if( $data = @$this->subresource( $this->url( $value[ 'url' ] ), in_array( $tag, $this->properties[ 'bundle' ] ), $extension, $value[ 'integrity' ] ) ) {
                                                        if( in_array( $tag, $this->properties[ 'bundle' ] ) )
                                                            $this->val[ $tag ][] = $tag === 'link' && isset( $value[ 'media' ] ) ? sprintf( \PageSpeed::FORMAT_SUBRESOURCES_MEDIA, $value[ 'media' ], $data ) : $data;
                                                        else {
                                                            if( $this->properties[ 'crossorigin' ] )
                                                                $value[ 'crossorigin' ] = $this->properties[ 'crossorigin' ];
                                                            if( $this->properties[ 'script' ] && $tag === 'script' )
                                                                $value[ $this->properties[ 'script' ] ] = TRUE;
                                                            $this->val[ $tag ][] = array_merge( [ $tag === 'link' ? 'href' : 'src' => $data ], array_intersect_key( array_filter( $value, function( $val ) {
                                                                return isset( $val );
                                                            } ), array_fill_keys( $tag === 'link' ? \PageSpeed::HTML_ATTRIBUTE_LINK : \PageSpeed::HTML_ATTRIBUTE_SCRIPT, TRUE ) ) );
                                                        }
                                                    }
                                                } else
                                                    $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed', [ 'message' => print_r( $value, TRUE ) ] ) );
                                            }
                                            if( in_array( $tag, $this->properties[ 'bundle' ] ) && $this->val[ $tag ] = implode( $tag === 'link' ? PHP_EOL : PHP_EOL . ';', $this->val[ $tag ] ) ) {
                                                $basename = $this->key . ( in_array( $tag, $this->properties[ 'minify' ] ) ? '.min.' : '.' ) . $extension;
                                                if( $this->cache( $filename = $this->properties[ 'path' ] . $basename, $this->val[ $tag ] ) ) {
                                                    $value = [ $tag === 'link' ? 'href' : 'src' => $this->properties[ 'url' ] . $basename . $this->version( $filename ) ];
                                                    if( $this->properties[ 'crossorigin' ] )
                                                        $value[ 'crossorigin' ] = $this->properties[ 'crossorigin' ];
                                                    if( $this->properties[ 'integrity' ] )
                                                        $value[ 'integrity' ] = $this->integrity( $this->val[ $tag ] );
                                                    if( $this->properties[ 'script' ] && $tag === 'script' )
                                                        $value[ $this->properties[ 'script' ] ] = TRUE;
                                                    $this->val[ $tag ] = [ $value ];
                                                } else
                                                    $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_cache', [ 'filename' => $filename ] ) );
                                            }
                                        }
                                }
                                $this->modx->cacheManager->set( $this->key, $this->val, $this->properties[ 'lifetime' ], $this->options );
                            }
                            if( $this->properties[ 'WEBP' ] && $this->properties[ 'convert' ] !== 'disable' || in_array( 'css', $this->properties[ 'minify' ] ) ) {
                                foreach( $this->DOMXPath->query( \PageSpeed::XPATH_CSS ) as $DOMNode ) {
                                    if( $this->properties[ 'WEBP' ] && $this->properties[ 'convert' ] !== 'disable' ) {
                                        $Document = ( new \Sabberworm\CSS\Parser( sprintf( \PageSpeed::FORMAT_SUBRESOURCES_RULESET, $DOMNode->nodeValue ) ) )->parse();
                                        foreach( $Document->getAllValues() as $Value )
                                            if( $Value instanceof \Sabberworm\CSS\Value\URL )
                                                if( in_array( $extension = $this->extension( $url = $Value->getURL()->getString() ), \PageSpeed::EXTENSION_IMAGE ) )
                                                    $Value->setURL( new \Sabberworm\CSS\Value\CSSString( $this->subresource( $this->url( $url ), FALSE, $extension ) ) );
                                        $value = [];
                                        foreach( $Document->getAllRuleSets()[ 0 ]->getRules() as $Rule )
                                            $value[] = $Rule->render( in_array( 'css', $this->properties[ 'minify' ] ) ? Sabberworm\CSS\OutputFormat::createCompact() : Sabberworm\CSS\OutputFormat::createPretty() );
                                        $DOMNode->nodeValue = in_array( 'css', $this->properties[ 'minify' ] ) ? implode( $value ) : implode( ' ', $value );
                                    }
                                    if( in_array( 'css', $this->properties[ 'minify' ] ) ) {
                                        if( $Minify = new \MatthiasMullie\Minify\CSS( $DOMNode->nodeValue ) )
                                            $DOMNode->nodeValue = $Minify->minify();
                                        else
                                            $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => MatthiasMullie\Minify\CSS::class ] ) );
                                    }
                                }
                            }
                            if( $this->properties[ 'WEBP' ] && $this->properties[ 'convert' ] !== 'disable' ) {
                                foreach( $this->DOMXPath->query( \PageSpeed::XPATH_SRC ) as $DOMNode )
                                    if( in_array( $extension = $this->extension( $DOMNode->nodeValue ), \PageSpeed::EXTENSION_IMAGE ) )
                                        $DOMNode->parentNode->setAttribute( $DOMNode->nodeName, $this->subresource( $this->url( $DOMNode->nodeValue ), FALSE, $extension ) );
                                foreach( $this->DOMXPath->query( \PageSpeed::XPATH_SRCSET ) as $DOMNode ) {
                                    $srcset = explode( ',', preg_replace( \PageSpeed::PCRE_HTML_WHITESPACE, ' ', $DOMNode->nodeValue ) );
                                    foreach( $srcset as &$value ) {
                                        $value = explode( ' ', trim( $value ) );
                                        if( in_array( $extension = $this->extension( $value[ 0 ] ), \PageSpeed::EXTENSION_IMAGE ) )
                                            $value[ 0 ] = $this->subresource( $this->url( $value[ 0 ] ), FALSE, $extension );
                                        $value = implode( ' ', $value );
                                    }
                                    $DOMNode->parentNode->setAttribute( $DOMNode->nodeName, implode( ',', $srcset ) );
                                }
                            }
                            foreach( $this->DOMXPath->query( \PageSpeed::XPATH_LINK ) as $DOMNode ) {
                                $href = $DOMNode->getAttribute( 'href' );
                                if( in_array( $as = strtolower( $DOMNode->getAttribute( 'as' ) ), \PageSpeed::HTML_ATTRIBUTE_VALUE_AS ) &&
                                    $href = in_array( $as, \PageSpeed::HTML_ATTRIBUTE_VALUE_AS_EXTENSION ) ?
                                    $this->subresource( $this->url( $href ), FALSE, $as === 'script' ? 'js' : 'css' ) :
                                    $this->subresource( $this->url( $href ), FALSE )
                                )
                                    $DOMNode->setAttribute( 'href', $href );
                                if( ! $DOMNode->hasAttribute( 'crossorigin' ) ) {
                                    if( $this->properties[ 'crossorigin' ] && $as !== 'image' )
                                        $DOMNode->setAttribute( 'crossorigin', $this->properties[ 'crossorigin' ] );
                                    elseif( $as === 'font' )
                                        $DOMNode->setAttribute( 'crossorigin', 'anonymous' );
                                }
                                if( strtolower( $DOMNode->getAttribute( 'rel' ) ) === 'preload' )
                                    @header( sprintf( $DOMNode->hasAttribute( 'crossorigin' ) ? \PageSpeed::FORMAT_HTTP_HEADER_PRELOAD_CROSSORIGIN : \PageSpeed::FORMAT_HTTP_HEADER_PRELOAD, $href, $as, strtolower( $DOMNode->getAttribute( 'crossorigin' ) ) ), FALSE );
                            }
                            if( $this->properties[ 'loading' ] !== 'auto' )
                                foreach( $this->DOMXPath->query( \PageSpeed::XPATH_LOADING ) as $DOMNode )
                                    $DOMNode->setAttribute( 'loading', $this->properties[ 'loading' ] );
                            if( in_array( 'js', $this->properties[ 'minify' ] ) ) {
                                foreach( $this->DOMXPath->query( \PageSpeed::XPATH_JS ) as $DOMNode )
                                    if( $Minify = new \MatthiasMullie\Minify\JS( $DOMNode->nodeValue ) )
                                        $DOMNode->nodeValue = $Minify->minify();
                                    else
                                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => MatthiasMullie\Minify\JS::class ] ) );
                            }
                            if( in_array( 'json', $this->properties[ 'minify' ] ) )
                                foreach( $this->DOMXPath->query( \PageSpeed::XPATH_JSON ) as $DOMNode ) {
                                    $nodeValue = json_decode( $DOMNode->nodeValue );
                                    if( json_last_error() )
                                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_json_last_error_msg', [ 'message' => json_last_error_msg() ] ) );
                                    else
                                        $DOMNode->nodeValue = htmlentities( json_encode( $nodeValue ) );
                                }
                            if( $this->properties[ 'critical' ] && isset( $this->val[ 'critical' ] ) )
                                $this->createElement( $this->head, 'style', $this->val[ 'critical' ] );
                            foreach( $this->val[ 'link' ] as $value ) {
                                $this->createElement( $this->head, 'link', NULL, array_merge( $value, [
                                    'media' => 'all and ( color : 0 )', 'onload' => 'media = \'' . ( $value[ 'media' ] ?? 'all' ) . '\'', 'rel' => 'stylesheet', 'type' => 'text/css'
                                ] ) );
                                @header( sprintf( isset( $value[ 'crossorigin' ] ) ? \PageSpeed::FORMAT_HTTP_HEADER_PRELOAD_CROSSORIGIN : \PageSpeed::FORMAT_HTTP_HEADER_PRELOAD, $value[ 'href' ], 'style', $value[ 'crossorigin' ] ), FALSE );
                            }
                            foreach( $this->sjscripts as $value )
                                $this->createElement( $this->head, 'script', NULL, $value );
                            $config = [ 'critical' => $this->properties[ 'critical' ] && ! isset( $this->val[ 'critical' ] ), 'key' => $this->key, 'resize' => $this->properties[ 'resize' ], 'url' => $this->properties[ 'url' ] ];
                            if( $this->modx->user->isMember( 'Administrator' ) )
                                $config = array_merge( $config, [
                                    'full_appname' => $this->modx->getVersionData()[ 'full_appname' ], 'pt' => sprintf( \PageSpeed::FORMAT_MICROTIME, microtime( TRUE ) - $this->microtime )
                                ], array_combine( \PageSpeed::MODX_TIMING_TAG, array_map( function( $value ) {
                                    return sprintf( \PageSpeed::FORMAT_MODX_TIMING_TAG, $value );
                                }, \PageSpeed::MODX_TIMING_TAG ) ) );
                            $this->createElement( $this->head, 'script', sprintf( \PageSpeed::FORMAT_CONFIG, json_encode( $config, JSON_FORCE_OBJECT ) ) );
                            foreach( array_merge( $this->val[ 'script' ], $this->jscripts ) as $value ) {
                                $this->createElement( $this->body, 'script', NULL, $value );
                                if( parse_url( $value[ 'src' ], PHP_URL_SCHEME ) !== 'data' )
                                    @header( sprintf( isset( $value[ 'crossorigin' ] ) ? \PageSpeed::FORMAT_HTTP_HEADER_PRELOAD_CROSSORIGIN : \PageSpeed::FORMAT_HTTP_HEADER_PRELOAD, $value[ 'src' ], 'script', $value[ 'crossorigin' ] ), FALSE );
                            }
                            if( in_array( 'html', $this->properties[ 'minify' ] ) ) {
                                $this->DOMDocument->formatOutput = FALSE;
                                $this->DOMDocument->preserveWhiteSpace = FALSE;
                                foreach( $this->DOMXPath->query( \PageSpeed::XPATH_COMMENT ) as $DOMNode )
                                    $DOMNode->parentNode->removeChild( $DOMNode );
                                foreach( $this->DOMXPath->query( \PageSpeed::XPATH_TEXT ) as $DOMNode )
                                    if( ! preg_match( \PageSpeed::PCRE_HTML_WHITESPACE_PRESERVE, $DOMNode->getNodePath() ) )
                                        if( strlen( trim( $DOMNode->nodeValue ) ) )
                                            $DOMNode->nodeValue = preg_replace( \PageSpeed::PCRE_HTML_WHITESPACE, ' ', $DOMNode->nodeValue );
                                        else
                                            $DOMNode->parentNode->removeChild( $DOMNode );
                            } else {
                                $this->DOMDocument->formatOutput = TRUE;
                                $this->DOMDocument->preserveWhiteSpace = TRUE;
                            }
                            return preg_replace_callback( \PageSpeed::PCRE_HTML_ENTITY, function( $matches ) {
                                return html_entity_decode( $matches[ 0 ] );
                            }, in_array( 'html', $this->properties[ 'minify' ] ) ? preg_replace( \PageSpeed::PCRE_HTML_DOCTYPE, '$1', $this->DOMDocument->saveHTML(), 1 ) : $this->DOMDocument->saveHTML() );
                        }
                    }
                }
            }
        }
        public function replacePlaceholders( string $pattern, array $properties, string $data ) {
            return preg_replace_callback( $pattern, function( $matches ) use( $properties ) {
                return isset( $properties[ $matches[ 'key' ] ] ) && is_scalar( $properties[ $matches[ 'key' ] ] ) ? $properties[ $matches[ 'key' ] ] : $matches[ 0 ];
            }, $data );
        }
        public function stream_context_create( string $user_agent ) {
            return is_resource( $this->context = stream_context_create( [ 'http' => [
                'timeout' => \PageSpeed::SUBRESOURCES_HTTP_TIMEOUT, 'user_agent' => $user_agent
            ] ] ) );
        }
        public function subresource( string $url, bool $return, string $extension = NULL, string &$integrity = NULL ) {
            if( in_array( $extension = $extension ?? $this->extension( $url ), $this->properties[ 'upload_files' ] ) ) {
                if( $this->properties[ 'WEBP' ] && in_array( $extension, \PageSpeed::EXTENSION_IMAGE ) && $this->properties[ 'convert' ] === 'dynamic' )
                    return $return ? $this->convert( $this->data( $url ) ) : sprintf( $this->properties[ 'format_url_convert' ], $this->key, $url ) . parse_url( $url, PHP_URL_FRAGMENT );
                if( in_array( $extension, \PageSpeed::EXTENSION_IMAGE ) )
                    $hash = hash( \PageSpeed::CACHE_HASH_ALGO, serialize( array_intersect_key( $this->properties, array_fill_keys( \PageSpeed::PROPERTIES_CACHE_IMAGE, TRUE ) ) ) . $url );
                elseif( in_array( $extension, \PageSpeed::EXTENSION_STYLESHEET ) )
                    $hash = hash( \PageSpeed::CACHE_HASH_ALGO, serialize( array_intersect_key( $this->properties, array_fill_keys( array_merge( \PageSpeed::PROPERTIES_CACHE_STYLESHEET, \PageSpeed::MODX_OPTION ), TRUE ) ) ) . $url );
                else
                    $hash = hash( \PageSpeed::CACHE_HASH_ALGO, $url );
                $minify = in_array( 'link', $this->properties[ 'minify' ] ) && in_array( $extension, \PageSpeed::EXTENSION_STYLESHEET ) ||
                    in_array( 'script', $this->properties[ 'minify' ] ) && in_array( $extension, \PageSpeed::EXTENSION_JAVASCRIPT );
                if( $this->properties[ 'convert' ] === 'persistent' && in_array( $extension, \PageSpeed::EXTENSION_IMAGE ) && $filename = $this->filename( $url ) ) {
                    $basename = pathinfo( $path = parse_url( $url, PHP_URL_PATH ), PATHINFO_FILENAME ) . '.webp';
                    $filename = dirname( $filename ) . DIRECTORY_SEPARATOR . $basename;
                    $filehref = $this->properties[ 'site_url' ] . ltrim( str_replace( DIRECTORY_SEPARATOR, '/', dirname( $path ) ), '/' ) . '/' . $basename;
                } else {
                    $basename = strlen( $extension ) ? substr( $hash, \PageSpeed::CACHE_SPLIT_LENGTH * \PageSpeed::CACHE_SPLIT_LEVELS ) . ( $minify ? '.min.' : '.' ) . (
                        $this->properties[ 'WEBP' ] && in_array( $extension, \PageSpeed::EXTENSION_IMAGE ) && in_array( $this->properties[ 'convert' ], \PageSpeed::PROPERTIES_CACHE_CONVERT ) ? 'webp' : $extension
                    ) : substr( $hash, \PageSpeed::CACHE_SPLIT_LENGTH * \PageSpeed::CACHE_SPLIT_LEVELS );
                    $filename = $this->properties[ 'path' ] . implode( DIRECTORY_SEPARATOR, str_split( substr( $hash, 0, \PageSpeed::CACHE_SPLIT_LENGTH * \PageSpeed::CACHE_SPLIT_LEVELS ), \PageSpeed::CACHE_SPLIT_LENGTH ) ) . DIRECTORY_SEPARATOR . $basename;
                    $filehref = $this->properties[ 'url' ] . implode( '/', str_split( substr( $hash, 0, \PageSpeed::CACHE_SPLIT_LENGTH * \PageSpeed::CACHE_SPLIT_LEVELS ), \PageSpeed::CACHE_SPLIT_LENGTH ) ) . '/' . $basename;
                }
                if( is_file( $filename ) && ( $this->properties[ 'lifetime' ] === 0 || $this->microtime - filemtime( $filename ) < $this->properties[ 'lifetime' ] ) ) {
                    $data = file_get_contents( $filename );
                    if( $this->properties[ 'integrity' ] && in_array( $extension, array_merge( \PageSpeed::EXTENSION_JAVASCRIPT, \PageSpeed::EXTENSION_STYLESHEET ) ) )
                        $integrity = $this->integrity( $data );
                    return $return ? $data : $filehref . $this->version( $filename ) . parse_url( $url, PHP_URL_FRAGMENT );
                }
                if( $data = $this->data( $url ) ) {
                    if( $this->properties[ 'integrity' ] && isset( $integrity ) && ! $this->integrity( $data, $integrity ) )
                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_integrity', [ 'url' => $url ] ) );
                    else {
                        if( in_array( $extension, array_merge( \PageSpeed::EXTENSION_JAVASCRIPT, \PageSpeed::EXTENSION_STYLESHEET ) ) ) {
                            $data = preg_replace( \PageSpeed::PCRE_SUBRESOURCES_SOURCE_MAPPING, '', $data );
                            if( in_array( $extension, \PageSpeed::EXTENSION_STYLESHEET ) ) {
                                if( preg_match( \PageSpeed::PCRE_URL_GOOGLE_FONTS, $url ) ) {
                                    $Document = ( new \Sabberworm\CSS\Parser( $data ) )->parse();
                                    $fonts = [ 'truetype' => $this->getAllURLs( $Document ) ];
                                    foreach( \PageSpeed::USERAGENT_GOOGLE_FONTS as $format => $useragent ) {
                                        if( $this->config[ 'cURL' ] )
                                            curl_setopt( $this->ch, CURLOPT_USERAGENT, $useragent );
                                        else
                                            $this->stream_context_create( $useragent );
                                        $fonts[ $format ] = $this->getAllURLs( ( new \Sabberworm\CSS\Parser( $this->data( $url ) ) )->parse() );
                                    }
                                    if( $this->config[ 'cURL' ] )
                                        curl_setopt( $this->ch, CURLOPT_USERAGENT, \PageSpeed::class );
                                    else
                                        $this->stream_context_create( \PageSpeed::class );
                                    $fonts = array_reverse( $fonts );
                                    foreach( $Document->getAllRuleSets() as $index => $RuleSet )
                                        if( $RuleSet instanceof \Sabberworm\CSS\RuleSet\AtRuleSet && $RuleSet->atRuleName() === 'font-face' )
                                            foreach( $RuleSet->getRules() as $Rule )
                                                if( $Rule->getRule() === 'src' ) {
                                                    $Value = new \Sabberworm\CSS\Value\RuleValueList( ',' );
                                                    foreach( $fonts as $format => $data ) {
                                                        $RuleValueList = new \Sabberworm\CSS\Value\RuleValueList( ' ' );
                                                        $RuleValueList->addListComponent( new \Sabberworm\CSS\Value\URL( new \Sabberworm\CSS\Value\CSSString( $data[ $index ] ) ) );
                                                        $RuleValueList->addListComponent( new \Sabberworm\CSS\Value\CSSFunction( 'format', [ new \Sabberworm\CSS\Value\CSSString( $format ) ] ) );
                                                        $Value->addListComponent( $RuleValueList );
                                                    }
                                                    $Rule->setValue( $Value );
                                                }
                                }
                                $Document = $Document ?? ( new \Sabberworm\CSS\Parser( $data ) )->parse();
                                $this->parseCSSBlockList( $Document, $url );
                                foreach( $Document->getAllValues() as $Value )
                                    if( $Value instanceof \Sabberworm\CSS\Value\URL )
                                        $Value->setURL( new \Sabberworm\CSS\Value\CSSString(
                                            strtok( $url, '#' ) === strtok( $this->url( $Value->getURL()->getString(), $url ), '#' ) ?
                                                NULL : $this->subresource( $this->url( $Value->getURL()->getString(), $url ), FALSE )
                                        ) );
                                if( $this->properties[ 'display' ] !== 'auto' )
                                    foreach( $Document->getAllRuleSets() as $RuleSet )
                                        if( $RuleSet instanceof \Sabberworm\CSS\RuleSet\AtRuleSet && $RuleSet->atRuleName() === 'font-face' ) {
                                            foreach( $RuleSet->getRules() as $Rule )
                                                if( $display = $Rule->getRule() === 'font-display' )
                                                    break;
                                            if( ! $display ) {
                                                $Rule = new \Sabberworm\CSS\Rule\Rule( 'font-display' );
                                                $Rule->setValue( $this->properties[ 'display' ] );
                                                $RuleSet->addRule( $Rule );
                                            }
                                        }
                                $data = $Document->render( $minify ? \Sabberworm\CSS\OutputFormat::createCompact() : \Sabberworm\CSS\OutputFormat::createPretty() );
                            }
                            if( $minify ) {
                                $data = trim( preg_replace( \PageSpeed::PCRE_SUBRESOURCES_COMMENT_LEADING, '', trim( $data ) ) );
                                if( in_array( $extension, \PageSpeed::EXTENSION_STYLESHEET ) ) {
                                    if( $Minify = new \MatthiasMullie\Minify\CSS( $data ) )
                                        $data = $Minify->minify();
                                    else
                                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => MatthiasMullie\Minify\CSS::class ] ) );
                                } else {
                                    if( $Minify = new \MatthiasMullie\Minify\JS( $data ) )
                                        $data = $Minify->minify();
                                    else
                                        $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_class', [ 'name' => MatthiasMullie\Minify\JS::class ] ) );
                                }
                            }
                        }                    
                        if( $this->properties[ 'WEBP' ] && in_array( $this->properties[ 'convert' ], \PageSpeed::PROPERTIES_CACHE_CONVERT ) && in_array( $extension, \PageSpeed::EXTENSION_IMAGE ) )
                            $data = $this->convert( $data );
                        if( $data ) {
                            if( $this->cache( $filename, $data ) ) {
                                if( $this->properties[ 'integrity' ] && in_array( $extension, array_merge( \PageSpeed::EXTENSION_STYLESHEET, \PageSpeed::EXTENSION_JAVASCRIPT ) ) )
                                    $integrity = $this->integrity( $data );
                                return $return ? $data : $filehref . $this->version( $filename ) . parse_url( $url, PHP_URL_FRAGMENT );
                            } else
                                $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_cache', [ 'filename' => $filename ] ) );
                        }
                    }
                }
            } else
                $this->modx->log( \xPDO::LOG_LEVEL_WARN, $this->modx->lexicon( 'message_PageSpeed_upload_files', [ 'extension' => $extension ] ) );
            return $return ? NULL : $url;
        }
        public function url( string $url, string $base_url = NULL ) {
            $base_url = $base_url ?? $this->properties[ 'site_url' ];
            extract( ( $parse_url = parse_url( trim( $url ) ) ) ? $parse_url : parse_url( $base_url ) );
            if( isset( $scheme ) && $scheme === 'data' )
                return $url;
            if( empty( $scheme ) ) {
                $parse_url = parse_url( $base_url );
                if( empty( $parse_url[ 'scheme' ] ) || $parse_url[ 'scheme' ] === 'data' )
                    $parse_url = parse_url( $this->properties[ 'site_url' ] );
                $scheme = $parse_url[ 'scheme' ];
                if( empty( $host ) ) {
                    $user = $parse_url[ 'user' ] ?? NULL;
                    $pass = $parse_url[ 'pass' ] ?? NULL;
                    $host = $parse_url[ 'host' ];
                    $port = $parse_url[ 'port' ] ?? NULL;
                    if( empty( $path ) ) {
                        $path = $parse_url[ 'path' ] ?? '/';
                        if( empty( $query ) ) {
                            $query = $parse_url[ 'query' ] ?? NULL;
                            if( empty( $fragment ) )
                                $fragment = $parse_url[ 'fragment' ] ?? NULL;
                        }
                    }
                }
                $path = preg_match( \PageSpeed::PCRE_URL_SLASH_LEADING, $path ) ? $path : dirname( $parse_url[ 'path' ] ) . '/' . $path;
            }
            return $scheme . '://' . ( isset( $user ) ? $user . ( isset( $pass ) ? ':' . $pass : '' ) . '@' : '' ) . $host . ( isset( $port ) ? ':' . $port : '' ) . preg_replace( \PageSpeed::PCRE_URL_SLASH, [ '/', '/', '' ], $path ) . ( isset( $query ) ? '?' . $query : '' ) . ( isset( $fragment ) ? '#' . $fragment : '' );
        }
        public function version( string $filename ) {
            return '?' . base_convert( filemtime( $filename ), 10, 36 );
        }
    }
