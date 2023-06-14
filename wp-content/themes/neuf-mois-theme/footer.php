<?php
/**
 * The footer template file
 *
 * @package Neuf-mois
 */

?>
            <footer>
                <section class="footer-widgets">
                    <div class="container">
                        <div class="raw">
                            Footer widgets
                        </div>
                    </div>
                </section>
                <section class="copyright">
                    <div class="container">
                        <div class="row">
                            <div class="copyright-text col-12 col-md-6"> Copyright</div>
                            <nav class="footer-menu col-12 col-md-6 text-star text-md-end">
                                <?php
                                wp_nav_menu(
                                    array(
                                        'theme_location' => 'neuf_mois_footer_menu'
                                    )
                                );
                                ?>
                            </nav>
                        </div>
                    </div>
                </section>
            </footer>
        </div>
    <?php wp_footer(); ?>
    </body>
</html>