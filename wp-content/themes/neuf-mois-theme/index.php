<?php
/**
 * The main template file
 *
 * @package Neuf-mois
 */

get_header(); ?>

<div class="content-area">
    <main>
        <section class="info-blog section">
            <div class="container">
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
        </section>
    </main>
</div>

<?php get_footer(); ?>


