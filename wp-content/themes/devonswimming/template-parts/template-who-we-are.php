<?php
/**
 * Template Name: Who We Are
 */
get_header();

/** Helpers */
$group = static function (string $key): array {
    return function_exists('get_field') ? ((array) get_field($key) ?: []) : [];
};
$section_id = static function (string $fallback, ?string $heading = null): string {
    $base = $heading ? sanitize_title($heading) : sanitize_title($fallback);
    return $base ?: sanitize_title($fallback);
};

/** ACF groups */
$values    = $group('our_values');          // header, text
$organised = $group('how_were_organised');  // header, text, link, link_label
?>

<div class="site-main template-about-us">

  <?php
  // Hero / Two-column
  get_template_part(
      'template-parts/sections/two-column-section',
      null,
      [
          'image_alt' => 'Devon Swimming ASA image',
          'reverse'   => false
      ]
  );
  ?>

  <?php
  $has_values = !empty($values['header']) || !empty($values['text']);
  $has_org    = !empty($organised['header']) || !empty($organised['text']) || !empty($organised['link']);
  if ($has_values || $has_org): ?>
    <div class="about-row">
      <?php if ($has_values):
        $values_id = $section_id('our-values', $values['header'] ?? null); ?>
        <section class="container about-block ar-item" aria-labelledby="<?php echo esc_attr($values_id); ?>">
          <div class="container-content">
            <?php if (!empty($values['header'])): ?>
              <h2 id="<?php echo esc_attr($values_id); ?>"><?php echo esc_html($values['header']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($values['text'])): ?>
              <div class="about-copy">
                <?php echo wp_kses_post($values['text']); ?>
              </div>
            <?php endif; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($has_org):
        $org_id = $section_id('how-were-organised', $organised['header'] ?? null); ?>
        <section class="container about-block ar-item" aria-labelledby="<?php echo esc_attr($org_id); ?>">
          <div class="container-content">
            <?php if (!empty($organised['header'])): ?>
              <h2 id="<?php echo esc_attr($org_id); ?>"><?php echo esc_html($organised['header']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($organised['text'])): ?>
              <div class="about-copy">
                <?php echo wp_kses_post($organised['text']); ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($organised['link'])): ?>
              <div class="about-cta">
                <a class="chip" href="<?php echo esc_url($organised['link']); ?>">
                  <?php echo esc_html($organised['link_label'] ?: 'Learn more'); ?>
                </a>
              </div>
            <?php endif; ?>
          </div>
        </section>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</div>

<?php get_footer(); ?>
