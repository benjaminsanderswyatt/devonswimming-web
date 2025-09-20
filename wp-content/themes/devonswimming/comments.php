<?php
/**
 * Comments Template
 * Location: wp-content/themes/your-theme/comments.php
 */

if ( post_password_required() ) {
    return;
}
?>

<section id="comments" class="comments-area">
    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
            printf(
                _nx( 'One comment', '%1$s comments', get_comments_number(), 'comments title', 'devon-swimming' ),
                number_format_i18n( get_comments_number() )
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( [
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size'=> 48,
            ] );
            ?>
        </ol>

        <?php
        the_comments_pagination( [
            'prev_text' => __( '&larr; Older Comments', 'devon-swimming' ),
            'next_text' => __( 'Newer Comments &rarr;', 'devon-swimming' ),
        ] );
        ?>
    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() ) : ?>
        <p class="no-comments"><?php _e( 'Comments are closed.', 'devon-swimming' ); ?></p>
    <?php endif; ?>

    <?php comment_form(); ?>
</section>
