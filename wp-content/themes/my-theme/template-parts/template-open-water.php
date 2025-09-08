<?php
/**
 * Template Name: Open Water
 */
get_header();

// Optional order parsing (if your cards-grid uses it)
$order_str = (string) get_field('role_order');
$order_tokens = array_values(array_filter(array_map(function($t){
  $t = trim($t);
  $t = preg_replace('/^\(contains\)\s*/i', '', $t);
  $t = mb_strtolower($t);
  return $t;
}, preg_split('/[\r\n,]+/', $order_str) ?: [])));
natcasesort($order_tokens);
$order_tokens = array_values(array_unique($order_tokens));

?>

<div class="site-main template-open-water">

    <div class="container">
        <div class="container-content">
            <h2><?php the_field('header'); ?></h2>
            <p><?php the_field('text'); ?></p>
        </div>
    </div>

    <?php
    get_template_part('template-parts/grids/tab-grid', null, [
        'aria_label'      => 'Open Water',
        'sidebar_heading' => 'Trophies',
        'active'          => 'all',
        'order_tokens'    => $order_tokens,
        'id_base'         => 'open-water',
        'tabs' => [
            ['slug'=>'all',       'label'=>'All',       'panel'=>['type'=>'cards','prefix'=>'all']],
        ],
    ]);
    ?>


</div>

<?php get_footer(); ?>
