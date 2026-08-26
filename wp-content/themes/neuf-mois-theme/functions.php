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

// ================================================
// SOLUZIONE COMPLETA - AGGIUNGI A FUNCTIONS.PHP
// ================================================

// 1. Fragment per aggiornare il contatore
function neuf_mois_cart_count_fragments($fragments) {
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();
    
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'neuf_mois_cart_count_fragments');

// 2. Carica gli script necessari
function neuf_mois_enqueue_cart_scripts() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-cart-fragments');
        
        // Aggiungi le variabili necessarie per AJAX
        wp_localize_script('wc-cart-fragments', 'neuf_mois_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%')
        ));
    }
}
add_action('wp_enqueue_scripts', 'neuf_mois_enqueue_cart_scripts');

// 3. Script JavaScript che gestisce TUTTI i casi
function neuf_mois_cart_counter_script() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        console.log('🛒 Neuf-mois cart counter initialized');
        
        // Funzione per aggiornare il contatore
        function updateCartCounter() {
            $.ajax({
                url: '/?wc-ajax=get_refreshed_fragments',
                type: 'POST',
                success: function(data) {
                    if (data && data.fragments) {
                        // Aggiorna tutti i contatori nell'header
                        if (data.fragments['span.cart-count']) {
                            $('.cart-count').replaceWith(data.fragments['span.cart-count']);
                        } else if (data.cart_contents_count !== undefined) {
                            $('.cart-count').text(data.cart_contents_count);
                        }
                        console.log('✅ Cart updated! Count:', data.cart_contents_count);
                    }
                },
                error: function() {
                    // Fallback: ricarica la pagina per aggiornare il contatore
                    location.reload();
                }
            });
        }
        
        // CASO 1: Bottoni AJAX nelle pagine archivio/shop
        $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
            console.log('✅ Product added via AJAX button');
            if (fragments && fragments['span.cart-count']) {
                $('.cart-count').replaceWith(fragments['span.cart-count']);
            }
        });
        
        // CASO 2: Form nella pagina singolo prodotto (NON AJAX)
        $('form.cart').on('submit', function(e) {
            console.log('📦 Single product form submitted');
            
            var $form = $(this);
            var $button = $form.find('.single_add_to_cart_button');
            
            // Se il prodotto è variabile, assicurati che sia selezionata una variazione
            if ($form.find('.variations_form').length && !$form.find('.variation_id').val()) {
                return true; // Lascia che WooCommerce gestisca l'errore
            }
            
            // Previeni il submit normale
            e.preventDefault();
            
            // Disabilita il bottone
            $button.prop('disabled', true).addClass('loading');
            
            // Invia via AJAX
            $.ajax({
                type: 'POST',
                url: $form.attr('action') || window.location.href,
                data: $form.serialize() + '&add-to-cart=' + $form.find('[name=add-to-cart]').val(),
                success: function(response) {
                    console.log('✅ Product added successfully');
                    
                    // Aggiorna il contatore
                    updateCartCounter();
                    
                    // Cambia il testo del bottone
                    $button.removeClass('loading').addClass('added');
                    $button.text('✓ Aggiunto');
                    
                    // Ripristina dopo 2 secondi
                    setTimeout(function() {
                        $button.prop('disabled', false)
                               .removeClass('added')
                               .text($button.data('text') || 'Aggiungi al carrello');
                    }, 2000);
                },
                error: function() {
                    // In caso di errore, fai il submit normale
                    $form.off('submit').submit();
                }
            });
        });
        
        // CASO 3: Altri bottoni add to cart generici
        $(document).on('click', '.add_to_cart_button:not(.ajax_add_to_cart)', function(e) {
            console.log('🔄 Non-AJAX add to cart clicked');
            setTimeout(updateCartCounter, 1500);
        });
        
        // CASO 4: Link diretto con parametro add-to-cart
        if (window.location.href.indexOf('add-to-cart=') > -1) {
            console.log('🔄 Page loaded after adding to cart');
            setTimeout(updateCartCounter, 500);
        }
        
        // Aggiorna il contatore al caricamento della pagina
        $(document.body).trigger('wc_fragment_refresh');
    });
    </script>
    
    <style>
    /* Stile per il bottone durante il caricamento */
    .single_add_to_cart_button.loading {
        opacity: 0.6;
        cursor: wait;
    }
    
    .single_add_to_cart_button.added {
        background-color: #48c78e !important;
        color: white !important;
    }
    
    /* Animazione per il contatore */
    @keyframes cartPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); background-color: #48c78e; color: white; border-radius: 50%; }
        100% { transform: scale(1); }
    }
    
    .cart-count {
        display: inline-block;
        transition: all 0.3s ease;
        padding: 2px 6px;
    }
    
    .cart-count.updating {
        animation: cartPulse 0.5s ease;
    }
    </style>
    <?php
}
add_action('wp_footer', 'neuf_mois_cart_counter_script');

//
function woo_remove_product_tabs( $tabs ) {
//    unset( $tabs['description'] );      // Rimuove la scheda Descrizione
    unset( $tabs['reviews'] );          // Rimuove la scheda Recensioni
//    unset( $tabs['additional_information'] ); // Rimuove la scheda Informazioni aggiuntive

    return $tabs;
}

//add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

// Disabilita le pagine di tassonomia per i tag di prodotto
add_action( 'init', 'disable_product_tag_taxonomy', 99 );
function disable_product_tag_taxonomy() {
    unregister_taxonomy_for_object_type( 'product_tag', 'product' );
}

