<?php







/* --------- Theme Setup --------- */

function my_theme_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');

    register_nav_menus([
        'primary' => __('Primary Menu', 'devon-swimming'),
        'links'   => __('Links Menu', 'devon-swimming'),
        'footer'  => __('Footer Menu', 'devon-swimming'),
    ]);
}
add_action('after_setup_theme', 'my_theme_setup');



/*--------- Styles and Scripts ---------*/
function my_theme_enqueue_files()
{
    // Main style.css from the theme root
    wp_enqueue_style('my-theme-style', get_stylesheet_uri());

    // Specific styles
    wp_enqueue_style('my-theme-header-style', get_template_directory_uri() . '/assets/css/header.css', [], null);
    wp_enqueue_style('my-theme-footer-style', get_template_directory_uri() . '/assets/css/footer.css', [], null);
    wp_enqueue_style('my-theme-front-page-style', get_template_directory_uri() . '/assets/css/front-page.css', [], null);
    wp_enqueue_style('my-theme-posts-style', get_template_directory_uri() . '/assets/css/posts.css', [], null);

    wp_enqueue_style('my-theme-single-style', get_template_directory_uri() . '/assets/css/single.css', [], null);
    wp_enqueue_style('my-theme-comments-style', get_template_directory_uri() . '/assets/css/comments.css', [], null);

    wp_enqueue_style('my-theme-clubs-style', get_template_directory_uri() . '/assets/css/clubs.css', [], null);
    wp_enqueue_style('my-theme-calendar-style', get_template_directory_uri() . '/assets/css/calendar.css', [], null);
    wp_enqueue_style('my-theme-tab-grid-style', get_template_directory_uri() . '/assets/css/tab-grid.css', [], null);
    wp_enqueue_style('my-theme-past-presidents-style', get_template_directory_uri() . '/assets/css/past-presidents.css', [], null);
    wp_enqueue_style('my-theme-about-us-style', get_template_directory_uri() . '/assets/css/about-us.css', [], null);
    wp_enqueue_style('my-theme-officials-style', get_template_directory_uri() . '/assets/css/officials.css', [], null);
    wp_enqueue_style('my-theme-safeguarding-welfare-style', get_template_directory_uri() . '/assets/css/safeguarding-welfare.css', [], null);
    wp_enqueue_style('my-theme-funding-support-style', get_template_directory_uri() . '/assets/css/funding-support.css', [], null);



    wp_enqueue_style('my-theme-two-column-section-style', get_template_directory_uri() . '/assets/css/two-column-section.css', [], null);
    wp_enqueue_style('my-theme-multiple-buttons-style', get_template_directory_uri() . '/assets/css/multiple-buttons.css', [], null);
    wp_enqueue_style('my-theme-grid-style', get_template_directory_uri() . '/assets/css/grid.css', [], null);



    // Scripts
    wp_enqueue_script('my-theme-header-script', get_template_directory_uri() . '/assets/js/header.js', [], null, true);
    wp_enqueue_script('my-theme-calendar-expand-script', get_template_directory_uri() . '/assets/js/calendar-expand.js', [], null, true);
    // Infinate scroll (on designated section)
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_files');



/* --------- Excerpts Config --------- */
add_filter('the_excerpt', function ($excerpt) {
    // Allow <a>, <strong>, <em>, <b>, <i>, <br>
    return wp_kses($excerpt, [
        'a' => [
            'href' => [],
            'title' => [],
            'target' => [],
            'rel' => [],
        ],
        'strong' => [],
        'em' => [],
        'b' => [],
        'i'  => [],
        'br' => [],
    ]);
});

/* Replace [...] with ... */
add_filter('excerpt_more', function ($more) {
    return '...';
});





/* --------- Sidebar Widgets --------- */
function devon_swimming_register_sidebars()
{
    register_sidebar([
        'name'          => __('Events Sidebar', 'devon-swimming'),
        'id'            => 'events-sidebar',
        'description'   => __('Left sidebar for events.', 'devon-swimming'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'devon_swimming_register_sidebars');











/* --------- Social Icons --------- */
/* Map a social URL to a slug used as the SVG. Fallback globe */


function devon_social_slug_from_url($url)
{
    $host = strtolower(wp_parse_url($url, PHP_URL_HOST) ?: '');

    if (strpos($host, 'facebook.com') !== false) return 'facebook';
    if (strpos($host, 'instagram.com') !== false) return 'instagram';
    if (strpos($host, 'twitter.com') !== false || strpos($host, 'x.com') !== false) return 'x';
    if (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false) return 'youtube';
    if (strpos($host, 'swimming.org') !== false) return 'swimengland';
    if (strpos($host, 'swimming.org') !== false || strpos($host, 'swimengland.org') !== false) return 'swimengland';

    // Default icon
    return 'globe';
}

/* Return SVG from /assets/icons/links/{slug}.svg*/
function devon_get_social_svg($url)
{
    $slug = devon_social_slug_from_url($url);

    // Candidate paths: child theme first, then parent theme.
    $paths = [
        trailingslashit(get_stylesheet_directory()) . 'assets/icons/links/' . $slug . '.svg',
        trailingslashit(get_template_directory())   . 'assets/icons/links/' . $slug . '.svg',
    ];

    $path = '';
    foreach ($paths as $p) {
        if (file_exists($p)) {
            $path = $p;
            break;
        }
    }

    // Fallback to globe.svg if slug-specific file missing.
    if (! $path) {
        $fallbacks = [
            trailingslashit(get_stylesheet_directory()) . 'assets/icons/links/globe.svg',
            trailingslashit(get_template_directory())   . 'assets/icons/links/globe.svg',
        ];
        foreach ($fallbacks as $p) {
            if (file_exists($p)) {
                $path = $p;
                break;
            }
        }
    }

    if (! $path) {
        return ''; // Nothing available
    }

    // Cache by absolute path + last modified time to bust when file changes.
    $cache_key = 'devon_svg_' . md5($path . '|' . filemtime($path));
    $svg       = get_transient($cache_key);

    if (false === $svg) {
        $svg = file_get_contents($path);
        if (! $svg) {
            return '';
        }

        // Basic hardening: strip potentially risky elements.
        $svg = preg_replace('#<(script|foreignObject)\b.*?>.*?</\1>#is', '', $svg);

        // Ensure decorative semantics if not set.
        if (strpos($svg, 'aria-hidden') === false) {
            $svg = preg_replace('#<svg\b#', '<svg aria-hidden="true" focusable="false"', $svg, 1);
        }

        set_transient($cache_key, $svg, DAY_IN_SECONDS);
    }

    return $svg;
}

/* Return SVG by a known slug (child theme first, then parent) */
function devon_get_social_svg_by_slug($slug) {
    $paths = [
        trailingslashit(get_stylesheet_directory()) . 'assets/icons/links/' . $slug . '.svg',
        trailingslashit(get_template_directory())   . 'assets/icons/links/' . $slug . '.svg',
    ];

    $path = '';
    foreach ($paths as $p) {
        if (file_exists($p)) { $path = $p; break; }
    }
    if (!$path) return '';

    // Cache by absolute path + mtime
    $cache_key = 'devon_svg_' . md5($path . '|' . filemtime($path));
    $svg = get_transient($cache_key);

    if ($svg === false) {
        $svg = file_get_contents($path);
        if (!$svg) return '';
        // Basic hardening
        $svg = preg_replace('#<(script|foreignObject)\b.*?>.*?</\1>#is', '', $svg);
        if (strpos($svg, 'aria-hidden') === false) {
            $svg = preg_replace('#<svg\b#', '<svg aria-hidden="true" focusable="false"', $svg, 1);
        }
        set_transient($cache_key, $svg, DAY_IN_SECONDS);
    }

    return $svg;
}


/* Social Walker */
class Devon_Walker_Social_Icons extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $classes = implode(' ', array_map('esc_attr', (array) $item->classes));
        $slug    = devon_social_slug_from_url($item->url);
        $icon    = devon_get_social_svg($item->url);
        $label   = '<span class="visually-hidden">' . esc_html($item->title) . '</span>';

        $output .= '<li class="menu-item social-item social--' . esc_attr($slug) . ' ' . $classes . '">';
        $output .= '<a class="social-link" href="' . esc_url($item->url) . '" target="_blank" rel="me noopener noreferrer">';

        if ($slug === 'swimengland') {
            // Large (default) + small (mobile) variants
            $icon_large = devon_get_social_svg_by_slug('swimengland');
            $icon_small = devon_get_social_svg_by_slug('swimengland-small');

            if ($icon_small) {
                $output .= '<span class="icon-swap">'
                         .   '<span class="icon--large">' . $icon_large . '</span>'
                         .   '<span class="icon--small">' . $icon_small . '</span>'
                         . '</span>';
            } else {
                // Fallback if small file missing
                $output .= $icon_large ?: $icon;
            }
        } else {
            $output .= $icon;
        }

        $output .= $label . '</a>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = []) {
        $output .= '</li>';
    }
}









/* --------- Infinite Scroll --------- */
function devon_enqueue_infinite_scroll()
{
    if (is_admin() || is_singular()) return;

    if (!(is_home() || is_archive() || is_search())) return;

    global $wp_query;
    if (empty($wp_query->found_posts)) return; // No posts to load

    wp_enqueue_script(
        'devon-infinite-scroll',
        get_template_directory_uri() . '/assets/js/infinite-scroll.js',
        ['jquery'],
        null,
        true
    );

    global $wp_query;

    wp_localize_script('devon-infinite-scroll', 'devonScroll', array(
        'ajaxurl'       => admin_url('admin-ajax.php'),
        'query_vars'    => wp_json_encode($wp_query->query),
        'current_page'  => (int) max(1, get_query_var('paged')),
        'max_page'      => (int) $wp_query->max_num_pages,
        'nonce'         => wp_create_nonce('devon_infinite_scroll'),
        'container'     => '.main-content-area',
        'sentinel'      => '#infinite-scroll-sentinel',
        'loader'        => '#infinite-scroll-loader',
    ));
}
add_action('wp_enqueue_scripts', 'devon_enqueue_infinite_scroll');

function devon_infinite_scroll_load_posts()
{
    check_ajax_referer('devon_infinite_scroll', 'nonce');

    $paged = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
    $query_vars = isset($_POST['query_vars']) ? json_decode(stripslashes($_POST['query_vars']), true) : array();

    $query_vars['paged'] = $paged + 1;

    if (!isset($query_vars['posts_per_page'])) {
        $query_vars['posts_per_page'] = get_option('posts_per_page');
    }

    $query = new WP_Query($query_vars);

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/post');
        }
        wp_reset_postdata();
        echo ob_get_clean();
    }

    wp_die();
}

