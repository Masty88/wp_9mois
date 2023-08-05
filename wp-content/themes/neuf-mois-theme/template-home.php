<?php
/*
 * Template Name: Home PAge
 */
get_header()
?>


        <section class="info-blog m-3">
            <div class="container">
                <div class="block">
                    <?php
                    if( have_posts()):
                        while (have_posts()) : the_post();
                            ?>
                            <article>
                                <div><?php the_content(); ?></div>
                            </article>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <p> Nothing</p>
                    <?php
                    endif;
                    ?>
                </div>

                <div class="block">
                    <div class="columns">
                        <?php
                        // obtenir les publications de la page d'accueil
                        $posts = get_posts( array(
                            'category_name' => 'post-home-page',
                            'posts_per_page' => -1,// Pour obtenir tous les posts. Changez ce nombre pour limiter le nombre de posts
                            'orderby' => 'menu_order',
                            'order' => 'ASC'
                        ) );

                        // commencer la boucle
                        if ( !empty($posts) ) :
                            foreach ( $posts as $post ) : setup_postdata( $post );
                                ?>
                            <div class="column">
                                <article class=" blog border-black mt-3">
                                    <div><?php the_content() ?></div>
                                </article>
                            </div>
                            <?php
                            endforeach;
                            // réinitialiser les données de la publication après une boucle personnalisée
                            wp_reset_postdata();
                        else:
                            ?>
                            <p>Nothing to display</p>
                        <?php
                        endif;
                        ?>
                    </div>
                </div>

            </div>
        </section>
    </main>
</div>

<?php get_footer(); ?>


