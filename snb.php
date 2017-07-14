<?php
/*
Plugin Name: Captcha Free Anti Spam for Contact Form 7 (Simple No-Bot)
Plugin URI: http://www.lilaeamedia.com/simple-no-bot/
Description: Simple, lightweight, no captcha, no configuration. Just works.
Version: 1.0.2
Author: Lilaea Media
License: GPLv2
(c) 2017 J Fleming Lilaea Media LLC
*/

if ( !class_exists( 'SimpleNoBot' ) ):

class SimpleNoBot {
    
    private $eventhash;
    private $token;
    private $uses;
    private $nonce;
    private $seed;
    
    function __construct(){
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'wp_ajax_nopriv_get_token', array( $this, 'get_token' ) );
        add_action( 'wp_ajax_get_token', array( $this, 'get_token' ) );
        add_filter( 'wpcf7_spam', array( $this, 'validate_session' ) );
        add_filter( 'wpcf7_ajax_json_echo', array( $this, 'trigger_ok') );
    }
    

    function trigger_ok( $items ){
        if ( !empty( $items[ 'mailSent' ] ) ):
            $items[ 'onSentOk' ][] = 'window.jQuery( "body" ).trigger( "snbsentok" );';
            delete_transient( 'snbvars' . $this->token );
        endif;
        // $this->log_debug( __FUNCTION__, print_r( $items, TRUE ) );
        return $items;
    }
    
    function enqueue(){
        wp_enqueue_script( 'snbvars', plugin_dir_url( __FILE__ ) . '/snb.min.js', array( 'jquery' ), SIMPLE_NO_BOT_VERSION, TRUE );
        wp_localize_script( 
            'snbvars', 
            'snbvars', 
            array( 
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'minEvents' => 5,
                'verify'    => wpcf7_create_nonce( 'snbvars' ),
            ) 
        );
    }
                   
    function get_token(){
        if ( isset( $_POST[ 'eventstr' ] ) 
           && isset( $_POST[ 'verify' ] ) 
           && wpcf7_verify_nonce( $_POST[ 'verify' ], 'snbvars' ) ):
            $this->eventhash = $this->generate_hash( sanitize_text_field( $_POST[ 'eventstr' ] ) );
            $this->token = uniqid( '' );
            $this->seed = mt_rand();
            $this->nonce = wpcf7_create_nonce( $this->seed . $this->token );
            $this->uses = 0;
            $this->save_session();
            die( $this->token );
        endif;
        die();
    }
    
    function save_session(){
        $session = implode( ':', array(
            $this->eventhash,
            $this->nonce,
            ++$this->uses,
            $this->seed
        ) );
        // $this->log_debug( __FUNCTION__, $this->token );
        // $this->log_debug( __FUNCTION__, $session );
        set_transient( 'snbvars' . $this->token, $session, 3600 ); // token valid for one hour
    }
    
    /**
     * spam tests are run after input validation,
     * so this won't affect legitimate users with invalid inputs.
     */
    function validate_session( $spam ) {
        if ( isset( $_POST[ 'snb-token' ] ) ):
            list( $token, $hash ) = explode( ':', $_POST[ 'snb-token' ] );
            if ( $session = get_transient( 'snbvars' . $token ) ):
                // $this->log_debug( __FUNCTION__, $session );
                list( $eventhash, $nonce, $uses, $seed ) = explode( ':', $session );
                $this->token        = $token;
                $this->eventhash    = $eventhash;
                $this->nonce        = $nonce;
                $this->uses         = intval( $uses );
                $this->seed         = $seed;
                $this->save_session();
                // $this->log_debug( __FUNCTION__, $_POST[ 'snb-token' ] );
                if ( strlen( $hash ) > 3
                    && $uses < 2 // more than 1 use of same token in one hour is probably trying to game honeypot
                    //&& time() - $lastuse > 5 // multiple submits in less than 5 seconds is probably spam
                    && wpcf7_verify_nonce( $nonce, $seed . $token ) // verify token is from same page
                    && $eventhash === $hash ): // verify browser hashes input events from same page
                    return $spam;
                endif;
            endif;
        endif;
		return TRUE;
	}
    
    /**
     * simple, lightweight string hashing function works in both JS and PHP
     */
    function generate_hash( $source ){
        if ( strlen( $source ) == 0 )
            return 0;
        $hash = 0;
        for ( $i = 0, $l = strlen( $source ); $i < $l; $i++ ):
            $chr   = ord( substr( $source, $i, 1 ) );
            $hash  = ( ( $hash << 5 ) - $hash )  + $chr;
            $hash &= 0xFFFFFFFF; // convert to 32 bit integer
        endfor;
        return ( $hash > 0x7FFFFFFF ) ? $hash -= 0x100000000 : $hash; // act like 32 bit signed
    }
    
    /*
    function log_debug( $fn, $msg ){
        file_put_contents( SIMPLE_NO_BOT_DIR . '/debug.txt', $fn . ":" . $msg . "\n", FILE_APPEND );
    }
    */
}

defined( 'SIMPLE_NO_BOT_VERSION' ) or define( 'SIMPLE_NO_BOT_VERSION', '1.0.1' );
//define( 'SIMPLE_NO_BOT_DIR', dirname( __FILE__ ) );

new SimpleNoBot();

endif;
