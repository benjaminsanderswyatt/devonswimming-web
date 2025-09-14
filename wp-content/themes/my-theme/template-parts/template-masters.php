<?php

/**
 * Template Name: Masters
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
$find = $g('masters_find');     // masters_find_heading, masters_find_item_1..6
$join = $g('masters_return');   // masters_return_heading, masters_return_text
$faqs = $g('masters_faqs');     // masters_faqs_heading, masters_faq_q_1..6, masters_faq_a_1..6

/** -------- Optional images (same field names as Swimming) -------- */
$image_1 = function_exists('get_field') ? get_field('image_1') : '';
$image_2 = function_exists('get_field') ? get_field('image_2') : '';
$image_3 = function_exists('get_field') ? get_field('image_3') : '';
?>

<div class="site-main template-masters">

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
    // Build card content (matches Swimming structure)
    $find_heading = $t($find['masters_find_heading'] ?? '');
    $find_items   = [];
    for ($i = 1; $i <= 6; $i++) {
        $val = $t($find["masters_find_item_$i"] ?? '');
        if ($val !== '') $find_items[] = $val;
    }

    $join_heading = $t($join['masters_return_heading'] ?? '');
    $join_text    = $join['masters_return_text'] ?? '';

    $has_any = ($find_heading || $find_items || $join_heading || $join_text || $image_1 || $image_2 || $image_3);

    if ($has_any): ?>
        <div class="swim-row">
            <?php if ($find_heading || $find_items): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($find_heading): ?><h2><?php echo esc_html($find_heading); ?></h2><?php endif; ?>
                        <?php if ($find_items): ?>
                            <ul>
                                <?php foreach ($find_items as $li): ?>
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
                        alt="<?php echo esc_attr($img_alt($image_1, 'Masters training in Devon')); ?>">
                </figure>
            <?php endif; ?>

            <?php if ($join_heading || $join_text): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($join_heading): ?><h2><?php echo esc_html($join_heading); ?></h2><?php endif; ?>
                        <?php if ($join_text): ?><div class="about-copy"><?php echo wp_kses_post($join_text); ?></div><?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($image_2): ?>
                <figure class="sr-item swim-image">
                    <img src="<?php echo esc_url($img_src($image_2)); ?>"
                        alt="<?php echo esc_attr($img_alt($image_2, 'Masters meets in Devon')); ?>">
                </figure>
            <?php endif; ?>

            <?php if ($image_3): ?>
                <figure class="sr-item swim-image">
                    <img src="<?php echo esc_url($img_src($image_3)); ?>"
                        alt="<?php echo esc_attr($img_alt($image_3, 'Masters community')); ?>">
                </figure>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    // FAQs (same structure as Swimming)
    $faq_heading = $t($faqs['masters_faqs_heading'] ?? '');
    $faq_pairs   = [];
    for ($i = 1; $i <= 6; $i++) {
        $q = $t($faqs["masters_faq_q_$i"] ?? '');
        $a = $faqs["masters_faq_a_$i"] ?? '';
        if ($q || $a) $faq_pairs[] = ['q' => $q, 'a' => $a];
    }

    if ($faq_heading || $faq_pairs): ?>
        <section class="container faq-block" itemscope itemtype="https://schema.org/FAQPage">
            <div class="container-content">
                <?php if ($faq_heading): ?>
                    <h2><?php echo esc_html($faq_heading); ?></h2>
                <?php endif; ?>

                <?php if ($faq_pairs): ?>
                    <div class="faqs">
                        <?php foreach ($faq_pairs as $idx => $pair):
                            $q     = $pair['q'] ?: 'Question';
                            $slug  = sanitize_title($q) ?: ('faq-' . ($idx + 1));
                            $ans   = $pair['a'];
                        ?>
                            <details class="faq-item" id="<?php echo esc_attr($slug); ?>" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                <summary class="faq-summary">
                                    <span class="faq-q" itemprop="name"><?php echo esc_html($q); ?></span>
                                </summary>
                                <?php if (!empty($ans)): ?>
                                    <div class="faq-a" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                        <div class="answer" itemprop="text"><?php echo wp_kses_post($ans); ?></div>
                                    </div>
                                <?php endif; ?>
                            </details>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>













</div>

<?php get_footer(); ?>