<?php
/**
 * The footer template file
 *
 * @package Neuf-mois
 */

?>
<footer class="footer has-background-black has-text-white">
    <div class="container">
        <div class="columns">
            <div class="column is-hidden-desktop">
                <section class="copyright">
                    <nav class="footer-menu is-flex is-justify-content-center">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'neuf_mois_footer_menu'
                            )
                        );
                        ?>
                    </nav>
                </section>
            </div>
            <div class="column has-text-centered">
                    <nav class="footer-menu is-flex is-justify-content-center mb-5 is-hidden-mobile">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'neuf_mois_footer_menu'
                            )
                        );
                        ?>
                    </nav>
                <p class="mb-3 ">Méthodes de paiement acceptées:</p>
                <div class="columns is-mobile">
                    <div class="column is-flex is-justify-content-center">
                        <img class="mr-3" src="<?php echo get_template_directory_uri(); ?>/img/logo/visa.svg" width="40" alt="Visa">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/logo/mastercard.svg" width="40" alt="MasterCard">
                    </div>
                </div>
                <p class="mt-3 ">© 9 mois aux petits soins all right reserved.</p>
                <p class="mt-3 ">Developped by Emanuele Mastaglia</p>
            </div>
        </div>
    </div>
</footer>

</div>
    <?php wp_footer(); ?>
    </body>
</html>