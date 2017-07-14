/*
Plugin Name: Captcha Free Anti Spam for Contact Form 7 (Simple No-Bot)
Plugin URI: http://www.lilaeamedia.com/simple-no-bot/
Description: Simple, lightweight, no captcha, no configuration. Just works.
Version: 1.0.2
Author: Lilaea Media
License: GPLv2
(c) 2017 J Fleming Lilaea Media LLC
*/
;(function($){
    'use strict';
    
    /**
     * Bind form inputs to mouse/keyboard events
     */
    function init(){
        $( '.wpcf7-form input,.wpcf7-form textarea,.wpcf7-form select' ).on( 'mousedown touchstart', mousedown );
        $( '.wpcf7-form input,.wpcf7-form textarea' ).on( 'keydown', keydown );
        $( '.wpcf7-form input,.wpcf7-form textarea,.wpcf7-form select' ).on( 'focus', focus );
        $( '.wpcf7-form input[type="submit"]' ).prop( 'disabled', true );
        $( 'body' ).on( 'snbsentok', function(){
            get_token( eventstr );
        } );
    }
    
    /**
     * Click listener
     */
    function mousedown( e ){
        if ( !interacted ){
            // console.log( 'mousedown detected ' + e.pageX + ' ' + e.pageY );
            // console.log( e );
            eventstr += e.pageX.toString() + e.pageY.toString();
            test();
        }
    }
    
    /**
     * Focus listener
     */
    function focus( e ){
        if ( !interacted ){
            // console.log( 'focus detected ' );
            // console.log( e );
            eventstr += String.fromCharCode( ( Math.floor( Math.random() * 26 ) ) + 97 );
            test();
        }
    }
    
    /**
     * Keystroke listener
     */
    function keydown( e ){
        if ( !interacted ){
            // console.log( 'keydown detected ' + e.keyCode );
            // console.log( e );
            eventstr += e.keyCode.toString();
            test();
        }
    }
    
    /**
     * Check if input event string is long enough to hash
     */
    function test(){
        if ( !interacted && eventstr.length >= window.snbvars.minEvents ){
            interacted++;
            get_token( eventstr );
        }
    }
    
    /**
     * Simple, lightweight string hashing function works in both JS and PHP
     */
    function generate_hash( source ){
        var hash = 0, 
            i, l, 
            chr;
        if ( source.length === 0 ) {
            return hash;
        }
        for ( i = 0, l = source.length; i < l; i++ ) {
            chr   = source.charCodeAt( i );
            hash  = ( ( hash << 5 ) - hash ) + chr;
            hash |= 0; // Convert to 32bit integer
        }
        return hash;
    }
    
    /**
     * Pass input event string over XHR and retrieve unique token from server.
     * Inject new input into any/all contact forms to pass back as verification.
     * Input contains unique token and hashed input event string.
     * Only do this once until form comes back as OK.
     */
    function get_token( str ){
        if ( pending ) {
            return;
        }
        pending++;
        hashevent = generate_hash( str );
        // console.log( 'hashevent: ' + hashevent );
        var postdata = {
                action:     'get_token',
                eventstr:   str,
                verify:     window.snbvars.verify
            };
        // console.log( postdata );
        $.ajax( {
            url:        window.snbvars.ajaxurl,
            type:       'post',
            dataType:   'text',
            data:       postdata
            
        } ).done( function( response ){
            // console.log( 'response: ' + response );
            if ( pending && response ){
                if ( !appended ){
                    $( '.wpcf7-form' ).each( function( ndx, el ){
                        $( el ).append( '<input type="hidden" name="snb-token" />' );
                        forms++;
                    } );
                    appended++;
                }
                if ( forms ){
                    //var tokenparts = response.split( /:/ );
                    //console.log( tokenparts );
                    $( '.wpcf7-form input[name="snb-token"]' ).val( response + ':' + hashevent );
                    setTimeout( function(){
                        get_token( eventstr );
                    }, 1800000 ); // refresh token after 30 minutes
                }
                $( '.wpcf7-form input[type="submit"]' ).prop( 'disabled', false );
                pending = 0;
            }
        } ).fail( function(){
            pending = 0;
            //console.log( 'ajax failed.' );
        } );
    }
    
    /**
     * initialize vars
     */
    var forms       = 0,    // are there contact forms on page?
        interacted  = 0,    // has input event string reached min length?
        appended    = 0,    // has verification input field been injected?
        pending     = 0,    // is XHR awaiting response?
        eventstr    = '',   // input event string
        hashevent;          // hashed event string
    
    $( document ).ready( function(){
        init(); // wait until all forms are loaded
    });
    
})(jQuery);