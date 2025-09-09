<?php
/**
 * Template Name: Officials
 */

get_header();
?>

<div class="site-main template-officials">

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

  <section>
    <?php get_template_part('template-parts/grids/cards-grid', null); ?>
  </section>


  <div class="officials-info-wrapper">
    <section class="officials-info-1">
      <?php get_template_part('template-parts/sections/container-info', null); ?>
    </section>

    <section class="officials-info-2">
      <?php get_template_part('template-parts/sections/container-info', null, ['version' => '2']); ?>
    </section>
  </div>

  <section>
      <?php get_template_part('template-parts/sections/multiple-buttons', null); ?>
  </section>

</div>


<?php get_footer(); ?>
