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
                <div class="navbar-brand">
                    <a class="navbar-item" href="<?php echo esc_url(home_url('/')); ?>">
                        <?php
                        $logo_image_url = get_template_directory_uri() . '/img/logo-cigogne.png';
                        ?>
                        <img src="<?php echo esc_url($logo_image_url); ?>" alt="logo of cigogne">
                    </a>

                    <a role="button" class="navbar-burger " aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                    </a>
                </div>

                <div id="navbarBasicExample" class="navbar-menu">
                    <div class="navbar-start ">
                        <ul class="is-hidden-mobile is-flex is-flex-direction-row is-align-items-center">
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

                    <div class="navbar-end is-hidden-mobile">
                        <div class="navbar-item">
                            <?php do_action('woocommerce_before_mini_cart'); ?>
                            <a class="cart-button" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                                <span class="cart-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                    <small>
                                        <?php echo WC()->cart->get_cart_contents_count(); ?>
                                    </small>
                                </span>
                            </a>
                        </div>
                    </div>


                </div>
            </nav>
        </div>
    </header>