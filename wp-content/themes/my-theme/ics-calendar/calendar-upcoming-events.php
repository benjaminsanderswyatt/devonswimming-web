<?php
// $args = validated shortcode args (e.g. count, timeformat, showendtimes, eventdesc, etc.)
// $ics_data = fully parsed data. Events are in $ics_data['events'] in chronological order.

$events = $ics_data['events'] ?? [];
$limit  = isset($args['count']) ? (int) $args['count'] : 10;
$timefmt = $args['timeformat'] ?? 'H:i';
$show_end = ($args['showendtimes'] ?? 'true') === 'true';

echo '<div class="devon-events">';
$i = 0;
foreach ($events as $event) {
    if ($i++ >= $limit) break;

    $title   = esc_html($event['summary'] ?? '');
    $url     = !empty($event['url']) ? esc_url($event['url']) : '';
    $start   = isset($event['start']) ? (int) $event['start'] : 0; // Unix timestamp
    $end     = isset($event['end']) ? (int) $event['end'] : 0;
    $all_day = !empty($event['allday']);
    $loc     = esc_html($event['location'] ?? '');

    // Optional desc trimming if you pass eventdesc="12" (words)
    $excerpt = '';
    if (!empty($args['eventdesc']) && $args['eventdesc'] !== 'false') {
        $desc = wp_strip_all_tags($event['description'] ?? '');
        if (is_numeric($args['eventdesc'])) {
            $words = preg_split('/\s+/', trim($desc));
            $excerpt = implode(' ', array_slice($words, 0, (int) $args['eventdesc']));
            if (count($words) > (int) $args['eventdesc']) $excerpt .= '…';
        } else {
            $excerpt = $desc;
        }
        $excerpt = esc_html($excerpt);
    }

    echo '<article class="event-card">';
        echo '<h4 class="event-title">' . ($url ? '<a href="'.$url.'">'.$title.'</a>' : $title) . '</h4>';

        echo '<div class="event-meta">';
            echo '<time datetime="' . esc_attr(gmdate('c', $start)) . '">'
               . esc_html(date_i18n('D j M Y', $start)) . '</time>';
            if (!$all_day && $start) {
                echo ' · ' . esc_html(date_i18n($timefmt, $start));
                if ($show_end && $end) echo '-' . esc_html(date_i18n($timefmt, $end));
            }
            if ($loc) echo ' · <span class="event-location">'.$loc.'</span>';
        echo '</div>';

        if ($excerpt) {
            echo '<p class="event-excerpt">'.$excerpt.'</p>';
        }
    echo '</article>';
}
echo '</div>';
