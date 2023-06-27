<?php
/**
 * The main template file
 *
 * @package Neuf-mois
 */

get_header(); ?>

<div class="content-area">
    <main>
        <section class="main-products">
            <div class="container">
                <div class="row">
                    My e-book
                </div>
            </div>
        </section>
        <section class="slider">
            <div class="container">
                <div class="row">
                    Slider
                </div>
            </div>
        </section>
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
            </div>
        </section>
    </main>
</div>

<?php get_footer(); ?>
