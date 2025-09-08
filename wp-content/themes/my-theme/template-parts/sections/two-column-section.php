<?php
/**
 * Expects ACF fields with these names:
 * - section_image (URL or Image ID/Array is fine if your ACF field returns URL; adjust as needed)
 * - section_header
 * - section_text
 * - (group) section_icons_icon_1_icon_image ... 5
 * - (group) section_icons_icon_1_icon_label ... 5
 * - section_link
 *
 */

// For reversing flex direction
$reverse_layout = !empty($args['reverse']) ? 'reverse-layout' : '';

$button_label = $args['button_label'] ?? 'Website';

$section_image = get_field('section_image');
$section_header = get_field('section_header');
$section_text   = get_field('section_text');
$section_link   = get_field('section_link');


// Collect icons into an array
$icons = [];
for ($row = 1; $row <= 5; $row++) {
    $icon_image = get_field("section_icons_icon_{$row}_icon_image");
    $icon_label = get_field("section_icons_icon_{$row}_icon_label");

    if (!empty($icon_image) || !empty($icon_label)) {
        $icons[] = [
            'image' => $icon_image,
            'label' => $icon_label,
        ];
    }
}

?>

<?php
if ($section_image || $section_header || $section_text || $section_link || !empty($icons)) :?>

    <section class="container two-column-section <?php echo esc_attr($reverse_layout); ?>">

        <?php if ( $section_image ) : ?>
            <div class="container-image">
                
                <img
                    src="<?php echo esc_url($section_image); ?>"
                    alt="<?php echo esc_attr($section_image['title'] ?? 'Featured section image'); ?>"
                    loading="lazy"
                    decoding="async"
                > 
                
            </div>
        <?php endif; ?>

        <div class="container-content">

            <!-- Header -->
            <?php if ( $section_header ) : ?>
                <h2><?php echo esc_html($section_header); ?></h2>
            <?php endif; ?>

            <!-- Body text -->
            <?php if ( $section_text ) : ?>
                <p><?php echo wp_kses_post($section_text); ?></p>
            <?php endif; ?>


            <!-- Icons -->
            <?php if (!empty($icons)) : ?>
                <div class="icons-row" role="list">
                    <?php foreach ($icons as $icon) : ?>

                        <div class="icon-block"  role="listitem">
                            <?php if (!empty($icon['image'])) : ?>
                                <img
                                    src="<?php echo esc_url($icon['image']); ?>"
                                    alt="<?php echo esc_attr($icon['label'] ?? ''); ?>"
                                    loading="lazy"
                                    decoding="async"
                                >
                            <?php endif; ?>
                            
                            <?php if (!empty($icon['label'])) : ?>
                                <p><?php echo esc_html($icon['label']); ?></p>
                            <?php endif; ?>
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php endif; ?>


            <!-- Link -->
            <?php if ( $section_link ) : ?>
                <a
                    href="<?php echo esc_url($section_link); ?>"
                    class="link-button"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="<?php echo esc_attr($button_label . ' (opens in new tab)'); ?>"
                ><?php echo esc_html($button_label); ?></a>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>