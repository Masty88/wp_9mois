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
        <section class="welcome">
            <div class="container">
                Bienvenue dans ma boutique en ligne!
            </div>
        </section>
        <section class="top-bar">
            <div class="container">
                <div class="row">
                    <div class="brand col-3 col-sm-6">Logo</div>
                    <div class="second-column col-9 col-sm-6">
                        <div class="account">Account</div>
                        <nav class="main-menu">
                        <?php
                        wp_nav_menu(
                            array(
                                   'theme_location' => 'neuf_mois_main_menu'
                            )
                        );
                        ?>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
    </header>