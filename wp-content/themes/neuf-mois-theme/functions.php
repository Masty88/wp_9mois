<?php

/**
 * Neuf-mois functions and definitions
 *
 * @package Neuf-mois
 */

function neuf_mois_scripts(){
    // Enqueue jQuery from a CDN
    wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js', array(), '3.6.0', true );

    // Enqueue Bulma CSS from a CDN
    wp_enqueue_style( 'bulma-css', 'https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css', array(), '0.9.3' );

    // Enqueue Font Awesome CSS from a CDN
    wp_enqueue_style( 'font-awesome-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4', 'all' );

    wp_enqueue_style( 'neuf-mois-style', get_stylesheet_uri(), array(), filemtime(get_template_directory() . '/style.css' ), 'all');
    wp_enqueue_script( 'neuf-mois-index', get_template_directory_uri() . '/index.js', array(), filemtime( get_template_directory() . '/index.js' ), true );
}

add_action( 'wp_enqueue_scripts', 'neuf_mois_scripts' );

function neuf_mois_config(){
    register_nav_menus(
        array(
            'neuf_mois_main_menu' => 'Neuf Mois Main Menu',
            'neuf_mois_footer_menu'=> 'Neuf Mois Footer Menu'
        )
    );

    add_theme_support('woocommerce', array(
        'thumbnail_image_width' => 255
    ));
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    if ( ! isset( $content_width ) ) {
        $content_width = 600;
    }
}

add_action('after_setup_theme', 'neuf_mois_config', 0);

function themename_custom_logo_setup() {
    $defaults = array(
        'height'               => 200,
        'width'                => 200,
        'flex-height'          => true,
        'flex-width'           => true,
        'header-text'          => array( 'site-title', 'site-description' ),
        'unlink-homepage-logo' => true,
    );
    add_theme_support( 'custom-logo', $defaults );
}
add_action( 'after_setup_theme', 'themename_custom_logo_setup' );

function mytheme_customize_register( $wp_customize ) {
    // Aggiungi una sezione per i colori
    $wp_customize->add_section( 'mytheme_colors' , array(
        'title'      => __( 'Colors', 'mytheme' ),
        'priority'   => 30,
    ) );

    // Aggiungi un'opzione per il colore di sfondo
    $wp_customize->add_setting( 'background_color' , array(
        'default'     => '#ffffff',
        'transport'   => 'refresh',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
        'label'        => __( 'Background Color', 'mytheme' ),
        'section'    => 'mytheme_colors',
        'settings'   => 'background_color',
    ) ) );
}

add_action( 'customize_register', 'mytheme_customize_register' );

function mytheme_customize_css() {
    $custom_css = "
        body {
            background-color: #" . get_theme_mod( 'background_color', '#ffffff' ) . ";
        }";
    wp_add_inline_style( 'neuf-mois-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'mytheme_customize_css');

function woocommerce_header_add_to_cart_fragment( $fragments ) {
    ob_start();
    ?>
    <div class="l-header-shop">
        <span class="shopbag_items_number"><?php echo WC()->cart->cart_contents_count; ?></span>
        <i class="icon-shop"></i>
        <div class="overview">
            <span class="bag-items-number"><?php echo sprintf (_n( '%d шт.', '%d шт.', WC()->cart->cart_contents_count, 'woodstock' ), WC()->cart->cart_contents_count ); ?></span>
            <?php echo WC()->cart->get_cart_total(); ?>
        </div>
    </div>
    <?php
    $fragments['div.l-header-shop'] = ob_get_clean();
    return $fragments;
}

function wc_refresh_mini_cart_count ($fragments){
    ob_start();
    $items_count = WC()->cart->get_cart_contents_count()
        ?>
    <span class="cart-count">
        <?php echo $items_count ? $items_count : '0'; ?>
    </span>
    <?php
        $fragments['.cart-count']= ob_get_clean();
        return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'wc_refresh_mini_cart_count');