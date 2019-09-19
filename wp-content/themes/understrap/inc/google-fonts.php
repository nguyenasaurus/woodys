<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'add_google_fonts' );
    function add_google_fonts() {

    $query_args = array(
    'family' => 'Bungee|Holtwood+One+SC|Roboto:400,700|Rye&display=swap'
    );

    wp_register_style( 
        'google-fonts', 
        add_query_arg( $query_args, '//fonts.googleapis.com/css' ), 
        array(), 
        null 
        );
        wp_enqueue_style( 'google-fonts' );

}
