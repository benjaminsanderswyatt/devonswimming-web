<?php

// For reversing flex direction
$reverse_layout = !empty($args['reverse']) ? 'reverse-layout' : '';


$mb_image = get_field('multiple_button_image');
$mb_header = get_field('multiple_button_header');
$mb_text   = get_field('multiple_button_text');

// Collect buttons
$buttons = [];
for ($i = 1; $i <= 3; $i++) {
    $img   = get_field("multiple_buttons_button_{$i}_button_image"); // URL
    $label = get_field("multiple_buttons_button_{$i}_button_label");
    $link  = get_field("multiple_buttons_button_{$i}_button_link");  // URL
    $file  = get_field("multiple_buttons_button_{$i}_button_file");  // array

// Determine href: prefer link if present, else file
    $href = '';
    $is_file = false;

    if (!empty($link)) {
        $href = $link;
    } elseif (!empty($file)) {
        $href = $file;
        $is_file = true;
    }

    // Only keep if there’s something to show
    if (!empty($label) || !empty($href) || !empty($img)) {
        $buttons[] = [
            'image'   => $img,
            'label'   => $label,
            'href'    => $href,
            'is_file' => $is_file,
        ];
    }
}


?>

<?php
if ($mb_image || $mb_header || $mb_text || !empty($buttons)) :?>
    <section class="container two-column-section <?php echo esc_attr($reverse_layout); ?>">

        <?php if (!empty($mb_image)) : ?>
            <div class="container-image">
                <img
                    src="<?php echo esc_url($mb_image); ?>"
                    alt="<?php echo esc_attr(!empty($mb_header) ? $mb_header : 'Section image'); ?>"
                    loading="lazy"
                    decoding="async"
                >
                
            </div>
        <?php endif; ?>

        

        <div class="container-content">
            <!-- Header -->
            <?php if (!empty($mb_header)) : ?>
                <h2><?php echo esc_html($mb_header); ?></h2>
            <?php endif; ?>

            <!-- Body text -->
            <?php if (!empty($mb_text)) : ?>
                <p><?php echo wp_kses_post($mb_text); ?></p>
            <?php endif; ?>


        </div>

        <?php if (!empty($buttons)) : ?>
            <div class="container-buttons">
                <div class="buttons-grid">
                    <?php foreach ($buttons as $btn) : ?>
                        <?php
                        $label   = $btn['label'] ?? '';
                        $href    = $btn['href'] ?? '';
                        $is_file = !empty($btn['is_file']);
                        ?>
                        <div class="button-card">
                            <?php if (!empty($btn['image'])) : ?>
                                <img
                                    src="<?php echo esc_url($btn['image']); ?>"
                                    alt="<?php echo esc_attr($label ?: ''); ?>"
                                    loading="lazy"
                                    decoding="async"
                                >
                            <?php endif; ?>

                            <?php if (!empty($href)) : ?>
                                <a
                                    href="<?php echo esc_url($href); ?>"
                                    class="link-button"
                                    <?php if ($is_file) : ?>
                                        download
                                    <?php else : ?>
                                        target="_blank" rel="noopener"
                                    <?php endif; ?>
                                >
                                    <?php echo esc_html($label ?: 'Open'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>



    </section>
<?php endif; ?>