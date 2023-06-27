<?php
/*
 * Template Name: Home PAge
 */
get_header()
?>


        <section class="info-blog">
            <div class="container">
                <div class="row justify-content-center">
                    <?php
                    if( have_posts()):
                        while (have_posts()) : the_post();
                            ?>
                            <article>
                                <h2><?php the_title() ?></h2>
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

                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <?php
                        // obtenir les publications de la page d'accueil
                        $posts = get_posts( array(
                            'category_name' => 'post-home-page',
                            'posts_per_page' => -1,  // Pour obtenir tous les posts. Changez ce nombre pour limiter le nombre de posts
                        ) );

                        // commencer la boucle
                        if ( !empty($posts) ) :
                            foreach ( $posts as $post ) : setup_postdata( $post );
                                ?>
                                <article class="col-12 col-sm-4">
                                    <h2><?php the_title(); ?></h2>
                                    <div><?php the_content() ?></div>
                                </article>
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


