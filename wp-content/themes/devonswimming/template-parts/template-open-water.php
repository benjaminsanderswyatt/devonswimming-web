<?php

/**
 * Template Name: Open Water
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

/** -------- Fetch groups (no intro block) -------- */
$safety = $g('ow_safety');   // ow_safety_heading, ow_safety_item_1..6
$events = $g('ow_events');   // ow_events_heading, ow_events_text
$ready  = $g('ow_ready');    // ow_ready_heading, ow_ready_item_1..6
$faqs   = $g('ow_faqs');     // ow_faqs_heading, ow_faq_q_1..6, ow_faq_a_1..6

/** -------- Optional images (same field names as Swimming) -------- */
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

<div class="site-main template-open-water">

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
    // Build card content
    $safety_heading = $t($safety['ow_safety_heading'] ?? '');
    $safety_items   = [];
    for ($i = 1; $i <= 6; $i++) {
        $val = $t($safety["ow_safety_item_$i"] ?? '');
        if ($val !== '') $safety_items[] = $val;
    }

    $events_heading = $t($events['ow_events_heading'] ?? '');
    $events_text    = $events['ow_events_text'] ?? '';

    $ready_heading  = $t($ready['ow_ready_heading'] ?? '');
    $ready_items    = [];
    for ($i = 1; $i <= 6; $i++) {
        $val = $t($ready["ow_ready_item_$i"] ?? '');
        if ($val !== '') $ready_items[] = $val;
    }

    $has_any_cards = ($safety_heading || $safety_items || $events_heading || $events_text || $ready_heading || $ready_items || $image_1 || $image_2 || $image_3);

    if ($has_any_cards): ?>
        <div class="swim-row">
            <?php if ($safety_heading || $safety_items): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($safety_heading): ?><h2><?php echo esc_html($safety_heading); ?></h2><?php endif; ?>
                        <?php if ($safety_items): ?>
                            <ul>
                                <?php foreach ($safety_items as $li): ?>
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
                        alt="<?php echo esc_attr($img_alt($image_1, 'Open water in Devon')); ?>">
                </figure>
            <?php endif; ?>

            <?php if ($events_heading || $events_text): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($events_heading): ?><h2><?php echo esc_html($events_heading); ?></h2><?php endif; ?>
                        <?php if ($events_text): ?><div class="about-copy"><?php echo wp_kses_post($events_text); ?></div><?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($image_2): ?>
                <figure class="sr-item swim-image">
                    <img src="<?php echo esc_url($img_src($image_2)); ?>"
                        alt="<?php echo esc_attr($img_alt($image_2, 'Coached open water session')); ?>">
                </figure>
            <?php endif; ?>

            <?php if ($ready_heading || $ready_items): ?>
                <section class="container sr-item">
                    <div class="container-content swim-card">
                        <?php if ($ready_heading): ?><h2><?php echo esc_html($ready_heading); ?></h2><?php endif; ?>
                        <?php if ($ready_items): ?>
                            <ul>
                                <?php foreach ($ready_items as $li): ?>
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
                        alt="<?php echo esc_attr($img_alt($image_3, 'Open water event')); ?>">
                </figure>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    // FAQs
    $faq_heading = $t($faqs['ow_faqs_heading'] ?? '');
    $faq_pairs   = [];
    for ($i = 1; $i <= 6; $i++) {
        $q = $t($faqs["ow_faq_q_$i"] ?? '');
        $a = $faqs["ow_faq_a_$i"] ?? '';
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
































    <?php
    get_template_part('template-parts/grids/tab-grid', null, [
        'aria_label'      => 'Open Water',
        'sidebar_heading' => 'Trophies',
        'active'          => 'all',
        'order_tokens'    => $order_tokens,
        'id_base'         => 'open-water',
        'tabs' => [
            ['slug' => 'all',       'label' => 'All',       'panel' => ['type' => 'cards', 'prefix' => 'all']],
        ],
    ]);
    ?>














</div>

<?php get_footer(); ?>