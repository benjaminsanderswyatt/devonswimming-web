<?php

/**
 * Template Name: Para Swimming
 */
get_header();

/** -------- Helpers (same as Swimming) -------- */
$g = static function (string $key): array {
    return function_exists('get_field') ? ((array) get_field($key) ?: []) : [];
};
$t = static function (?string $s): string {
    return trim((string) $s);
};
$img_src = static function ($img): string {
    if (is_array($img)) return (string)($img['url'] ?? '');
    return (string)$img;
};
$img_alt = static function ($img, string $fallback = ''): string {
    if (is_array($img)) return (string)($img['alt'] ?? $fallback);
    return $fallback;
};

/** -------- Fetch groups (no intro) -------- */
$help  = $g('para_help');   // para_help_heading, para_help_item_1..6
$start = $g('para_start');  // para_start_heading, para_start_text, para_contact_email

/** -------- Optional images (same field names as Swimming) -------- */
$image_1 = function_exists('get_field') ? get_field('image_1') : '';
$image_2 = function_exists('get_field') ? get_field('image_2') : '';
$image_3 = function_exists('get_field') ? get_field('image_3') : '';
?>

<div class="site-main template-para-swimming">

    <!-- Header -->
    <?php
    get_template_part(
        'template-parts/sections/two-column-section',
        null,
        [
            'image_alt'    => 'Swimmer',
        ]
    );
    ?>







    <?php
    // Build card content (mirrors Swimming structure)
    $help_heading = $t($help['para_help_heading'] ?? '');
    $help_items   = [];
    for ($i = 1; $i <= 6; $i++) {
        $val = $t($help["para_help_item_$i"] ?? '');
        if ($val !== '') $help_items[] = $val;
    }

    $start_heading = $t($start['para_start_heading'] ?? '');
    $start_text    = $start['para_start_text'] ?? '';
    $start_email   = $t($start['para_contact_email'] ?? '');

    $has_any = ($help_heading || $help_items || $start_heading || $start_text || $start_email || $image_1 || $image_2 || $image_3);

    if ($has_any): ?>
        <div class="swim-row">
            <?php if ($help_heading || $help_items): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($help_heading): ?><h2><?php echo esc_html($help_heading); ?></h2><?php endif; ?>
                        <?php if ($help_items): ?>
                            <ul>
                                <?php foreach ($help_items as $li): ?>
                                    <li><?php echo esc_html($li); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($image_1): ?>
                <figure class="sr-item swim-image">
                    <img src="<?php echo esc_url($img_src($image_1)); ?>"
                        alt="<?php echo esc_attr($img_alt($image_1, 'Para Swimming in Devon')); ?>">
                </figure>
            <?php endif; ?>

            <?php if ($start_heading || $start_text || $start_email): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($start_heading): ?><h2><?php echo esc_html($start_heading); ?></h2><?php endif; ?>
                        <?php if ($start_text): ?><div class="about-copy"><?php echo wp_kses_post($start_text); ?></div><?php endif; ?>
                        <?php if ($start_email): ?>
                            <p>
                                <a class="chip" href="mailto:<?php echo antispambot($start_email); ?>">
                                    <?php echo antispambot($start_email); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($image_2): ?>
                <figure class="sr-item swim-image">
                    <img src="<?php echo esc_url($img_src($image_2)); ?>"
                        alt="<?php echo esc_attr($img_alt($image_2, 'Inclusive coaching')); ?>">
                </figure>
            <?php endif; ?>

            <?php if ($image_3): ?>
                <figure class="sr-item swim-image">
                    <img src="<?php echo esc_url($img_src($image_3)); ?>"
                        alt="<?php echo esc_attr($img_alt($image_3, 'Accessible competition')); ?>">
                </figure>
            <?php endif; ?>
        </div>
    <?php endif; ?>













</div>

<?php get_footer(); ?>