add_action('wp_ajax_devon_infinite_scroll', 'devon_infinite_scroll_load_posts');
add_action('wp_ajax_nopriv_devon_infinite_scroll', 'devon_infinite_scroll_load_posts');

















/* --------- Footer Widget --------- */
function devon_swimming_register_footer_widget()
{
    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'devon-swimming'),
        'id'            => 'footer-widget',
        'description'   => __('Widgets added here will appear in the left side of the footer.', 'devon-swimming'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'devon_swimming_register_footer_widget');

function theme_customize_register($wp_customize)
{
    $wp_customize->add_section('footer_section', array(
        'title'       => __('Footer Settings', 'your-theme-slug'),
        'priority'    => 160,
    ));

    $wp_customize->add_setting('footer_copyright', array(
        'default'   => 'Devon Swimming',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('footer_copyright', array(
        'label'    => __('Footer Text', 'your-theme-slug'),
        'section'  => 'footer_section',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'theme_customize_register');

















/* --------- Upcoming Events --------- */
// Helper: find the first page using the Calendar template (adjust filename if yours is different)
function ms_get_calendar_page_id($template_file = 'template-calendar.php')
{
    $pages = get_pages([
        'meta_key'   => '_wp_page_template',
        'meta_value' => $template_file,
        'number'     => 1,
    ]);
    return $pages ? (int) $pages[0]->ID : 0;
}

/**
 * Bump a cache-buster whenever the calendar URLs change,
 * so any transients are invalidated automatically.
 */
function ms_upcoming_bump_cache_buster()
{
    $key = 'ms_upcoming_cache_buster';
    $v   = (int) get_option($key, 0);
    update_option($key, $v + 1, false);
}

/* Invalidate cache when the page is saved */
add_action('save_post_page', function ($post_id, $post, $update) {
    if (wp_is_post_revision($post_id)) return;
    // Only bump when saving a page that uses our calendar template
    $tpl = get_page_template_slug($post_id);
    if ($tpl === 'template-calendar.php') {
        ms_upcoming_bump_cache_buster();
    }
}, 10, 3);


/* Invalidate cache when either ACF field changes (works with ACF Free) */
foreach (['ms_calendar_ics_url', 'ms_calendar_embed_url'] as $acf_field_name) {
    add_filter("acf/update_value/name={$acf_field_name}", function ($value) {
        ms_upcoming_bump_cache_buster();
        return $value;
    });
}


// [ms_upcoming_events count="5" page_id="123" page_slug="events" view="list" tz="Europe/London" compact="true"]
add_shortcode('ms_upcoming_events', function ($atts) {
    if (!function_exists('get_field')) {
        return '<!-- ACF is not active -->';
    }
    if (!shortcode_exists('ics_calendar')) {
        return '<!-- ICS Calendar plugin not active -->';
    }

    $site_tz = function_exists('wp_timezone_string') ? wp_timezone_string() : get_option('timezone_string');

    // Defaults that map cleanly to ICS Calendar's current params
    $atts = shortcode_atts([
        'count'           => '5',
        'view'            => 'list',
        'tz'              => $site_tz ?: 'Europe/London',
        'compact'         => 'true',
        // use eventdesc, not desc; integers show an excerpt length (in words)
        'eventdesc'       => 'false',
        'timeformat'      => 'H:i',
        'showendtimes'    => 'true',
        'format'          => 'M j D',
        'combinemultiday' => 'false',
        // valid values: false | allday | overnight | both
        'extendmultiday'  => 'overnight',
        'nomonthheaders'  => 'true',
        'limitdays'  => '',

        // Newer/common params you might want to override:
        'ajax'            => 'false',     // set "true" if page caching interferes
        'monthnav'        => 'both',          // supports arrows|dropdown|both|none
        'stickymonths'    => 'true',
        'linktitles'      => 'false',
        'eventdl'         => 'false',
        'category'        => '',          // 11.5.2 — works only if your feed uses CATEGORIES

        'ua'             => 'WordPress ICS Calendar', // add UA so cURL sends a header

        // internal helpers (not passed to ICS Calendar)
        'page_id'         => '',
        'page_slug'       => '',
        'template'        => 'template-calendar.php',
        'cache'           => 'true',
        'cache_ttl'       => '300',
        // legacy alias support (we'll map it below if present)
        'desc'            => '',
        'pagination'      => '',
    ], $atts, 'ms_upcoming_events');

    // Resolve page ID: explicit > slug > first page using the template
    $page_id = (int) $atts['page_id'];
    if (!$page_id && $atts['page_slug']) {
        $p = get_page_by_path(sanitize_title($atts['page_slug']), OBJECT, 'page');
        if ($p) $page_id = (int) $p->ID;
    }
    if (!$page_id) {
        $pages = get_pages([
            'meta_key'   => '_wp_page_template',
            'meta_value' => $atts['template'],
            'number'     => 1,
        ]);
        if ($pages) $page_id = (int) $pages[0]->ID;
    }
    if (!$page_id) {
        return '<!-- Could not locate a page using the Calendar template. Provide page_id="" in the shortcode -->';
    }

    $ics_url = trim((string) get_field('ms_calendar_ics_url', $page_id));
    if (empty($ics_url)) {
        return '<!-- Add Microsoft ICS URL on the Calendar page -->';
    }

    // Map legacy attr `desc` -> `eventdesc` if a numeric/boolean-like value was supplied
    if ($atts['desc'] !== '') {
        $atts['eventdesc'] = $atts['desc'];
    }

    /**
     * Whitelist of ICS Calendar (free) shortcode params.
     * Pulled from the live parameter reference (trimmed to non-Pro items). This avoids
     * silently dropping newer params as ICS evolves (e.g., ajax, category, monthnav).
     */
    $allowed = [
        // Core
        'view',
        'count',
        'tz',
        'compact',
        'format',
        'timeformat',
        'pagination',
        'startdate',
        'pastdays',
        'limitdays',
        'reverse',

        // Events/details
        'eventdesc',
        'linktitles',
        'nolink',
        'eventdl',
        'hidetimes',
        'hiderecurrence',
        'hidecancelledevents',
        'hideprivateevents',
        'hidetentativeevents',
        'hidealldayindicator',
        'color',
        'category',
        'eventlocaltime',

        // Layout/UX
        'ajax',
        'monthnav',
        'stickymonths',
        'columnlabels',
        'legendposition',
        'combinemultiday',
        'extendmultiday',
        'nomonthheaders',
        'mapsource',
        'maskinfo',
        'subscribelink',
        'whitetext',
        'htmltagtitle',
        'htmltagmonth',
        'htmltagdate',
        'htmltagtime',
        'htmltageventtitle',
        'htmltageventdesc',
        'weeknumbers',
        'guid',
        'reload',
        'skip',
        'skipdomainerrors',
        'skiprecurrence',
        'solidcolors',
        'tablebg',

        // (FYI: Pro-only params intentionally not listed)
        'ua',
        'debug',
    ];

    // Build ICS Calendar shortcode
    $pass = [];
    foreach ($atts as $k => $v) {
        if (in_array($k, $allowed, true) && $v !== '' && $v !== null) {
            $pass[$k] = $v;
        }
    }

    // Build ICS Calendar shortcode
    $parts = [];
    foreach ($pass as $k => $v) {
        $parts[] = $k . '="' . esc_attr($v) . '"';
    }
    $ics_sc = '[ics_calendar url="' . esc_url($ics_url) . '" ' . implode(' ', $parts) . ']';

    // Optional cache
    if ($atts['cache'] === 'true') {
        $buster = (int) get_option('ms_upcoming_cache_buster', 0);
        $key    = 'ms_upcoming_' . md5($ics_sc . '|' . $buster);
        $cached = get_transient($key);
        if ($cached !== false) return $cached;

        $out = do_shortcode($ics_sc);

        // 👇 don't cache “empty”
        if (trim(wp_strip_all_tags((string)$out)) === '') {
            return $out ?: '<!-- ICS upcoming: empty response (not cached) -->';
        }

        set_transient($key, $out, max(60, (int)$atts['cache_ttl']));
        return $out;
    }


    return do_shortcode($ics_sc);
});









































/* --------- Past Presidents --------- */
// Register "President" custom post type
add_action('init', function () {
    $labels = [
        'name'               => 'Presidents',
        'singular_name'      => 'President',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New President',
        'edit_item'          => 'Edit President',
        'new_item'           => 'New President',
        'view_item'          => 'View President',
        'search_items'       => 'Search Presidents',
        'not_found'          => 'No presidents found',
        'not_found_in_trash' => 'No presidents found in Trash',
        'all_items'          => 'All Presidents',
        'menu_name'          => 'Presidents',
    ];
    register_post_type('president', [
        'labels' => $labels,
        'public' => true,
        'show_in_rest' => true, // Gutenberg
        'menu_position' => 21,
        'menu_icon' => 'dashicons-groups',
        'supports' => ['title', 'editor', 'thumbnail', 'revisions'],
        'has_archive' => false,
        'rewrite' => ['slug' => 'president'],
    ]);
});

// Admin list columns for Presidents
add_filter('manage_president_posts_columns', function ($cols) {
    $cols['year_start'] = 'Start Year';
    $cols['year_end']   = 'End Year';
    return $cols;
});
add_action('manage_president_posts_custom_column', function ($col, $post_id) {
    if ($col === 'year_start') echo esc_html(get_field('year_start', $post_id));
    if ($col === 'year_end')   echo esc_html(get_field('year_end', $post_id));
}, 10, 2);











// Posst prefetching for single posts
add_action('wp_head', function () {
    if (!is_single()) return;
    $prev = get_previous_post();
    $next = get_next_post();
    if ($prev) echo '<link rel="prefetch" href="' . esc_url(get_permalink($prev)) . '">';
    if ($next) echo '<link rel="prefetch" href="' . esc_url(get_permalink($next)) . '">';
}, 2);












// --- Notices CPT (ACF Free friendly) ---
add_action('init', function () {
    $labels = [
        'name'               => __('Notices', 'devon-swimming'),
        'singular_name'      => __('Notice', 'devon-swimming'),
        'add_new'            => __('Add New Notice', 'devon-swimming'),
        'add_new_item'       => __('Add New Notice', 'devon-swimming'),
        'edit_item'          => __('Edit Notice', 'devon-swimming'),
        'new_item'           => __('New Notice', 'devon-swimming'),
        'view_item'          => __('View Notice', 'devon-swimming'),
        'search_items'       => __('Search Notices', 'devon-swimming'),
        'not_found'          => __('No notices found', 'devon-swimming'),
        'not_found_in_trash' => __('No notices found in Trash', 'devon-swimming'),
        'menu_name'          => __('Notices', 'devon-swimming'),
    ];

    register_post_type('notice', [
        'labels'             => $labels,
        'public'             => false,            // backend only; not publicly accessible
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 58,
        'menu_icon'          => 'dashicons-megaphone',
        'supports'           => ['title', 'page-attributes'], // title + "Order" for manual sort
        'has_archive'        => false,
        'exclude_from_search' => true,
        'rewrite'            => false,
    ]);
});
// --- Admin columns: Status + Expires On for Notices ---
add_filter('manage_notice_posts_columns', function ($cols) {
    // Insert after title
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['notice_status'] = __('Status', 'devon-swimming');
            $new['expires_on']    = __('Expires On', 'devon-swimming');
        }
    }
    return $new;
});

add_action('manage_notice_posts_custom_column', function ($col, $post_id) {
    if ($col === 'notice_status' || $col === 'expires_on') {
        $today      = (int) current_time('Ymd');
        $expires_on = (int) get_field('expires_on', $post_id);

        if ($col === 'notice_status') {
            if (!$expires_on) {
                echo '<span class="status status--no-expiry">' . esc_html__('No expiry', 'devon-swimming') . '</span>';
            } elseif ($expires_on >= $today) {
                echo '<span class="status status--active">' . esc_html__('Active', 'devon-swimming') . '</span>';
            } else {
                echo '<span class="status status--expired">' . esc_html__('Expired', 'devon-swimming') . '</span>';
            }
        }

        if ($col === 'expires_on') {
            if ($expires_on) {
                $dt = DateTime::createFromFormat('Ymd', (string) $expires_on);
                echo esc_html($dt ? date_i18n(get_option('date_format'), $dt->getTimestamp()) : $expires_on);
            } else {
                echo '—';
            }
        }
    }
}, 10, 2);

// Small admin CSS for badges
add_action('admin_head', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'edit-notice') return; ?>
    <style>
        .column-notice_status {
            width: 110px;
        }

        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 12px;
            line-height: 1.5;
            color: #111;
            background: #e5e7eb;
        }

        .status--active {
            background: #bbf7d0;
        }

        /* green-ish */
        .status--expired {
            background: #fecaca;
        }

        /* red-ish */
        .status--no-expiry {
            background: #fde68a;
        }

        /* amber-ish */
    </style>
<?php
});

