<?php
/**
 * Multiple Files Section
 * Expects $args['post_id'] to get fields from CPT
 */

$post_id = $args['post_id'] ?? get_the_ID();

$mf_header = get_field('multiple_files_header', $post_id);
$mf_text   = get_field('multiple_files_text', $post_id);
$static_image = get_template_directory_uri() . '/assets/icons/document-text.svg';

$file_items = [];

for ($i = 1; $i <= 20; $i++) {
    $label = get_field("multiple_files_file_{$i}_file_label", $post_id);
    $file  = get_field("multiple_files_file_{$i}_file_file", $post_id);

    if (!$label && !$file) continue;

    $href = is_array($file) ? $file['url'] : $file;

    $file_items[] = [
        'label' => $label,
        'href'  => $href,
    ];
}
?>

<?php if ($mf_header || $mf_text || !empty($file_items)) : ?>
<section class="container file-download-section">

    <div class="container-content">

        <?php if ($mf_header) : ?>
            <h2><?php echo esc_html($mf_header); ?></h2>
        <?php endif; ?>

        <?php if ($mf_text) : ?>
            <p><?php echo wp_kses_post($mf_text); ?></p>
        <?php endif; ?>

        <?php if (!empty($file_items)) : ?>
        <div class="container-multiple-files">
            <?php foreach ($file_items as $it) : 
                $has_link = !empty($it['href']);
            ?>
                <a
                    href="<?= $has_link ? esc_url($it['href']) : '#' ?>"
                    class="file-button"
                    <?= $has_link ? 'download' : '' ?>
                >
                    <img
                        src="<?= esc_url($static_image); ?>"
                        alt="<?= esc_attr($it['label'] ?: 'Document'); ?>"
                        class="file-button-icon"
                        loading="lazy"
                        decoding="async"
                    >
                    <span class="file-button-label">
                        <?= esc_html($it['label'] ?: 'Download'); ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
<?php endif; ?>