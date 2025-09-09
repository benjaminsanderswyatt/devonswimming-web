<?php

/**
 * Concern Block (ACF Free friendly; no repeaters)
 * - Welfare Officer shown elsewhere (e.g., committee panel); here we only link to it.
 * - Inline anchor link after a chosen step (default 2) to scroll to #welfare-officer.
 * - Org contacts flattened (no headings), auto-linked, and de-duplicated.
 * - Referral link rendered inline like the other items.
 * - Emergency items don’t show a trailing dash if no phone/URL.
 *
 * Usage in template:
 * get_template_part('template-parts/sections/concern-block', null, [
 *   'wo_anchor_id' => 'welfare-officer', // target id to scroll to
 *   'wo_link_step' => 2                  // after which step to show the inline link
 * ]);
 */

$args          = is_array($args ?? null) ? $args : [];
$version       = isset($args['version']) && $args['version'] !== '' ? '_' . sanitize_key($args['version']) : '';
$wo_anchor_id  = isset($args['wo_anchor_id']) && $args['wo_anchor_id'] !== '' ? sanitize_title($args['wo_anchor_id']) : 'welfare-officer';
$wo_link_step  = isset($args['wo_link_step']) ? max(1, min(4, (int)$args['wo_link_step'])) : 2;

$gf = static function (string $name) use ($version) {
    return get_field($name . $version);
};
$sanitize_phone = static function ($p) {
    return preg_replace('/\s+/', '', (string)$p);
};
$get_link_url = static function ($maybeLink) {
    if (empty($maybeLink)) return '';
    if (is_array($maybeLink)) return $maybeLink['url'] ?? '';
    if (is_string($maybeLink)) return $maybeLink;
    return '';
};

/** Core fields */
$heading = $gf('concern_heading');
$intro   = $gf('concern_intro');

/** Steps (Group with 1..4) */
$steps_group = $gf('steps');
$steps       = [];
for ($i = 1; $i <= 4; $i++) {
    $key   = "concern_step_{$i}";
    $value = is_array($steps_group) ? ($steps_group[$key] ?? '') : '';
    if ($value) $steps[] = $value;
}

/** We only need to know if there IS a welfare officer to decide to show the inline link text */
$wo_name  = $gf('welfare_officer_name');
$wo_email = $gf('welfare_officer_email');
$wo_phone = $gf('welfare_officer_phone');
$wo_extra = $gf('welfare_officer_extra');
$has_wo   = $wo_name || $wo_email || $wo_phone || $wo_extra;

/** Org contacts (Group with 1..3) -> flatten + dedupe */
$org_group     = $gf('org_contacts');
$contact_items = []; // each: ['html' => '...']
$seen          = []; // dedupe keys

$normalize_url = static function ($u) {
    $u = trim((string)$u);
    $u = preg_replace('#^https?://#i', '', $u);
    return strtolower(rtrim($u, '/'));
};
$normalize_text = static function ($t) {
    return strtolower(trim((string)$t));
};

$make_link_html = static function ($text, $url) {
    return '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">'
        . esc_html($text ?: $url) . '</a>';
};

$autolink_from_value = static function ($value) use ($make_link_html, $sanitize_phone) {
    $v = trim((string)$value);
    if (!$v) return '';
    if (is_email($v))            return $make_link_html(antispambot($v), 'mailto:' . antispambot($v));
    if (preg_match('/^[+()0-9\s-]+$/', $v)) return $make_link_html($v, 'tel:' . $sanitize_phone($v));
    if (preg_match('#^https?://#i', $v))    return $make_link_html($v, $v);
    return esc_html($v);
};

$referral_label = trim((string)$gf('referral_link_label'));
$referral_url   = trim((string)$gf('referral_link_url'));

for ($i = 1; $i <= 3; $i++) {
    // We intentionally ignore labels for display to keep it flat
    $label = is_array($org_group) ? trim((string)($org_group["label_{$i}"] ?? '')) : '';
    $value = is_array($org_group) ? trim((string)($org_group["value_{$i}"] ?? '')) : '';
    $link  = is_array($org_group) ? ($org_group["link_{$i}"] ?? '') : '';
    $url   = $get_link_url($link);

    $text = $value !== '' ? $value : $label; // prefer value, fall back to label
    if ($text === '' && $url === '') continue;

    // If we also have a dedicated referral field, skip rows that look like referral to avoid duplicates
    if ($referral_url && (stripos($text, 'referral') !== false || stripos($label, 'referral') !== false)) {
        continue;
    }

    $html = $url ? $make_link_html($text, $url) : $autolink_from_value($text);
    if ($html === '') continue;

    $key = $normalize_text(strip_tags($text)) . '|' . ($url ? $normalize_url($url) : $normalize_text(strip_tags($text)));
    if (isset($seen[$key])) continue;
    $seen[$key] = true;

    $contact_items[] = ['html' => $html];
}