// Make "Expires On" sortable in admin
add_filter('manage_edit-notice_sortable_columns', function ($cols) {
    $cols['expires_on'] = 'expires_on';
    return $cols;
});
add_action('pre_get_posts', function ($q) {
    if (!is_admin() || !$q->is_main_query()) return;
    if ($q->get('post_type') !== 'notice') return;

    if ($q->get('orderby') === 'expires_on') {
        $q->set('meta_key', 'expires_on');
        $q->set('orderby', 'meta_value_num');
    }

    // Apply status filter dropdown (see below)
    $status = isset($_GET['notice_status']) ? sanitize_text_field($_GET['notice_status']) : '';
    $today  = (int) current_time('Ymd');

    if ($status === 'active') {
        $q->set('meta_query', [[
            'key'     => 'expires_on',
            'value'   => $today,
            'type'    => 'NUMERIC',
            'compare' => '>='
        ]]);
    } elseif ($status === 'expired') {
        $q->set('meta_query', [[
            'key'     => 'expires_on',
            'value'   => $today,
            'type'    => 'NUMERIC',
            'compare' => '<'
        ]]);
    } elseif ($status === 'noexp') {
        $q->set('meta_query', [
            'relation' => 'OR',
            ['key' => 'expires_on', 'compare' => 'NOT EXISTS'],
            ['key' => 'expires_on', 'value' => '', 'compare' => '='],
        ]);
    }
});

