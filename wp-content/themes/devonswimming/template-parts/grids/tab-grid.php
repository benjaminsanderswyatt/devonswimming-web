<?php

/**
 * Generic Tab Grid
 *
 * Usage via:
 *   get_template_part('template-parts/layout/tab-grid', null, [
 *     'aria_label'      => 'Committees',
 *     'sidebar_heading' => null, // or 'Trophies'
 *     'active'          => 'backstroke', // slug of initial tab
 *     'order_tokens'    => $order_tokens, // optional; passed to cards-grid
 *     'id_base'         => 'committees',  // optional; ensures unique IDs on page
 *     'hash_cleanup'    => [              // optional; clears specific URL hashes when leaving a tab
 *       'keep_slug' => 'past_presidents',
 *       'regex'     => '^#decade-\\d{4}$|^#decade-unknown$'
 *     ],
 *     'tabs' => [
 *       // Each tab:
 *       // 'slug'   => unique slug (required)
 *       // 'label'  => visible text (required)
 *       // 'panel'  => what to render (required)
 *       //   Default (cards grid): ['type' => 'cards', 'prefix' => 'management']
 *       //   Custom template:      ['type' => 'template_part', 'template' => 'template-parts/presidents-timeline', 'args' => []]
 *       ['slug' => 'management', 'label' => 'Management Committee', 'panel' => ['type' => 'cards', 'prefix' => 'management']],
 *       // ...
 *     ],
 *   ]);
 */

$args = is_array($args ?? null) ? $args : [];

$tabs            = $args['tabs']            ?? [];
$active_slug     = $args['active']          ?? ($tabs[0]['slug'] ?? null);
$aria_label      = $args['aria_label']      ?? 'Tabbed Sections';
$sidebar_heading = $args['sidebar_heading'] ?? null;
$order_tokens    = $args['order_tokens']    ?? [];
$id_base_raw     = $args['id_base']         ?? 'tabgrid';
$hash_cleanup    = $args['hash_cleanup']    ?? null;

// Ensure stable, unique base for element IDs
if (function_exists('sanitize_title')) {
    $id_base_raw = sanitize_title($id_base_raw);
}
if (!function_exists('wp_unique_id')) {
    // Fallback if running on very old WP
    $uniq = substr(md5(microtime(true) . rand()), 0, 6);
    $id_base = $id_base_raw . '-' . $uniq;
} else {
    $id_base = $id_base_raw . '-' . wp_unique_id();
}

// Guard: nothing to render
if (empty($tabs)) return;

// Normalize tab array (basic safety)
$tabs = array_values(array_filter($tabs, function ($t) {
    return is_array($t) && !empty($t['slug']) && !empty($t['label']) && !empty($t['panel']);
}));
if (!$tabs) return;

// Validate active tab
$slugs = array_column($tabs, 'slug');
if (!in_array($active_slug, $slugs, true)) {
    $active_slug = $slugs[0];
}

// Data attributes for optional hash cleanup (e.g., Past Presidents decades)
$cleanup_keep  = $hash_cleanup['keep_slug'] ?? '';
$cleanup_regex = $hash_cleanup['regex']     ?? '';
?>

<div
    class="tab-grid-layout"
    data-id-base="<?php echo esc_attr($id_base); ?>"
    <?php if ($cleanup_keep):  ?>data-keep-slug="<?php echo esc_attr($cleanup_keep); ?>" <?php endif; ?>
    <?php if ($cleanup_regex): ?>data-hash-regex="<?php echo esc_attr($cleanup_regex); ?>" <?php endif; ?>>
    <aside class="tab-grid-sidebar sidebar sidebar-left">
        <?php if (!empty($sidebar_heading)): ?>
            <h1 class="sidebar-header"><?php echo esc_html($sidebar_heading); ?></h1>
        <?php endif; ?>

        <nav class="tab-grid-tabs" role="tablist" aria-label="<?php echo esc_attr($aria_label); ?>">
            <?php foreach ($tabs as $t):
                $slug      = (string) $t['slug'];
                $label     = (string) $t['label'];
                $is_active = ($slug === $active_slug);
                $tab_id    = $id_base . '-tab-'   . $slug;
                $panel_id  = $id_base . '-panel-' . $slug;
            ?>
                <button
                    type="button"
                    id="<?php echo esc_attr($tab_id); ?>"
                    class="tab-grid-tab<?php echo $is_active ? ' is-active' : ''; ?>"
                    role="tab"
                    aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                    aria-controls="<?php echo esc_attr($panel_id); ?>"
                    data-target="<?php echo esc_attr($slug); ?>">
                    <?php echo esc_html($label); ?>
                </button>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="tab-grid-panels main-content-area">
        <?php foreach ($tabs as $t):
            $slug      = (string) $t['slug'];
            $is_active = ($slug === $active_slug);
            $tab_id    = $id_base . '-tab-'   . $slug;
            $panel_id  = $id_base . '-panel-' . $slug;
            $panel     = $t['panel'] ?? [];
            $ptype     = $panel['type'] ?? 'cards';
        ?>
            <section
                id="<?php echo esc_attr($panel_id); ?>"
                class="tab-grid-panel<?php echo $is_active ? ' is-active' : ''; ?>"
                role="tabpanel"
                aria-labelledby="<?php echo esc_attr($tab_id); ?>"
                <?php echo $is_active ? '' : 'hidden'; ?>>
                <?php
                if ($ptype === 'template_part') {
                    $template = $panel['template'] ?? '';
                    $pargs    = $panel['args']     ?? [];
                    if ($template) {
                        get_template_part($template, null, $pargs);
                    }
                } else {
                    // Default: cards-grid with AUTO empty-state detection
                    $prefix          = $panel['prefix']         ?? '';
                    $empty_message   = $panel['empty_message']  ?? 'No items found';
                    $detect_selector = $panel['detect_selector'] ?? '.item-block'; // class used by your cards

                    // Render cards-grid into a buffer to inspect output
                    ob_start();
                    get_template_part('template-parts/grids/cards-grid', null, [
                        'prefix'       => $prefix,
                        'order_tokens' => $order_tokens,
                    ]);
                    $html = trim(ob_get_clean());

                    // Heuristic: consider "has items" if the detect selector appears
                    $has_items = false;
                    if ($detect_selector) {
                        if (strpos($detect_selector, '.') === 0) {
                            // Class selector -> look for class="... item-block ..."
                            $class = preg_quote(substr($detect_selector, 1), '/');
                            $has_items = (bool) preg_match('/class=(["\']).*?\b' . $class . '\b.*?\1/si', $html);
                        } else {
                            // Fallback: simple substring
                            $has_items = (stripos($html, $detect_selector) !== false);
                        }
                    } else {
                        // If no selector provided, treat any non-empty HTML as "has items"
                        $has_items = ($html !== '');
                    }

                    if ($has_items) {
                        echo $html; // normal path
                    } else {
                        // Accessible empty state
                ?>
                        <div class="tab-grid-empty" role="status" aria-live="polite">
                            <p class="tab-grid-empty_title"><?php echo esc_html($empty_message); ?></p>
                        </div>
                <?php
                    }
                }
                ?>
            </section>
        <?php endforeach; ?>
    </div>
