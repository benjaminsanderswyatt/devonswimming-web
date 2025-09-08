<?php
/**
 * Expects ACF fields with these names:
 * - container_header
 * - container_text
 * - container_link
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

?>

<?php
if ($container_header || $container_text || $container_link) : ?>

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