<?php
/**
 * Expects ACF fields with these names:
 * - container_header
 * - container_text
 * - container_link
 * - container_link_label
 * - container_file_1
 * - container_file_1_icon
 * - container_file_1_label
 * - container_file_2
 * - container_file_2_icon
 * 
 */

$args = isset($args) && is_array($args) ? $args : [];
$version = '';
if (!empty($args['version'])) {
    $version = '_' . sanitize_key($args['version']);
}

$container_header       = get_field('container_header' . $version);
$container_text         = get_field('container_text' . $version);
$container_link         = get_field('container_link' . $version);
$container_link_label   = get_field('container_link_label' . $version);

$container_file_1       = get_field('container_file_1' . $version);
$container_file_1_label = get_field('container_file_1_label' . $version);
$container_file_2       = get_field('container_file_2' . $version);
$container_file_2_label = get_field('container_file_2_label' . $version);

// Collect available files
$file_buttons = [];
for ($i = 1; $i <= 2; $i++) {
    $icon  = get_field("container_file_{$i}_icon" . $version);
    $label = get_field("container_file_{$i}_label" . $version);
    $file  = get_field("container_file_{$i}" . $version);

    if (!empty($file)) {
        $file_buttons[] = [
            'icon'  => is_array($icon) ? $icon['url'] : $icon,
            'label' => $label ?: 'Download File',
            'url'   => is_array($file) ? $file['url'] : $file,
        ];
    }
}

?>

<?php
if ($container_header || $container_text || $container_link|| !empty($file_buttons)) : ?>

    <section class="container two-column-section">

        <div class="container-content">

            <!-- Header -->
            <?php if ( $container_header ) : ?>
                <h2><?php echo esc_html($container_header); ?></h2>
            <?php endif; ?>

            <!-- Body text -->
            <?php if ( $container_text ) : ?>
                <p><?php echo wp_kses_post($container_text); ?></p>
            <?php endif; ?>


            <!-- File download buttons -->
            <?php if (!empty($file_buttons)) : ?>
                <div class="container-files">
                    <?php foreach ($file_buttons as $btn) : ?>
                        <a
                            href="<?php echo esc_url($btn['url']); ?>"
                            class="file-button"
                            download
                        >
                            <?php if (!empty($btn['icon'])) : ?>
                                <img
                                    src="<?php echo esc_url($btn['icon']); ?>"
                                    alt=""
                                    class="file-button-icon"
                                    loading="lazy"
                                    decoding="async"
                                >
                            <?php endif; ?>
                            <span class="file-button-label">
                                <?php echo esc_html($btn['label']); ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>


            <!-- Link -->
            <?php if ( $container_link ) : ?>
                <a
                    href="<?php echo esc_url($container_link); ?>"
                    class="link-button"
                    target="_blank"
                    rel="noopener"
                ><?php echo esc_html($container_link_label); ?></a>
            <?php endif; ?>

        </div>

    </section>
<?php endif; ?>