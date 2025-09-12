<?php
/**
 * Past Presidents — Auto-calculated end (next start), "Current" chips, term count
 * CPT: president
 * ACF: year_start (int)  ← year_end removed
 */

if (!defined('ABSPATH')) { exit; }

/** Initials fallback (first + last initial) */
function pp_initials($name) {
  $name = trim((string) $name);
  if ($name === '') return '';
  $parts = preg_split('/\s+/', $name) ?: [];
  $first = mb_substr($parts[0] ?? '', 0, 1);
  $last  = mb_substr($parts[count($parts) - 1] ?? '', 0, 1);
  $init  = mb_strtoupper($first . ($last !== $first ? $last : ''));
  return $init;
}

/** Term count in "seasons": past => end - start; current => currentYear - start */
function pp_terms_served($start, $end) {
  if (!is_numeric($start)) return null;
  $s = (int) $start;

  if (is_string($end) && strtolower($end) === 'current') {
    $e = (int) date('Y');
    $t = $e - $s;                 // e.g., 2025-start(2025) => 0 so far
    return ($t > 0) ? $t : null;  // hide 0
  }

  if (is_numeric($end)) {
    $e = (int) $end;
    $t = $e - $s;                 // 1901-1902 => 1 term
    return ($t > 0) ? $t : null;  // hide 0 (same-year handover)
  }

  return null;
}

/**
 * Compute display end years from neighbours.
 * Assumes $rows sorted newest → oldest by year_start (desc).
 * - Most recent (index 0): end = "Current".
 * - Others: end = nextStart (NOT nextStart - 1).
 */
function pp_compute_end_years(array $rows) {
  $n = count($rows);
  for ($i = 0; $i < $n; $i++) {
    $start = $rows[$i]['year_start'];
    if ($i === 0) {
      $rows[$i]['year_end'] = 'Current';
    } else {
      $nextStart = $rows[$i - 1]['year_start']; // newer record
      $rows[$i]['year_end'] = (is_numeric($start) && is_numeric($nextStart)) ? (int) $nextStart : null;
    }
  }
  return $rows;
}

// Query - newest first by ACF year_start (fallback date)
$q = new WP_Query([
  'post_type'           => 'president',
  'posts_per_page'      => -1,
  'post_status'         => 'publish',
  'ignore_sticky_posts' => true,
  'meta_key'            => 'year_start',
  'orderby'             => [
    'meta_value_num' => 'DESC',
    'date'           => 'DESC',
  ],
]);

$rows = []; // flat rows newest → oldest

if ($q->have_posts()) {
  while ($q->have_posts()) {
    $q->the_post();

    $name    = get_the_title();
    $content = get_the_excerpt() ?: wp_strip_all_tags(get_the_content(null, false));
    $bio     = $content ? wp_trim_words($content, 40, '…') : '';

    $ys = get_field('year_start'); // int

    $img_id  = get_post_thumbnail_id();
    $img_src = $img_id ? wp_get_attachment_image_src($img_id, 'thumbnail') : null;
    $img_alt = $img_id ? get_post_meta($img_id, '_wp_attachment_image_alt', true) : '';

    $rows[] = [
      'name'       => $name,
      'bio'        => $bio,
      'year_start' => is_numeric($ys) ? (int) $ys : null,
      'img'        => $img_src ? [
        'src'    => $img_src[0],
        'width'  => (int) $img_src[1],
        'height' => (int) $img_src[2],
        'alt'    => $img_alt ?: $name,
      ] : null,
    ];
  }
  wp_reset_postdata();
}

// Compute end years (display) from neighbours
$rows = pp_compute_end_years($rows);

// Build groups (by decade of start; fall back to end if start missing)
$groups  = [];
$decades = [];

