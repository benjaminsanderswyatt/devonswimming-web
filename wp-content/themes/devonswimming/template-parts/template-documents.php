<?php
/**
 * Template Name: Documents
 */

get_header();
?>

<div class="site-main template-documents">

  <?php
  // Query all Documents Sections ordered by title
  $documents_sections = new WP_Query([
      'post_type'      => 'documents_section',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'DESC',
  ]);

  if ($documents_sections->have_posts()) :
      while ($documents_sections->have_posts()) : $documents_sections->the_post();

          $mf_text = get_field('multiple_files_text');
          $static_image = get_template_directory_uri() . '/assets/icons/document-text.svg';
          $file_items = [];

          // Collect files from ACF fields (1-20)
          for ($i = 1; $i <= 20; $i++) {
              $label = get_field("multiple_files_file_{$i}_file_label");
              $file  = get_field("multiple_files_file_{$i}_file_file");

              $href = '';
              if (!empty($file)) {
                  $href = is_array($file) ? $file['url'] : $file;
              }

              if (!$label && !$href) continue;

              $file_items[] = [
                  'label' => $label,
                  'href'  => $href,
              ];
          }

        // Get content safely
        $post_content = trim(get_the_content());

        // Skip this post if no files AND no content
        if (empty($file_items) && empty($post_content)) {
            continue;
        }
  ?>

  <section class="container file-download-section">

      <div class="container-content">

            <h2><?php the_title(); ?></h2>

            <?php
            // Post editor content
            if (!empty($post_content)) :
            ?>
                <div class="documents-content">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($mf_text)) : ?>
                <p><?php echo wp_kses_post($mf_text); ?></p>
            <?php endif; ?>
          

          <div class="container-multiple-files">

              <?php foreach ($file_items as $it):
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

      </div>
  </section>

  <?php
      endwhile;
      wp_reset_postdata();
  endif;
  ?>

</div>

<?php get_footer(); ?>