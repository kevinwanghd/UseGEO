<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="usegeo-shell usegeo-shell--narrow">
    <div class="usegeo-card">
        <?php
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                ?>
                <article <?php post_class(); ?>>
                    <h1><?php the_title(); ?></h1>
                    <div>
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            }
        }
        ?>
    </div>
</main>
<?php
get_footer();
