<?php

/**
 * Template Name: Safeguarding-Welfare
 */

get_header();
?>

<div class="site-main template-safeguarding-welfare">

    <?php
    get_template_part(
        'template-parts/sections/two-column-section',
        null,
        [
            'image_alt'    => 'Officials image',
            'reverse'      => false
        ]
    );
    ?>


    <section class="officials-info-1">
        <?php get_template_part('template-parts/sections/container-info', null); ?>
    </section>


    <section class="container">
        <?php
        get_template_part('template-parts/sections/concern-block', null, [
            'wo_anchor_id' => 'welfare-officer', // welfare officer section id (scrolls to it)
            'wo_link_step' => 2 // step after which to insert the link
        ]);
        ?>
    </section>

    <section id="welfare-officer">
        <?php get_template_part('template-parts/grids/cards-grid', null); ?>
    </section>


</div>

<?php get_footer(); ?>