foreach ($rows as $r) {
  $pivot  = is_numeric($r['year_start']) ? (int) $r['year_start']
          : (is_numeric($r['year_end'])   ? (int) $r['year_end'] : null);
  $decade = $pivot ? (int) (floor($pivot / 10) * 10) : 0;

  $served = pp_terms_served($r['year_start'], $r['year_end']);

  $groups[$decade][] = [
    'name'       => $r['name'],
    'initials'   => pp_initials($r['name']),
    'start'      => $r['year_start'],
    'end'        => $r['year_end'], // int | "Current" | null
    'served'     => $served,        // null when 0/unknown
    'bio'        => $r['bio'],
    'img'        => $r['img'],
  ];
}

// Sort decades DESC, unknown (0) last
if (!empty($groups)) {
  krsort($groups, SORT_NUMERIC);
  if (isset($groups[0])) { $unknown = $groups[0]; unset($groups[0]); $groups[0] = $unknown; }
  foreach (array_keys($groups) as $d) { if ($d > 0) $decades[] = $d; }
}
?>

<section class="presidents" aria-label="<?php echo esc_attr__('Past Presidents', 'devon-swimming'); ?>">
  <div class="presidents-header">
    <div class="presidents-jump">
      <label for="pp-jump"><?php echo esc_html__('Jump to decade', 'devon-swimming'); ?></label>
      <select id="pp-jump" aria-label="<?php echo esc_attr__('Jump to decade', 'devon-swimming'); ?>">
        <option value=""><?php echo esc_html__('— Select —', 'devon-swimming'); ?></option>
        <?php foreach ($decades as $dec): ?>
          <option value="#decade-<?php echo esc_attr($dec); ?>"><?php echo esc_html($dec . 's'); ?></option>
        <?php endforeach; ?>
        <?php if (!empty($groups[0])): ?>
          <option value="#decade-unknown"><?php echo esc_html__('Unknown decade', 'devon-swimming'); ?></option>
        <?php endif; ?>
      </select>
    </div>

    <div class="presidents-filter">
      <label for="pp-filter"><?php echo esc_html__('Filter by name', 'devon-swimming'); ?></label>
      <input id="pp-filter" type="search" placeholder="<?php echo esc_attr__('Type a name…', 'devon-swimming'); ?>" autocomplete="off" />
    </div>
  </div>

  <?php if (!empty($groups)) : ?>
    <?php foreach ($groups as $dec => $items): ?>
      <?php
        $anchor  = $dec > 0 ? 'decade-' . $dec : 'decade-unknown';
        $heading = $dec > 0 ? ($dec . 's') : __('Unknown decade', 'devon-swimming');
      ?>
      <h3 id="<?php echo esc_attr($anchor); ?>" class="presidents-decade">
        <?php echo esc_html($heading); ?>
      </h3>

      <ul class="presidents-list" role="list">
        <?php foreach ($items as $p): ?>
          <li class="president-row"
              data-name="<?php echo esc_attr(mb_strtolower($p['name'])); ?>"
              itemscope itemtype="https://schema.org/Person">

            <?php if (!empty($p['img'])): ?>
              <div class="avatar">
                <img
                  src="<?php echo esc_url($p['img']['src']); ?>"
                  alt="<?php echo esc_attr($p['img']['alt']); ?>"
                  width="<?php echo esc_attr($p['img']['width']); ?>"
                  height="<?php echo esc_attr($p['img']['height']); ?>"
                  loading="lazy" decoding="async" />
              </div>
            <?php else: ?>
              <div class="avatar avatar--initials" aria-hidden="true"
                   data-initials="<?php echo esc_attr($p['initials']); ?>"></div>
            <?php endif; ?>

            <div class="meta">
              <div class="meta__top">
                <span class="name" itemprop="name"><?php echo esc_html($p['name']); ?></span>

                <?php
                  $is_current = is_string($p['end']) && strtolower($p['end']) === 'current';
                  if ($is_current):
                ?>
                  <?php if (is_numeric($p['start'])): ?>
                    <span class="chip-predisent chip--years"><?php echo esc_html((string) $p['start']); ?></span>
                  <?php endif; ?>
                  <span class="chip-predisent chip--years chip--current"><?php echo esc_html__('Current', 'devon-swimming'); ?></span>
                <?php else: ?>
                  <?php if (is_numeric($p['start']) && is_numeric($p['end'])): ?>
                    <span class="chip-predisent chip--years"><?php echo esc_html($p['start'] . '-' . $p['end']); ?></span>
                  <?php elseif (is_numeric($p['start'])): ?>
                    <span class="chip-predisent chip--years"><?php echo esc_html((string) $p['start']); ?></span>
                  <?php endif; ?>
                <?php endif; ?>

                <?php if (!is_null($p['served'])): ?>
                  <span class="chip-predisent chip--served">
                    <?php echo esc_html($p['served']); ?>
                    <?php echo $p['served'] === 1
                      ? esc_html__('year', 'devon-swimming')
                      : esc_html__('years', 'devon-swimming'); ?>
                  </span>
                <?php endif; ?>
              </div>

              <?php if (!empty($p['bio'])): ?>
                <p class="bio" itemprop="description"><?php echo esc_html($p['bio']); ?></p>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="pp-empty"><?php echo esc_html__('No past presidents found.', 'devon-swimming'); ?></p>
  <?php endif; ?>
