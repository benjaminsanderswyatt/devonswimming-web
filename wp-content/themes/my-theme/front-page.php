<?php get_header(); ?>

<section class="main-content">
    <div class="column-wrapper">
        <!-- Three-column layout -->
        <div class="three-column-layout">

            <!-- Left sidebar -->
            <aside class="sidebar sidebar-left" role="complementary" aria-label="<?php esc_attr_e('Events', 'devon-swimming'); ?>">

                <h1 class="sidebar-header">Upcoming Events</h1>

                <?php



                if (shortcode_exists('ms_upcoming_events')) {
                    // Try to render the shortcode
                    $output = do_shortcode('[ms_upcoming_events 
                            page_slug="calendar" 
                            count="5" 
                            compact="true" 
                            nomonthheaders="true"
                            eventdesc="false"
                            timeformat="H:i"
                            showendtimes="true"
                            limitdays="330"
                        ]');

                    // If the output contains "No events found" or is empty
                    if (stripos($output, 'no events') !== false || trim(wp_strip_all_tags($output)) === '') {
                        echo '<p>' . esc_html__('No upcoming events found', 'devon-swimming') . '</p>';
                    } else {

                        echo '<div class="events-widget">' . $output . '</div>';
                    }
                } else {
                    // Shortcode doesn't exist at all
                    echo '<p>' . esc_html__('An error occured', 'devon-swimming') . '</p>';
                }

                // Link to the calendar page
                $calendar_page = get_page_by_path('calendar');
                $calendar_url  = $calendar_page ? get_permalink($calendar_page) : home_url();

                echo '<a class="button view-calendar-btn" href="' . esc_url($calendar_url) . '">' . esc_html__('View Full Calendar', 'devon-swimming') . '</a>';

                ?>



            </aside>

            <!-- Main content -->
            <div class="main-content-area">


                <?php if (have_posts()):
                    while (have_posts()): the_post();
                        get_template_part('template-parts/post');
                    endwhile; ?>

                    <!-- Infinite Scroll -->
                    <div id="infinite-scroll-sentinel" aria-hidden="true"></div>
                    <div id="infinite-scroll-status" class="infinite-scroll-status">
                        <span class="status-text">Loading more posts...</span>
                    </div>


                <?php else: ?>
                    <article>
                        <div class="post-content">
                            <h2 class="post-title"><?php _e('No Posts Found', 'devon-swimming'); ?></h2>
                            <p class="post-excerpt"><?php _e('Sorry, no posts matched your criteria.', 'devon-swimming'); ?></p>
                        </div>
                    </article>
                <?php endif; ?>

            </div>












            <!-- Right sidebar -->
            <aside class="sidebar sidebar-right" role="complementary" aria-label="<?php esc_attr_e('Notice Board', 'devon-swimming'); ?>">
                <h1 class="sidebar-header">
                    <?php esc_html_e('Notice Board', 'devon-swimming'); ?>
                    <?php
                    // Show "Create Notice" to users who can create notices
                    if (current_user_can('edit_posts')) {
                        $add_url = admin_url('post-new.php?post_type=notice');
                        echo ' <a class="button button--create-notice" href="' . esc_url($add_url) . '" target="_blank" rel="noopener">' . esc_html__('Create Notice', 'devon-swimming') . '</a>';
                    }
                    ?>
                </h1>

                <?php
                if (! function_exists('get_field')) {
                    echo '<p>' . esc_html__('Notice board not available', 'devon-swimming') . '</p>';
                } else {

                    $today = (int) current_time('Ymd');
                    $limit = 6;

                    // Helper to render one notice card (links always open in new tab)
                    $render_notice = function ($post_id) {
                        $expires_on = (int) get_field('expires_on', $post_id);
                        $text       = (string) get_field('text', $post_id);
                        $link_url   = trim((string) get_field('link_url', $post_id));
                        $link_text  = trim((string) get_field('link_text', $post_id));

                        // Single toggle: show/hide expiry to public
                        $show_expiry_public_raw = get_field('show_expiry_public', $post_id);
                        // Default: hidden if not set
                        $show_expiry_public = (bool) $show_expiry_public_raw;

                        $is_admin = current_user_can('edit_posts');

                        // Format expiry date (if set)
                        $expires_display = '';
                        if ($expires_on) {
                            $dt = DateTime::createFromFormat('Ymd', (string) $expires_on);
                            if ($dt) $expires_display = date_i18n(get_option('date_format'), $dt->getTimestamp());
                        }

                        // CTA (always new tab)
                        $cta_html = '';
                        if ($link_url) {
                            $label  = $link_text !== '' ? $link_text : __('Learn more', 'devon-swimming');
                            $cta_html = '<p class="notice-link"><a class="button" href="' . esc_url($link_url) . '" target="_blank" rel="noopener">' . esc_html($label) . '</a></p>';
                        }
                ?>
                        <article class="notice">
                            <h2 class="notice-title"><?php echo esc_html(get_the_title($post_id)); ?></h2>

                            <?php
                            // --- Expiry line ---
                            if ($is_admin) {
                                // Admins always see expiry (or "No expiry") + a badge showing public visibility
                                if ($expires_display) {
                                    echo '<p class="notice-meta">' .
                                        sprintf(esc_html__('Until %s', 'devon-swimming'), esc_html($expires_display)) .
                                        ' <span class="notice-admin-flag">' .
                                        ($show_expiry_public
                                            ? esc_html__('public', 'devon-swimming')
                                            : esc_html__('hidden', 'devon-swimming')) .
                                        '</span></p>';
                                } else {
                                    echo '<p class="notice-meta">' .
                                        esc_html__('No expiry', 'devon-swimming') .
                                        ' <span class="notice-admin-flag">' .
                                        ($show_expiry_public
                                            ? esc_html__('public', 'devon-swimming')
                                            : esc_html__('hidden', 'devon-swimming')) .
                                        '</span></p>';
                                }
                            } else {
                                // Public only sees expiry if toggle is on AND a value exists
                                if ($show_expiry_public && $expires_display) {
                                    echo '<p class="notice-meta">' .
                                        sprintf(esc_html__('Displays until %s', 'devon-swimming'), esc_html($expires_display)) .
                                        '</p>';
                                }
                            }
                            ?>

                            <div class="notice-text"><?php echo wpautop(wp_kses_post($text)); ?></div>
                            <?php echo $cta_html; ?>
                        </article>
                <?php
                    };


                    // 1) Time-sensitive (has expiry >= today), soonest first, then by manual Order
                    $time_sensitive = new WP_Query([
                        'post_type'      => 'notice',
                        'posts_per_page' => $limit,
                        'meta_key'       => 'expires_on',
                        'orderby'        => [
                            'meta_value_num' => 'ASC',
                            'menu_order'     => 'ASC',
                            'date'           => 'DESC',
                        ],
                        'meta_query'     => [[
                            'key'     => 'expires_on',
                            'value'   => $today,
                            'type'    => 'NUMERIC',
                            'compare' => '>='
                        ]],
                        'no_found_rows'  => true,
                        'ignore_sticky_posts' => true,
                        'fields'         => 'ids',
                    ]);

                    $printed = 0;
                    if ($time_sensitive->have_posts()) {
                        echo '<div class="notice-board">';
                        foreach ($time_sensitive->posts as $pid) {
                            $render_notice($pid);
                            $printed++;
                        }
                    } else {
                        echo '<div class="notice-board">';
                    }

                    // 2) Fill remaining slots with "no expiry" (no date set)
                    if ($printed < $limit) {
                        $no_expiry = new WP_Query([
                            'post_type'      => 'notice',
                            'posts_per_page' => ($limit - $printed),
                            'orderby'        => [
                                'menu_order' => 'ASC',
                                'date'       => 'DESC',
                            ],
                            'meta_query'     => [
                                'relation' => 'OR',
                                ['key' => 'expires_on', 'compare' => 'NOT EXISTS'],
                                ['key' => 'expires_on', 'value' => '', 'compare' => '='],
                            ],
                            'no_found_rows'  => true,
                            'ignore_sticky_posts' => true,
                            'fields'         => 'ids',
                        ]);
                        foreach ($no_expiry->posts as $pid) {
                            $render_notice($pid);
                            $printed++;
                        }
                    }

                    echo $printed ? '</div>' : '';
                    if (!$printed) {
                        echo '<p>' . esc_html__('No current notices', 'devon-swimming') . '</p>';
                    }

                    wp_reset_postdata();
                }
                ?>
            </aside>










        </div>
    </div>
</section>

<?php get_footer(); ?>