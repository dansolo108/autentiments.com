var PageSpeed = PageSpeed || {};
! function( window, PageSpeed ) {
    'use strict';
    PageSpeed.append = function( name, value ) {
        if( value instanceof Object )
            for( var [ index, value ] of Object.entries( value ) )
                PageSpeed.append.call( this,  name + '[' + index + ']', value );
        else
            this.append( name, value );
    };
    PageSpeed.critical = [];
    PageSpeed.cssRules = function( cssRules, mediaText = null ) {
        for( var rule of cssRules ) {
            /*
                https://developer.mozilla.org/en-US/docs/Web/API/CSSRule
            */
            //    CSSRule.STYLE_RULE / CSSStyleRule
            if( rule.type === 1 ) {
                var nodeList = document.querySelectorAll( rule.selectorText );
                if( nodeList.length )
                    for( var element of nodeList )
                        if( element.getBoundingClientRect().top < window.innerHeight ) {
                            PageSpeed.critical.push( mediaText ? '@media ' + mediaText + ' {' + rule.cssText + ' }' : rule.cssText );
                            break;
                        }
            }
            //    CSSRule.MEDIA_RULE / CSSMediaRule
            if( rule.type === 4 )
                PageSpeed.cssRules( rule.cssRules, mediaText ? mediaText + ' and ' + rule.media.mediaText : rule.media.mediaText );
        }
    };
    PageSpeed.extension = function() {
        return this.split( /[#?]/ )[ 0 ].split( '.' ).pop().trim().toLowerCase().replace( [ /^jfi$/, /^jfif$/, /^jif$/, /^jpe$/, /^jpg$/ ], 'jpeg' );
    };
    PageSpeed.fetch = function() {
        PageSpeed.formData = PageSpeed.formData || { OnHandleRequest : 'PageSpeed', key : PageSpeed.config.key };
        PageSpeed.formData = PageSpeed.merge.call( PageSpeed.formData, this );
        PageSpeed.throttle( function() {
            var formData = new FormData();
            for( var [ name, value ] of Object.entries( PageSpeed.formData ) )
                PageSpeed.append.call( formData, name, value );
            fetch( window.location, { method : 'POST', body : formData } );
            delete PageSpeed.formData;
        }, 1000 );
    };
    PageSpeed.merge = function() {
        for( var value of arguments )
            for( var [ index, value ] of Object.entries( value ) )
                if( ( this[ index ] instanceof Array ) && ( value instanceof Array ) )
                    this[ index ] = this[ index ].concat( value );
                else if( ( this[ index ] instanceof Object ) && ( value instanceof Object ) )
                    this[ index ] = PageSpeed.merge.call( this[ index ], value );
                else
                    this[ index ] = value;
        return this;
    };
    PageSpeed.resize = function() {
        var width = this.width * 2;
        var height = this.height * 2;
        if( this.naturalWidth && this.naturalHeight && this.naturalWidth > width && this.naturalHeight > height )
            PageSpeed.fetch.call( {
                resize : [ { src : this.src, width : width, height : height } ]
            } );
    };
    PageSpeed.throttle = function( callback, milliseconds ) {
        clearTimeout( PageSpeed.timeout );
        PageSpeed.timeout = setTimeout( function() {
            callback.apply( this, arguments );
        }.bind( this, arguments ), milliseconds );
    };
	window.addEventListener( 'load', function( event ) {
	    if( PageSpeed.config.full_appname ) {
            console.groupCollapsed( PageSpeed.config.full_appname );
            for( var [ name, description ] of Object.entries( {
                qt : 'Query Time', q : 'Query Count', p : 'Parse Time', t : 'Total Time', s : 'Source', m : 'Memory Usage', pt : 'PageSpeed Time'
            } ) )
                console.log( description + ': %c' + PageSpeed.config[ name ], 'font-weight : bold' );
            console.groupEnd();
	    }
	    if( PageSpeed.config.key ) {
            if( PageSpeed.config.critical ) {
                var script = document.createElement( 'script' );
                script.src = 'https://unpkg.com/csso/dist/csso.js';
                script.addEventListener( 'load', function( event ) {
                    for( var sheet of document.styleSheets )
                        try {
                            PageSpeed.cssRules( sheet.cssRules );
                        } catch( exception ) {
                            //  DOMException: CSSStyleSheet.rules getter: Not allowed to access cross-origin stylesheet
                            console.error( exception );
                        }
                    PageSpeed.fetch.call( {
                        critical : csso.minify( PageSpeed.critical.join( '' ) ).css
                    } );
                } );
                document.head.appendChild( script );
            }
            if( PageSpeed.config.resize )
                for( var element of document.querySelectorAll( 'img[src^="' + PageSpeed.config.url + '"]' ) )
                    if( [ 'gif', 'jpeg', 'png', 'webp' ].includes( PageSpeed.extension.call( element.src ) ) ) {
                        if( element.naturalWidth && element.naturalHeight )
                            PageSpeed.resize.call( element );
                        else
                            element.addEventListener( 'load', PageSpeed.resize );
                    }    
        }
	} );
}( window, PageSpeed );
