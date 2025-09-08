<?php
/**
 * 
 */

// For reversing flex direction
$reverse_layout = !empty($args['reverse']) ? 'reverse-layout' : '';

?>


<?php
$title       = get_field('commitment_title') ?: 'Safeguarding & Welfare';
$intro       = get_field('commitment_intro');
$follow_text = get_field('commitment_follow_text');
?>
<section class="safeguarding-section safeguarding-commitment <?php echo esc_attr($reverse_layout); ?>">
  <div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>
    <?php if ($intro): ?>
      <p><?php echo wp_kses_post($intro); ?></p>
    <?php endif; ?>
    <?php if ($follow_text): ?>
      <p><?php echo wp_kses_post($follow_text); ?></p>
    <?php endif; ?>
  </div>
</section>
