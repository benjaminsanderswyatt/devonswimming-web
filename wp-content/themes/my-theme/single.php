<?php

get_header();
?>

<main id="primary" class="site-main" role="main">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" class="container" <?php post_class(); ?> itemscope itemtype="https://schema.org/BlogPosting">

                <div class="entry-header">
                    <h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>

                    <div class="entry-meta">
                        <span class="posted-on">
                            <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>" itemprop="datePublished">
                                <?php echo esc_html(get_the_date(get_option('date_format'))); ?>
                            </time>
                        </span>

                        <?php
                        // Show "by Author" ONLY to admins.
                        if (current_user_can('manage_options')) : ?>
                            <span class="byline">
                                &nbsp;•&nbsp;<?php esc_html_e('by', 'devon-swimming'); ?>
                                <span class="author vcard" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                    <a class="url fn n" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" itemprop="name">
                                        <?php the_author(); ?>
                                    </a>
                                </span>
                            </span>
                        <?php endif; ?>

                        <?php
                        // Show updated date if different.
                        if (get_the_modified_time('U') !== get_the_time('U')) : ?>
                            <span class="updated-on">
                                &nbsp;•&nbsp;<?php esc_html_e('Updated:', 'devon-swimming'); ?>
                                <time datetime="<?php echo esc_attr(get_the_modified_date(DATE_W3C)); ?>" itemprop="dateModified">
                                    <?php echo esc_html(get_the_modified_date(get_option('date_format'))); ?>
                                </time>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (has_post_thumbnail()) : ?>
                        <figure class="post-thumbnail" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                            <?php
                            // Provide smart sizes: full width up to 800px (adjust to your container)
                            the_post_thumbnail(
                                'large',
                                [
                                    'itemprop'        => 'url',
                                    'loading'         => 'eager',        // don't lazy-load hero
                                    'decoding'        => 'async',
                                    'fetchpriority'   => 'high',         // hint browser to fetch early
                                    'sizes'           => '(max-width: 800px) 100vw, 800px'
                                ]
                            );
                            ?>
                        </figure>
                    <?php endif; ?>
                </div>

                <div class="entry-content" itemprop="articleBody">
                    <?php the_content(); ?>
                    <?php
                    // Paginated posts support
                    wp_link_pages([
                        'before' => '<nav class="page-links" aria-label="' . esc_attr__('Post pages', 'devon-swimming') . '">',
                        'after'  => '</nav>',
                    ]);
                    ?>
                </div>

                <footer class="entry-footer">
                    <div class="entry-taxonomies">
                        <?php
                        // Categories removed (per request).
                        // Keep tags (they link properly). Remove this block if you also want tags gone.
                        the_tags('<span class="tag-links">', ' ', '</span>');
                        ?>
                    </div>

                    <?php edit_post_link(__('Edit', 'devon-swimming'), '<span class="edit-link">', '</span>'); ?>
                </footer>

            </article>



            
    <?php
            // Post navigation
            ?> <div class="container"> <?php
            the_post_navigation([
                'prev_text' => '&larr; %title',
                'next_text' => '%title &rarr;',
                'screen_reader_text' => __('Post navigation', 'devon-swimming'),
            ]);
            ?> </div> <?php

            // Comments
            if (comments_open() || get_comments_number()) {
                comments_template();
            }

        endwhile;
    endif; ?>

</main>


<?php get_footer(); ?>