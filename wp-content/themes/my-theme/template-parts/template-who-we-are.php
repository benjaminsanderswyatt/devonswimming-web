<?php
/**
 * Template Name: Who We Are
 */
get_header();

/** -------- Helpers -------- */
$group = static function (string $key): array {
    return function_exists('get_field') ? ((array) get_field($key) ?: []) : [];
};
$section_id = static function (string $fallback, ?string $heading = null): string {
    $base = $heading ? sanitize_title($heading) : sanitize_title($fallback);
    return $base ?: sanitize_title($fallback);
};

/** -------- Fetch ACF groups (exclude who_we_are & what_we_do) -------- */
$values     = $group('our_values');                // header, text
$organised  = $group('how_were_organised');        // header, text, link, link_label
$events     = $group('competitions_and_events');   // header, text, link, link_label
$support    = $group('support_and_bursaries');     // header, text, link, link_label
$safeguard  = $group('safeguarding_and_welfare');  // header, text, link, link_label
?>

<div class="site-main template-about-us">

    <?php
    // ===== Hero / Two-column section at the top =====
    get_template_part(
        'template-parts/sections/two-column-section',
        null,
        [
            'image_alt' => 'Devon Swimming ASA image',
            'reverse'   => false
        ]
    );

    // Reusable renderer for remaining sections (H2 + text + optional CTA)
    $render_section = static function(array $g, string $fallback_id) use ($section_id) {
        if (empty($g)) return;
        $has = !empty($g['header']) || !empty($g['text']) || !empty($g['link']);
        if (!$has) return;

        $id = $section_id($fallback_id, $g['header'] ?? null);
        ?>
        <section class="container about-block" aria-labelledby="<?php echo esc_attr($id); ?>">
            <div class="container-content">
                <?php if (!empty($g['header'])): ?>
                    <h2 id="<?php echo esc_attr($id); ?>"><?php echo esc_html($g['header']); ?></h2>
                <?php endif; ?>

                <?php if (!empty($g['text'])): ?>
                    <div class="about-copy">
                        <?php echo wp_kses_post($g['text']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($g['link'])): ?>
                    <div class="about-cta">
                        <a class="chip" href="<?php echo esc_url($g['link']); ?>">
                            <?php echo esc_html($g['link_label'] ?: 'Learn more'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    };

    // ===== Our Values =====
    $render_section($values, 'our-values');

    // ===== How We’re Organised =====
    $render_section($organised, 'how-were-organised');

    // ===== Competitions & Events =====
    $render_section($events, 'competitions-and-events');

    ?>

</div>

<?php get_footer(); ?>
