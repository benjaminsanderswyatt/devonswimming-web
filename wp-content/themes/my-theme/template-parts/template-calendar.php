<?php
/**
 * Template Name: Calendar
 */
get_header();

$ics_url = function_exists('get_field') ? trim((string) get_field('ms_calendar_ics_url')) : '';
$embed_url = function_exists('get_field') ? trim((string) get_field('ms_calendar_embed_url')) : '';


$header = function_exists('get_field') ? get_field('header') : '';
$text = function_exists('get_field') ? get_field('text') : '';
?>

<div class="site-main template-calendar">

    <!-- Display header and text if either is set -->
    <?php if ($header || $text): ?>
        <div class="container">
            <div class="container-content">
                <h2><?php the_field('header'); ?></h2>
                <p><?php the_field('text'); ?></p>
            </div>
        </div>
    <?php endif; ?>


    <div class="calendar-body container">
        <?php
        if (!$ics_url) {
            echo '<p>' . esc_html__('Please add the Microsoft ICS URL in the fields on this page.', 'devon-swimming') . '</p>';
        } elseif (!shortcode_exists('ics_calendar')) {
            echo '<p>' . esc_html__('ICS Calendar plugin is not active.', 'devon-swimming') . '</p>';
        } else {
            // Common attrs
            $atts = [
                'url'          => esc_url($ics_url),
                'view'         => 'month',
                'ajax'         => 'true',       // flip to "true" if you run page caching
                'monthnav'     => 'both',       // arrows + dropdown (valid values: arrows|dropdown|both|none)
                'columnlabels' => 'short',      // Mon/Tue abbreviated
                'eventdesc'    => 'true',       // show description on hover/tap
                'location'     => 'maplinks',   // location becomes a map link (if present)
                'mapsource'    => 'google',     // map source
                'organizer'    => 'true',
                'timeformat'   => 'H:i',
                'showendtimes' => 'true',
                'ua'           => 'WordPress ICS Calendar', // add UA so cURL sends a header
                'debug'        => 'false', // set to "true" to enable debug mode (useful for troubleshooting)
                // inherit site timezone; free plugin picks this up automatically
            ];

            $parts = [];
            foreach ($atts as $k => $v) {
                $parts[] = $k . '="' . esc_attr($v) . '"';
            }

            echo do_shortcode('[ics_calendar ' . implode(' ', $parts) . ']');
        }
        ?>
        <noscript>
            <p><?php esc_html_e('JavaScript is required to view the calendar.', 'devon-swimming'); ?></p>
        </noscript>
    </div>



</div>
<?php get_footer(); ?>