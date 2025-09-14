<?php

/**
 * Template Name: Swimming
 */
get_header();


/** -------- Helpers -------- */
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

/** -------- Fetch groups -------- */
$what      = $g('swim_what');
$pathways  = $g('swim_pathways');
$involved  = $g('swim_involved');
$faqs      = $g('swim_faqs');

/** -------- Images (own ACF fields) -------- */
$image_1 = function_exists('get_field') ? get_field('image_1') : '';
$image_2 = function_exists('get_field') ? get_field('image_2') : '';
$image_3 = function_exists('get_field') ? get_field('image_3') : '';







// Optional order parsing (if your cards-grid uses it)
$order_str = (string) get_field('role_order');
$order_tokens = array_values(array_filter(array_map(function ($t) {
    $t = trim($t);
    $t = preg_replace('/^\(contains\)\s*/i', '', $t);
    $t = mb_strtolower($t);
    return $t;
}, preg_split('/[\r\n,]+/', $order_str) ?: [])));
natcasesort($order_tokens);
$order_tokens = array_values(array_unique($order_tokens));

?>

<div class="site-main template-swimming">

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
    // --- Build the three text sections ---
    $wwd_heading = $t($what['swim_what_heading'] ?? '');
    $wwd_items   = [];
    for ($i = 1; $i <= 6; $i++) {
        $val = $t($what["swim_what_item_$i"] ?? '');
        if ($val !== '') $wwd_items[] = $val;
    }

    $pw_heading = $t($pathways['swim_pathways_heading'] ?? '');
    $pw_text    = $pathways['swim_pathways_text'] ?? '';

    $gi_heading = $t($involved['swim_involved_heading'] ?? '');
    $gi_items   = [];
    for ($i = 1; $i <= 6; $i++) {
        $val = $t($involved["swim_involved_item_$i"] ?? '');
        if ($val !== '') $gi_items[] = $val;
    }

    $has_any_features = ($wwd_heading || $wwd_items || $pw_heading || $pw_text || $gi_heading || $gi_items || $image_1 || $image_2 || $image_3);

    if ($has_any_features): ?>
        <div class="swim-row">
            <?php if ($wwd_heading || $wwd_items): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($wwd_heading): ?><h2><?php echo esc_html($wwd_heading); ?></h2><?php endif; ?>
                        <?php if ($wwd_items): ?>
                            <ul>
                                <?php foreach ($wwd_items as $li): ?>
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
                        alt="<?php echo esc_attr($img_alt($image_1, 'Swimming in Devon')); ?>">
                </figure>
            <?php endif; ?>

            <?php if ($pw_heading || $pw_text): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($pw_heading): ?><h2><?php echo esc_html($pw_heading); ?></h2><?php endif; ?>
                        <?php if ($pw_text): ?><div class="about-copy"><?php echo wp_kses_post($pw_text); ?></div><?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($image_2): ?>
                <figure class="sr-item swim-image">
                    <img src="<?php echo esc_url($img_src($image_2)); ?>"
                        alt="<?php echo esc_attr($img_alt($image_2, 'Devon ASA swimmers')); ?>">
                </figure>
            <?php endif; ?>

            <?php if ($gi_heading || $gi_items): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($gi_heading): ?><h2><?php echo esc_html($gi_heading); ?></h2><?php endif; ?>
                        <?php if ($gi_items): ?>
                            <ul>
                                <?php foreach ($gi_items as $li): ?>
                                    <li><?php echo esc_html($li); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($image_3): ?>
                <figure class="sr-item swim-image">
                    <img src="<?php echo esc_url($img_src($image_3)); ?>"
                        alt="<?php echo esc_attr($img_alt($image_3, 'Competition in Devon')); ?>">
                </figure>
            <?php endif; ?>
        </div>
    <?php endif; ?>








    <?php
    // FAQs
    $faq_heading = $t($faqs['swim_faqs_heading'] ?? '');
    $faq_pairs   = [];
    for ($i = 1; $i <= 6; $i++) {
        $q = $t($faqs["swim_faq_q_$i"] ?? '');
        $a = $faqs["swim_faq_a_$i"] ?? '';
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











    <!-- Trophies -->
    <?php
    get_template_part('template-parts/grids/tab-grid', null, [
        'aria_label'      => 'Swimming',
        'sidebar_heading' => 'Trophies',
        'active'          => 'backstroke',
        'order_tokens'    => $order_tokens,
        'id_base'         => 'swimming',
        'tabs' => [
            ['slug' => 'backstroke',        'label' => 'Backstroke',        'panel' => ['type' => 'cards', 'prefix' => 'backstroke']],
            ['slug' => 'breaststroke',      'label' => 'Breaststroke',      'panel' => ['type' => 'cards', 'prefix' => 'breaststroke']],
            ['slug' => 'butterfly',         'label' => 'Butterfly',         'panel' => ['type' => 'cards', 'prefix' => 'butterfly']],
            ['slug' => 'freestyle',         'label' => 'Freestyle',         'panel' => ['type' => 'cards', 'prefix' => 'freestyle']],
            ['slug' => 'individual_medley', 'label' => 'Individual Medley', 'panel' => ['type' => 'cards', 'prefix' => 'individual_medley']],
            ['slug' => 'special_awards',    'label' => 'Special Awards',    'panel' => ['type' => 'cards', 'prefix' => 'special_awards']],

        ],
    ]);
    ?>



















</div>

<?php get_footer(); ?>