// Add a status filter dropdown on Notices list
add_action('restrict_manage_posts', function ($post_type) {
    if ($post_type !== 'notice') return;
    $current = isset($_GET['notice_status']) ? sanitize_text_field($_GET['notice_status']) : '';
?>
    <label for="filter-by-notice-status" class="screen-reader-text"><?php esc_html_e('Filter by status', 'devon-swimming'); ?></label>
    <select name="notice_status" id="filter-by-notice-status">
        <option value=""><?php esc_html_e('All statuses', 'devon-swimming'); ?></option>
        <option value="active" <?php selected($current, 'active'); ?>><?php esc_html_e('Active', 'devon-swimming'); ?></option>
        <option value="expired" <?php selected($current, 'expired'); ?>><?php esc_html_e('Expired', 'devon-swimming'); ?></option>
        <option value="noexp" <?php selected($current, 'noexp'); ?>><?php esc_html_e('No expiry', 'devon-swimming'); ?></option>
    </select>
<?php
});
































































































/* --------- WORDPRESS HIDE UPDATES NOTIFICATIONS --------- */
/*
 * Zero-out the numeric update bubbles in the sidebar + toolbar menus
 * (Does not block update checks. only hides the counts/alerts)
 */
add_filter('wp_get_update_data', function ($update_data) {
    if (isset($update_data['counts']) && is_array($update_data['counts'])) {
        foreach ($update_data['counts'] as $k => $v) {
            $update_data['counts'][$k] = 0;
        }
    }
    // Also clear the toolbar title like "Updates 3"
    $update_data['title'] = '';
    return $update_data;
}, 99);
