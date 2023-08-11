<?php
/**
 * The header template file
 *
 * @package Neuf-mois
 */

get_header(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="page" class="site">
    <header>
        <div class="container">

        </div>
        <div class="container mt-3">
            <nav class="navbar navbar-desktop-margin" role="navigation" aria-label="main navigation">
                <div class="navbar-brand is-justify-content-space-between">

                    <a role="button" class="navbar-burger " aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                    </a>

                    <a class="navbar-item" href="<?php echo esc_url(home_url('/')); ?>">
                        <?php
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );

                        if ( has_custom_logo() ) {
                            echo '<img src="'. esc_url( $logo[0] ) .'" alt="logo of cigogne">';
                        } else {
                            echo '<h1>'. get_bloginfo( 'name' ) .'</h1>';
                        }
                        ?>
                    </a>
                    <div class="navbar-item is-hidden-desktop mr-3">
<!--                        --><?php //do_action('woocommerce_before_mini_cart'); ?>
                        <a class="cart-button" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                                <span class="cart-icon">
                                     <?php $items_count = WC()->cart->get_cart_contents_count()?>
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="cart-count">
                                        <?php echo $items_count ? $items_count : '0'; ?>
<!--                                        --><?php //echo wp_kses_post(WC()->cart->get_cart_contents_count()); ?>
                                    </span>
                                </span>
                        </a>
                    </div>

                </div>


                <div id="navbarBasicExample" class="navbar-menu">
                    <div class="navbar-start ">
                        <ul class="menu-desk is-hidden-mobile is-hidden-touch is-flex is-flex-direction-row is-align-items-center">
                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'neuf_mois_main_menu',
                                    'container' => false,
                                    'items_wrap' => '%3$s'
                                )
                            );
                            ?>
                        </ul>

                        <ul class="menu-mobile is-hidden-desktop is-flex is-flex-direction-column is-justify-content-center is-align-items-center">
                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'neuf_mois_main_menu',
                                    'container' => false,
                                    'items_wrap' => '%3$s'
                                )
                            );
                            ?>
                        </ul>
                    </div>

                    <div class="navbar-end is-hidden-mobile is-hidden-touch">
                        <div class="navbar-item">
                            <?php do_action('woocommerce_before_mini_cart'); ?>
                            <a class="cart-button" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                                <span class="cart-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="cart-count"><?php echo wp_kses_post(WC()->cart->get_cart_contents_count()); ?></span>
                                </span>
                            </a>
                        </div>
                    </div>


                </div>
            </nav>
        </div>
    </header>