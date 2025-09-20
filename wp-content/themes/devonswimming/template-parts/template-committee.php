<?php
/**
 * Template Name: Committees
 */
get_header();

// Role order parsing (unchanged)
$order_str = (string) get_field('role_order');
$order_tokens = array_values(array_filter(array_map(function($t){
  $t = trim($t);
  $t = preg_replace('/^\(contains\)\s*/i', '', $t);
  $t = mb_strtolower($t);
  return $t;
}, preg_split('/[\r\n,]+/', $order_str) ?: [])));
?>
<div class="site-main template-committees">
  <div class="container">
    <div class="container-content">
      <h2><?php the_field('header'); ?></h2>
      <p><?php the_field('text'); ?></p>
    </div>
  </div>

  <?php
  get_template_part('template-parts/grids/tab-grid', null, [
    'aria_label'      => 'Committees',
    'sidebar_heading' => null,
    'active'          => 'management',
    'order_tokens'    => $order_tokens,
    'id_base'         => 'committees',
    'hash_cleanup'    => [
      'keep_slug' => 'past_presidents',
      'regex'     => '^#decade-\d{4}$|^#decade-unknown$'
    ],
    'tabs' => [
      ['slug'=>'management',     'label'=>'Management Committee',  'panel'=>['type'=>'cards','prefix'=>'management']],
      ['slug'=>'swimming',       'label'=>'Swimming Committee',    'panel'=>['type'=>'cards','prefix'=>'swimming']],
      ['slug'=>'para_swimming',  'label'=>'Para Swimming Committee','panel'=>['type'=>'cards','prefix'=>'para_swimming']],
      ['slug'=>'masters',        'label'=>'Masters Committee',     'panel'=>['type'=>'cards','prefix'=>'masters']],
      ['slug'=>'open_water',     'label'=>'Open Water Committee',  'panel'=>['type'=>'cards','prefix'=>'open_water']],
      ['slug'=>'water_polo',     'label'=>'Water Polo Committee',  'panel'=>['type'=>'cards','prefix'=>'water_polo']],
      ['slug'=>'emergency',      'label'=>'Emergency Committee',   'panel'=>['type'=>'cards','prefix'=>'emergency']],
      ['slug'=>'past_presidents','label'=>'Past Presidents',       'panel'=>['type'=>'template_part','template'=>'template-parts/presidents-timeline']],
    ],
  ]);
  ?>
</div>
<?php get_footer(); ?>
