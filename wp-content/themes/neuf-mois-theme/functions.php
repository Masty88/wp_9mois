<?php

/**
 * Neuf-mois functions and definitions
 *
 * @package Neuf-mois
 */

function neuf_mois_scripts(){
    wp_enqueue_script( 'bootstrap-js', get_template_directory_uri().'/inc/bootstrap.min.js', array('jquery' ), '5.3.0', true);
    wp_enqueue_style( 'bootstrap-css', get_template_directory_uri().'/inc/bootstrap.min.css',array(), '5.3.0','all');
    wp_enqueue_style( 'neuf-mois-style', get_stylesheet_uri(), array(), filemtime(get_template_directory( ).'/style.css'  ), 'all');
}
add_action( 'wp_enqueue_scripts', 'neuf_mois_scripts' );