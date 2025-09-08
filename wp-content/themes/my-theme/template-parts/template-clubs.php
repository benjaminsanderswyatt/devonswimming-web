<?php
/**
 * Template Name: Clubs
 */
get_header(); ?>

<div class="site-main template-clubs">


     <?php
    get_template_part(
        'template-parts/sections/two-column-section',
        null,
        [
            'image_alt'    => 'Club promotion image',
            'reverse'      => true
        ]
    );
    ?>




    <?php
        get_template_part('template-parts/grids/cards-grid', null);
    ?>




</div>

<?php get_footer(); ?>