// Disabilita le pagine di tassonomia per le categorie di prodotto
add_action( 'init', 'disable_product_category_taxonomy', 99 );
function disable_product_category_taxonomy() {
    unregister_taxonomy_for_object_type( 'product_cat', 'product' );
}

// Disabilita le pagine di tassonomia per gli attributi di prodotto
add_action( 'init', 'disable_product_attribute_taxonomies', 99 );
function disable_product_attribute_taxonomies() {
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    if ( $attribute_taxonomies ) {
        foreach ( $attribute_taxonomies as $tax ) {
            unregister_taxonomy_for_object_type( $tax->attribute_name, 'product' );
        }
    }
}

function redirect_single_posts_to_home() {
    if (is_single() && get_post_type() === 'post') {
        // Controlla se la pagina è un singolo post e il post type è "post"
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'redirect_single_posts_to_home');

add_theme_support( 'title-tag' );
add_theme_support( 'custom-logo', array(
    'height' => 480,
    'width'  => 720,
) );

function custom_shipping_cost( $cost, $method ) {
    $cart_items = WC()->cart->get_cart_contents_count();
    
    // Recupera il costo originale impostato in WooCommerce
    $original_cost = $cost;

    // Calcola il nuovo costo basato sul numero di articoli nel carrello
    $new_cost = $original_cost * ceil($cart_items / 2);
    
    return $new_cost;
}
add_filter( 'woocommerce_shipping_rate_cost', 'custom_shipping_cost', 10, 2 );

add_action('init', function() {
    if (class_exists('WooCommerce')) {
        // Disabilita Object Cache per WooCommerce
        if (defined('WP_CACHE')) {
            define('DONOTCACHEPAGE', true);
            define('DONOTCACHEOBJECT', true);
            define('DONOTCACHEDB', true);
        }
        
        // Headers anti-cache
        if (is_woocommerce() || is_cart() || is_checkout() || wp_doing_ajax()) {
            nocache_headers();
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }
    }
}, 1);

// Bypass Cache Enabler per WooCommerce
add_filter('cache_enabler_bypass_cache', function() {
    if (is_woocommerce() || is_cart() || is_checkout()) {
        return true;
    }
    if (isset($_GET['wc-ajax'])) {
        return true;
    }
    return false;
});

// Disabilita cache per AJAX
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', function() {
    nocache_headers();
}, 1);
add_action('wp_ajax_woocommerce_ajax_add_to_cart', function() {
    nocache_headers();
}, 1);

add_action('test_simple_action', function() {
    // Scrive nel log di WordPress
    error_log('🎉 TEST SCHEDULED ACTION FUNZIONA! - Ora: ' . date('Y-m-d H:i:s'));
    
    // Scrive anche nel log di debug se attivo
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('✅ Debug: La scheduled action è stata eseguita correttamente');
    }
    
    // Per essere sicuri, scriviamo anche in un file custom
    $log_message = date('Y-m-d H:i:s') . " - Scheduled action eseguita!\n";
    file_put_contents(WP_CONTENT_DIR . '/test-scheduled.log', $log_message, FILE_APPEND);
});

// Sostituisci la tua action cleanup_expired_transients con questa versione completa
add_action('cleanup_expired_transients', function() {
    
    // 1. Pulisce tutti i transients scaduti
    delete_expired_transients();
    
    // 2. Pulisce transients WooCommerce specifici
    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients();
    }
    
    // 3. Pulisce la TEMPLATE CACHE
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
        error_log('🗑️ Object cache pulita');
    }
    
    // 4. Pulisce la cache dei template WooCommerce
    if (class_exists('WC_Cache_Helper')) {
        WC_Cache_Helper::get_transient_version('shipping', true);
        error_log('🗑️ WooCommerce template cache pulita');
    }
    
    // 5. Pulisce eventuali file di cache compilati
    $cache_dir = WP_CONTENT_DIR . '/cache';
    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . '/*.php');
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
            error_log('🗑️ File cache template eliminati: ' . count($files));
        }
    }
    
    // 6. Pulisce la cache di Cache Enabler (se presente)
    if (class_exists('Cache_Enabler')) {
        Cache_Enabler::clear_total_cache();
        error_log('🗑️ Cache Enabler pulita');
    }
    
    // 7. Log finale
    error_log('✅ Cleanup completo alle: ' . date('Y-m-d H:i:s'));
    
    $log_message = date('Y-m-d H:i:s') . " - Cleanup completo (transients + template cache)!\n";
    file_put_contents(WP_CONTENT_DIR . '/cleanup-log.log', $log_message, FILE_APPEND);
});

// Aggiunge un parametro random alle pagine WooCommerce per bypassare la cache di GoDaddy
add_filter('woocommerce_get_cart_url', 'add_cache_buster_to_cart');
add_filter('woocommerce_get_checkout_url', 'add_cache_buster_to_checkout');
add_filter('woocommerce_get_shop_url', 'add_cache_buster_to_shop');

function add_cache_buster_to_cart($url) {
    return add_query_arg('ph', time(), $url);
}

function add_cache_buster_to_checkout($url) {
    return add_query_arg('ph', time(), $url);
}

function add_cache_buster_to_shop($url) {
    return add_query_arg('ph', time(), $url);
}

// Aggiunge anche ai link "Add to Cart" per forzare il refresh
add_filter('woocommerce_loop_add_to_cart_link', function($html, $product) {
    $url = $product->add_to_cart_url();
    $url = add_query_arg('ph', time(), $url);
    
    return str_replace($product->add_to_cart_url(), $url, $html);
}, 10, 2);