</div>

<script>
    (function() {
        // Initialize all tab-grid-layout blocks on the page (safe for multiple instances)
        document.querySelectorAll('.tab-grid-layout').forEach(function(container) {
            const idBase = container.getAttribute('data-id-base') || '';
            const keepSlug = container.getAttribute('data-keep-slug') || '';
            const hashRegex = container.getAttribute('data-hash-regex');
            const rx = hashRegex ? new RegExp(hashRegex) : null;

            const tabs = Array.from(container.querySelectorAll('.tab-grid-tab'));
            const panels = Array.from(container.querySelectorAll('.tab-grid-panel'));
            const valid = new Set(tabs.map(t => t.getAttribute('data-target')));

            function clearHashIfNeeded(target, {
                push = false
            } = {}) {
                if (!rx) return;
                if (target === keepSlug) return;
                const url = new URL(window.location.href);
                if (url.hash && rx.test(url.hash)) {
                    url.hash = '';
                    if (push) history.pushState({
                        tab: target,
                        idBase
                    }, '', url.toString());
                    else history.replaceState({
                        tab: target,
                        idBase
                    }, '', url.toString());
                }
            }

            function activate(target, {
                setFocus = false
            } = {}) {
                if (!valid.has(target)) return;

                tabs.forEach(tab => {
                    const isActive = tab.getAttribute('data-target') === target;
                    tab.classList.toggle('is-active', isActive);
                    tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    if (isActive && setFocus) tab.focus();
                });

                panels.forEach(panel => {
                    const isActive = panel.id.endsWith('panel-' + target);
                    panel.classList.toggle('is-active', isActive);
                    if (isActive) panel.removeAttribute('hidden');
                    else panel.setAttribute('hidden', '');
                });
            }

            function getTabFromURL() {
                const url = new URL(window.location.href);
                const qp = url.searchParams.get('tab');
                if (qp && valid.has(qp)) return qp;

                const hash = url.hash.replace(/^#/, '');
                if (hash.startsWith('tab=')) {
                    const h = hash.slice(4);
                    if (valid.has(h)) return h;
                }
                if (valid.has(hash)) return hash;
                return null;
            }

            function setTabInURL(target, {
                push = true
            } = {}) {
                const url = new URL(window.location.href);
                url.searchParams.set('tab', target);
                if (push) history.pushState({
                    tab: target,
                    idBase
                }, '', url.toString());
                else history.replaceState({
                    tab: target,
                    idBase
                }, '', url.toString());
            }

            // Initial activation (read from URL if present)
            const initial =
                getTabFromURL() ||
                (tabs.find(t => t.classList.contains('is-active'))?.getAttribute('data-target')) ||
                tabs[0]?.getAttribute('data-target');

            if (initial) {
                activate(initial);
                setTabInURL(initial, {
                    push: false
                });
                clearHashIfNeeded(initial, {
                    push: false
                });
            }

            // Click + keyboard
            tabs.forEach(tab => {
                const go = () => {
                    const target = tab.getAttribute('data-target');
                    if (!valid.has(target)) return;
                    activate(target);
                    setTabInURL(target, {
                        push: true
                    });
                    clearHashIfNeeded(target, {
                        push: true
                    });
                };

                tab.addEventListener('click', go);

                tab.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                        e.preventDefault();
                        const i = tabs.indexOf(tab);
                        const next = e.key === 'ArrowRight' ? (i + 1) % tabs.length : (i - 1 + tabs.length) % tabs.length;
                        tabs[next].focus();
                    }
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        go();
                    }
                });
            });

            // Back/Forward support (per-instance safe)
            window.addEventListener('popstate', () => {
                const t = getTabFromURL();
                if (t) {
                    activate(t, {
                        setFocus: false
                    });
                    clearHashIfNeeded(t, {
                        push: false
                    });
                }
            });
        });
    })();
</script>