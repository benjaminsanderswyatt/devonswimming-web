<?php
/**
 * Template Name: Funding-Support
 */
get_header();

// -------- Helpers --------
$gf = static function (string $k, $default = '') {
    return function_exists('get_field') ? (get_field($k) ?: $default) : $default;
};

// -------- ACF: Page Intro (optional) --------
$funding_intro = $gf('funding_intro'); // WYSIWYG or textarea

// -------- ACF: Devon County Bursary --------
$county_heading    = $gf('county_heading');               // "Devon County Bursary"
$county_intro      = $gf('county_intro');                 // wysiwyg/textarea
$county_rate_l1    = trim((string)$gf('county_rate_l1')); // e.g. "£50"
$county_rate_l2    = trim((string)$gf('county_rate_l2')); // e.g. "£100"
$county_steps_1    = $gf('county_step_1');                // "Identify your course..."
$county_steps_2    = $gf('county_step_2');                // "Complete your bursary form..."
$county_steps_3    = $gf('county_step_3');                // "On receipt of your certificate..."
$county_email      = trim((string)$gf('county_email'));   // "bursaries@devonswimming.org.uk"
$county_form_label = $gf('county_form_label');            // "DCASA bursary application 2025"
$county_form_url   = trim((string)$gf('county_form_url')); // URL (page on your site preferred)
$county_deadline   = trim((string)$gf('county_deadline')); // free text or date formatted
$county_note       = $gf('county_note');                  // "Copies of receipts/certificates required"

// -------- ACF: Regional Bursary (SESW) --------
$regional_heading     = $gf('regional_heading');                // "Regional Bursaries"
$regional_intro       = $gf('regional_intro');                  // wysiwyg
$regional_rate_l1     = trim((string)$gf('regional_rate_l1'));  // "£50"
$regional_rate_l2     = trim((string)$gf('regional_rate_l2'));  // "£100"
$regional_rate_l3     = trim((string)$gf('regional_rate_l3'));  // "£200"
$regional_limit_text  = $gf('regional_limit_text');             // "Max 3 per discipline per club..."
$regional_submit_note = $gf('regional_submit_note');            // "Submit at same time as county..."
$regional_form_label  = $gf('regional_form_label');             // "SESW bursary application form 2025"
$regional_form_url    = trim((string)$gf('regional_form_url')); // URL
$regional_note        = $gf('regional_note');                   // extra notes if needed
$global_deadline      = trim((string)$gf('funding_deadline'));  // "Deadline 30 November 2025"

// Guards
$has_county = (
    $county_heading || $county_intro || $county_rate_l1 || $county_rate_l2 ||
    $county_steps_1 || $county_steps_2 || $county_steps_3 ||
    $county_form_url || $county_email || $county_deadline || $county_note
);
$has_regional = (
    $regional_heading || $regional_intro || $regional_rate_l1 || $regional_rate_l2 || $regional_rate_l3 ||
    $regional_form_url || $regional_limit_text || $regional_submit_note || $regional_note || $global_deadline
);
?>

<div class="site-main template-funding-support">

    <?php if ($funding_intro): ?>
        <section class="container">
            <div class="container-content">
                <div class="funding-intro">
                    <?php echo wp_kses_post($funding_intro); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($has_county || $has_regional): ?>
        <div class="fr-row">

            <?php if ($has_county): ?>
                <section class="funding-block container fr-item">
                    <div class="container-content">
                        <?php if ($county_heading): ?>
                            <h2><?php echo esc_html($county_heading); ?></h2>
                        <?php endif; ?>

                        <?php if ($county_intro): ?>
                            <div class="funding-copy"><?php echo wp_kses_post($county_intro); ?></div>
                        <?php endif; ?>

                        <?php if ($county_rate_l1 || $county_rate_l2): ?>
                            <ul class="rate-chips" aria-label="County bursary rates">
                                <?php if ($county_rate_l1): ?>
                                    <li><span class="chip">Level 1 - <?php echo esc_html($county_rate_l1); ?></span></li>
                                <?php endif; ?>
                                <?php if ($county_rate_l2): ?>
                                    <li><span class="chip">Level 2 - <?php echo esc_html($county_rate_l2); ?></span></li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if ($county_steps_1 || $county_steps_2 || $county_steps_3): ?>
                            <ol class="funding-steps">
                                <?php if ($county_steps_1): ?><li><?php echo wp_kses_post($county_steps_1); ?></li><?php endif; ?>
                                <?php if ($county_steps_2): ?><li><?php echo wp_kses_post($county_steps_2); ?></li><?php endif; ?>
                                <?php if ($county_steps_3): ?><li><?php echo wp_kses_post($county_steps_3); ?></li><?php endif; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if ($county_note): ?>
                            <div class="funding-note"><?php echo wp_kses_post($county_note); ?></div>
                        <?php endif; ?>

                        <?php if ($county_form_url || $county_email): ?>
                            <div class="funding-cta">
                                <?php if ($county_form_url): ?>
                                    <a class="chip" href="<?php echo esc_url($county_form_url); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html($county_form_label ?: 'Bursary Application Form'); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($county_email): ?>
                                    <a class="chip" href="mailto:<?php echo antispambot($county_email); ?>">
                                        Email Devon Bursary Secretary
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($county_deadline): ?>
                            <div class="deadline-callout" role="note">
                                <strong>Deadline:</strong>
                                <span><?php echo esc_html($county_deadline); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($has_regional): ?>
                <section class="funding-block container fr-item">
                    <div class="container-content">
                        <?php if ($regional_heading): ?>
                            <h2><?php echo esc_html($regional_heading); ?></h2>
                        <?php endif; ?>

                        <?php if ($regional_intro): ?>
                            <div class="funding-copy"><?php echo wp_kses_post($regional_intro); ?></div>
                        <?php endif; ?>

                        <?php if ($regional_rate_l1 || $regional_rate_l2 || $regional_rate_l3): ?>
                            <ul class="rate-chips" aria-label="Regional bursary rates (SESW)">
                                <?php if ($regional_rate_l1): ?>
                                    <li><span class="chip">Level 1 - <?php echo esc_html($regional_rate_l1); ?></span></li>
                                <?php endif; ?>
                                <?php if ($regional_rate_l2): ?>
                                    <li><span class="chip">Level 2 - <?php echo esc_html($regional_rate_l2); ?></span></li>
                                <?php endif; ?>
                                <?php if ($regional_rate_l3): ?>
                                    <li><span class="chip">Level 3 - <?php echo esc_html($regional_rate_l3); ?></span></li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if ($regional_limit_text): ?>
                            <div class="funding-note"><?php echo wp_kses_post($regional_limit_text); ?></div>
                        <?php endif; ?>

                        <?php if ($regional_submit_note): ?>
                            <div class="funding-note"><?php echo wp_kses_post($regional_submit_note); ?></div>
                        <?php endif; ?>

                        <?php if ($regional_note): ?>
                            <div class="funding-note"><?php echo wp_kses_post($regional_note); ?></div>
                        <?php endif; ?>

                        <?php if ($regional_form_url): ?>
                            <div class="funding-cta">
                                <a class="chip" href="<?php echo esc_url($regional_form_url); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html($regional_form_label ?: 'SESW Bursary Application Form'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($global_deadline): ?>
                            <div class="deadline-callout" role="note">
                                <strong>Deadline:</strong>
                                <span><?php echo esc_html($global_deadline); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
