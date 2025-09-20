<?php

/**
 * Template Name: Safeguarding-Welfare
 */

get_header();




// Helpers
$gf = static function (string $k, $default = '') {
    return function_exists('get_field') ? (get_field($k) ?: $default) : $default;
};
$tel_href = static function (string $p): string {
    $num = preg_replace('/\s+/', '', $p);
    return 'tel:' . preg_replace('/[^+0-9]/', '', $num);
};
$host_of = static function (?string $url): string {
    $url = trim((string)$url);
    if ($url === '') return '';
    $host = parse_url($url, PHP_URL_HOST);
    return $host ?: $url;
};

/* ---------- ACF: Support & Helplines (group: support_items) ---------- */
$support_heading = $gf('support_heading'); // e.g. "Additional Support & Helplines"
$support_items   = function_exists('get_field') ? (get_field('support_items') ?: []) : [];
$support         = [];
for ($i = 1; $i <= 6; $i++) {
    $name  = trim((string)($support_items["name_{$i}"]  ?? ''));
    $phone = trim((string)($support_items["phone_{$i}"] ?? ''));
    $url   = trim((string)($support_items["url_{$i}"]   ?? ''));
    if ($name || $phone || $url) {
        $support[] = ['name' => $name, 'phone' => $phone, 'url' => $url];
    }
}

/* ---------- ACF: Resources (group: resources) ---------- */
$resources_heading = $gf('resources_heading'); // e.g. "Resources"
$resources_group   = function_exists('get_field') ? (get_field('resources') ?: []) : [];
$resources         = [];
for ($i = 1; $i <= 4; $i++) {
    $label = trim((string)($resources_group["label_{$i}"] ?? ''));
    $url   = trim((string)($resources_group["url_{$i}"]   ?? ''));
    if ($label || $url) {
        $resources[] = ['label' => $label, 'url' => $url];
    }
}

/* ---------- ACF: Welfare Officer Role ---------- */
$wo_heading     = $gf('welfare_role_heading'); // "Welfare Officer Role"
$wo_body        = $gf('welfare_role_body');    // WYSIWYG/textarea
$training_label = $gf('training_link_label');  // "Swim England Safeguarding Training"
$training_url   = $gf('training_link_url');    // text/URL
$wo_email       = $gf('welfare_contact_email'); // text/email






?>

<div class="site-main template-safeguarding-welfare">

    <?php
    get_template_part(
        'template-parts/sections/two-column-section',
        null,
        [
            'image_alt'    => 'Officials image',
            'reverse'      => false
        ]
    );
    ?>

    <!-- What is Welfare -->
    <section class="officials-info-1">
        <?php get_template_part('template-parts/sections/container-info', null); ?>
    </section>








    <!-- Concern -->
    <section class="container">
        <?php
        get_template_part('template-parts/sections/concern-block', null, [
            'wo_anchor_id' => 'welfare-officer', // welfare officer section id (scrolls to it)
            'wo_link_step' => 2 // step after which to insert the link
        ]);
        ?>
    </section>





    <?php if ($wo_heading || $wo_body || $training_url || $wo_email) : ?>
        <section class="welfare-role-block container">
            <div class="container-content">
                <?php if ($wo_heading): ?>
                    <h2><?php echo esc_html($wo_heading); ?></h2>
                <?php endif; ?>

                <div class="role-body">
                    <?php if ($wo_body): ?>
                        <p><?php echo wp_kses_post($wo_body); ?></p>
                    <?php endif; ?>

                    <div class="role-cta">
                        <?php if ($training_url): ?>
                            <a class="chip" href="<?php echo esc_url($training_url); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html($training_label ?: 'Training information'); ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($wo_email): ?>
                            <a class="chip" href="mailto:<?php echo antispambot($wo_email); ?>">
                                Contact Devon ASA Welfare Officer
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>













    <section id="welfare-officer" class="welfare-officer">
        <?php get_template_part('template-parts/grids/cards-grid', null); ?>
    </section>










    <?php
    $has_support   = ($support_heading || !empty($support));
    $has_resources = ($resources_heading || !empty($resources));

    if ($has_support || $has_resources): ?>
        <div class="sr-row">
            <?php if ($has_support): ?>
                <section class="support-block container sr-item">
                    <div class="container-content">
                        <?php if ($support_heading): ?>
                            <h2><?php echo esc_html($support_heading); ?></h2>
                        <?php endif; ?>

                        <?php if (!empty($support)): ?>
                            <ul class="support-list stretch">
                                <?php foreach ($support as $s): ?>
                                    <?php
                                    $name  = esc_html($s['name'] ?? '');
                                    $phone = trim((string)($s['phone'] ?? ''));
                                    $url   = trim((string)($s['url']   ?? ''));
                                    $host  = $host_of($url);
                                    ?>
                                    <li>
                                        <div class="support-card">
                                            <?php if ($name): ?>
                                                <span class="support-name"><?php echo $name; ?></span>
                                            <?php endif; ?>

                                            <?php if ($phone): ?>

                                                <a href="<?php echo esc_attr($tel_href($phone)); ?>"><?php echo esc_html($phone); ?></a>
                                            <?php endif; ?>

                                            <?php if ($url): ?>

                                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                                    <?php echo esc_html($host); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($has_resources): ?>
                <section class="resources-block container sr-item">
                    <div class="container-content">
                        <?php if ($resources_heading): ?>
                            <h2><?php echo esc_html($resources_heading); ?></h2>
                        <?php endif; ?>

                        <?php if (!empty($resources)): ?>
                            <ul class="resources-list stretch">
                                <?php foreach ($resources as $r): ?>
                                    <?php
                                    $label = esc_html($r['label'] ?? '');
                                    $url   = trim((string)($r['url'] ?? ''));
                                    ?>
                                    <li>
                                        <?php if ($url): ?>
                                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                                <?php echo $label ?: esc_html($url); ?>
                                            </a>
                                        <?php elseif ($label): ?>
                                            <span><?php echo $label; ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    <?php endif; ?>
















</div>

<?php get_footer(); ?>