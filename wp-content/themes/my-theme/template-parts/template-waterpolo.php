<?php
/**
 * Template Name: Water Polo
 */
get_header(); 

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

<div class="site-main template-water-polo">


    <?php
    get_template_part(
        'template-parts/sections/two-column-section',
        null,
        [
            'image_alt'    => 'Water polo player scoring a goal',
            'button_label' => 'Website',
        ]
    );
    ?>

    <?php
    get_template_part('template-parts/grids/tab-grid', null, [
        'aria_label'      => 'Water Polo',
        'sidebar_heading' => 'Trophies',
        'active'          => 'leagues',
        'order_tokens'    => $order_tokens,
        'id_base'         => 'water-polo',
        'tabs' => [
            ['slug'=>'leagues',       'label'=>'Leagues',       'panel'=>['type'=>'cards','prefix'=>'leagues']],
            ['slug'=>'knockouts',       'label'=>'Knockouts',       'panel'=>['type'=>'cards','prefix'=>'knockouts']],
            ['slug'=>'tournaments',       'label'=>'Tournaments',       'panel'=>['type'=>'cards','prefix'=>'tournaments']],
            ['slug'=>'player_awards',       'label'=>'Player Awards',       'panel'=>['type'=>'cards','prefix'=>'player_awards']],
        ],
    ]);
    ?>


</div>

<?php get_footer(); ?>