/** Add the referral as a normal list item (no big button), deduped */
if ($referral_url) {
    $ref_text = $referral_label ?: 'Online referral form';
    $key = $normalize_text($ref_text) . '|' . $normalize_url($referral_url);
    if (!isset($seen[$key])) {
        $contact_items[] = ['html' => $make_link_html($ref_text, $referral_url)];
        $seen[$key] = true;
    }
}

/** Emergency */
$em_heading   = $gf('emergency_heading');
$em_intro     = $gf('emergency_intro');
$em_group     = $gf('emergency_items');
$em_items     = [];
for ($i = 1; $i <= 3; $i++) {
    $label = is_array($em_group) ? trim((string)($em_group["label_{$i}"] ?? '')) : '';
    $phone = is_array($em_group) ? trim((string)($em_group["phone_{$i}"] ?? '')) : '';
    $url   = is_array($em_group) ? trim((string)($em_group["url_{$i}"]   ?? '')) : '';
    if ($label || $phone || $url) $em_items[] = compact('label', 'phone', 'url');
}
$em_aftercare = $gf('emergency_aftercare');

/** Render guards */
$has_steps    = !empty($steps);
$has_contacts = !empty($contact_items);
$has_em       = $em_heading || $em_intro || !empty($em_items) || $em_aftercare;

if (!$heading && !$intro && !$has_steps && !$has_contacts && !$has_em) return;
?>

<section class="concern-block safeguard-block" aria-labelledby="concern-heading<?php echo esc_attr($version); ?>">
    <?php if ($heading): ?>
        <h2 id="concern-heading<?php echo esc_attr($version); ?>"><?php echo esc_html($heading); ?></h2>
    <?php endif; ?>

    <div class="concern-columns">
        <div class="concern-col">
            <?php if ($intro): ?>
                <div class="concern-intro"><?php echo wp_kses_post($intro); ?></div>
            <?php endif; ?>

            <?php if ($has_steps): ?>
                <ol class="concern-steps">
                    <?php $idx = 0; ?>
                    <?php foreach ($steps as $html): $idx++; ?>
                        <li>
                            <?php echo wp_kses_post($html); ?>
                            <?php if ($has_wo && $idx === $wo_link_step): ?>
                                <div class="inline-tip">
                                    <a class="inline-anchor" href="#<?php echo esc_attr($wo_anchor_id); ?>">
                                        View Details
                                    </a>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>

            <?php if ($has_contacts): ?>
                <ul class="contact-list flat team">
                    <?php foreach ($contact_items as $it): ?>
                        <li><?php echo $it['html']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <?php if ($has_em): ?>
            <aside class="concern-col emergency" aria-labelledby="emergency-heading<?php echo esc_attr($version); ?>">
                <?php if ($em_heading): ?>
                    <h3 id="emergency-heading<?php echo esc_attr($version); ?>"><?php echo esc_html($em_heading); ?></h3>
                <?php endif; ?>

                <?php if ($em_intro): ?>
                    <div><?php echo wp_kses_post($em_intro); ?></div>
                <?php endif; ?>

                <?php if (!empty($em_items)): ?>
                    <ul class="emergency-list">
                        <?php foreach ($em_items as $e): ?>
                            <?php
                            $hasDetail = !empty($e['phone']) || !empty($e['url']);
                            $detailOut = '';
                            if (!empty($e['phone'])) {
                                $detailOut = '<a href="tel:' . esc_attr($sanitize_phone($e['phone'])) . '">' . esc_html($e['phone']) . '</a>';
                            } elseif (!empty($e['url'])) {
                                $detailOut = '<a href="' . esc_url($e['url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($e['url']) . '</a>';
                            }
                            ?>
                            <li>
                                <?php echo esc_html($e['label']); ?><?php if ($hasDetail): ?> – <?php echo $detailOut; ?><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($em_aftercare): ?>
                    <p class="small-print"><?php echo wp_kses_post($em_aftercare); ?></p>
                <?php endif; ?>
            </aside>
        <?php endif; ?>
    </div>
</section>