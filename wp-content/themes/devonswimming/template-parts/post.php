<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if(has_post_thumbnail()): ?>
        <div class="post-featured-image">
            
            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                <?php the_post_thumbnail('large'); ?>
            </a>
            
        </div>
    <?php endif; ?>
    
    <div class="post-content">

        <div class="post-header">
            <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="post-meta">
                <span class="posted-on"><?php echo esc_html( get_the_date() ); ?></span>
            </div>
        </div>

        <div class="post-excerpt-wrapper">
            <div class="post-excerpt">
                <?php 
                // Display excerpt if available, otherwise show first 50 words
                if (has_excerpt()) {
                    the_excerpt();
                } else {
                    echo wp_trim_words(get_the_content(), 50);
                }
                ?>
            </div>
            <a href="<?php the_permalink(); ?>" class="read-more">Read More...</a>
        </div>

    </div>
</article>