</section>

<script>
(function(){
  const jump   = document.getElementById('pp-jump');
  const filter = document.getElementById('pp-filter');

  // --- configurable offset ---
  // Set to 0.33 for one-third, 0.5 for half-way, or any 0-1 ratio of the viewport height
  const RATIO = 0.33;

  function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  function scrollToWithOffset(el, behavior = 'smooth') {
    if (!el) return;

    const rect   = el.getBoundingClientRect();
    const yNow   = window.scrollY || window.pageYOffset || 0;
    const vh     = window.innerHeight || document.documentElement.clientHeight || 0;
    const offset = Math.max(0, Math.round(vh * RATIO));

    // Position the element so its top is offset down by the chosen ratio
    const targetY = Math.max(0, yNow + rect.top - offset);

    window.scrollTo({
      top: targetY,
      behavior: prefersReducedMotion() ? 'auto' : behavior
    });
  }

  // Smooth jump + keep hash shareable
  if (jump) {
    jump.addEventListener('change', () => {
      const v = jump.value;
      if (!v) return;
      const el = document.querySelector(v);
      if (el) {
        scrollToWithOffset(el, 'smooth');
        const url = new URL(window.location.href);
        url.hash = v.slice(1);
        history.replaceState(null, '', url.toString());
      }
    });
  }

  // Debounced filter
  if (filter) {
    const lists = document.querySelectorAll('.presidents-list');
    let t = 0;
    const apply = () => {
      const q = (filter.value || '').toLowerCase().trim();
      lists.forEach(ul => {
        let shown = 0;
        ul.querySelectorAll('.president-row').forEach(li => {
          const name = (li.getAttribute('data-name') || '');
          const ok = !q || name.includes(q);
          li.style.display = ok ? '' : 'none';
          if (ok) shown++;
        });
        const header = ul.previousElementSibling;
        if (header && header.classList.contains('presidents-decade')) {
          const empty = shown === 0;
          ul.style.display = empty ? 'none' : '';
          header.style.display = empty ? 'none' : '';
        }
      });
    };
    const debounced = () => { clearTimeout(t); t = setTimeout(apply, 120); };
    filter.addEventListener('input', debounced, { passive: true });
  }

  // Respect existing #decade-XXXX hash on load (use the same offset logic)
  (function() {
    const id = window.location.hash && window.location.hash.replace(/^#/, '');
    if (!id) return;
    const el = document.getElementById(id);
    if (el) {
      // Use 'auto' to avoid animated jumping on initial load
      scrollToWithOffset(el, 'auto');
    }
  })();
})();
</script>
