<div class="grid-container">
    <?php 
    $prefix = $args['prefix'] ?? ''; 
    $field_base = $prefix ? "{$prefix}_item_" : "item_";
    $post_id = get_queried_object_id();

    // Get order
    $tokens = isset($args['order_tokens']) && is_array($args['order_tokens']) ? $args['order_tokens'] : [];

    // Get default image from an ACF option field
    $default_img = get_field('default_item_image', 'option');

    // Collect items first
    $items = [];
    for ($row = 1; $row <= 36; $row++) {
        $item = get_field($field_base . $row, $post_id); // ACF Group

        if (!$item) continue;

        $item_image = $item['image'] ?? '';
        $item_header = $item['header'] ?? ''; // likely the person’s name
        $item_text   = $item['text']   ?? ''; // this is the ROLE per your note
        $item_email  = $item['email']  ?? '';
        $item_link   = $item['link']   ?? '';

        // show only meaningful rows
        if (empty($item_header) && empty($item_text) && empty($item_email)) continue;

        if (empty($item_image) && !empty($default_img)) $item_image = $default_img;

        $items[] = [
            'image'  => $item_image,
            'header' => $item_header,
            'text'   => $item_text,   // ROLE
            'email'  => $item_email,
            'link'   => $item_link,
            '_row'   => $row,         // original position (stable fallback)
        ];
    }




    // Sort by tokens against ROLE ($item['text'])
    if (!empty($items) && !empty($tokens)) {

        // normalize onces
        $normTokens = array_values(array_filter(array_map(function($t){
            $t = trim($t);
            $t = preg_replace('/^\(contains\)\s*/i', '', $t);
            return mb_strtolower($t);
        }, $tokens)));

        $priorityIndex = function(string $haystack) use ($normTokens) : int {
            $haystack = mb_strtolower($haystack);
            $n = count($normTokens);
            foreach ($normTokens as $i => $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return $i; // earlier token = higher priority
            }
            }
            return $n + 1; // unmatched -> end
        };

        usort($items, function($a, $b) use ($priorityIndex){
            $pa = $priorityIndex((string)$a['text']); // role
            $pb = $priorityIndex((string)$b['text']);

            if ($pa === $pb) {
            // tiebreakers: name (header), then original order
            $nameCmp = strcasecmp((string)$a['header'], (string)$b['header']);
            if ($nameCmp !== 0) return $nameCmp;
            return ($a['_row'] <=> $b['_row']);
            }
            return $pa <=> $pb;
        });
    }


    // Display
    foreach ($items as $it):
        $has_link = !empty($it['link']);
        $tag      = $has_link ? 'a' : 'div';
        $attrs    = $has_link ? ' href="'.esc_url($it['link']).'" target="_blank" rel="noopener"' : '';
        $border   = $has_link ? 'has-link' : 'no-link';
        ?>
        <<?= $tag; ?> class="item-block-link-wrapper <?= $border; ?>"<?= $attrs; ?>>
            <div class="item-block">
            <?php if (!empty($it['image'])): ?>
                <div class="item-media">
                <img src="<?= esc_url($it['image']); ?>"
                    alt="<?= esc_attr($it['header']); ?>"
                    loading="lazy" decoding="async">
                <?php if ($has_link): ?>
                    <div class="item-block-icon">
                    <img src="<?= esc_url(get_stylesheet_directory_uri() . '/assets/icons/links/globe.svg'); ?>" alt="Globe icon">
                    </div>
                <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($it['header'])): ?>
                <h1><?= esc_html($it['header']); ?></h1>
            <?php endif; ?>

            <?php if (!empty($it['text'])): // ROLE ?>
                <p><?= esc_html($it['text']); ?></p>
            <?php endif; ?>

            <?php if (!empty($it['email'])): ?>
                <div class="item-email" data-email="<?= esc_attr($it['email']); ?>" title="Click to copy email">
                <img src="<?= esc_url(get_stylesheet_directory_uri() . '/assets/icons/email.svg'); ?>" alt="Email icon">
                </div>
            <?php endif; ?>
            </div>
        </<?= $tag; ?>>
    <?php endforeach; ?>
</div>





<script>
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.item-email');
  if (!btn) return;

  // Prevent parent <a> navigation if the email icon lives inside a link card
  e.preventDefault();
  e.stopPropagation();

  const email = btn.getAttribute('data-email');
  if (!email) return;

  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(email);
    } else {
      const ta = document.createElement('textarea');
      ta.value = email;
      ta.style.position = 'fixed';
      ta.style.left = '-9999px';
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
    }
    showCopiedTooltip(btn);
  } catch (err) {
    console.error('Copy failed:', err);
  }
});

function showCopiedTooltip(target) {
  // If a tooltip exists, reuse it
  let tip = target.querySelector('.tooltip-copied');
  if (!tip) {
    tip = document.createElement('span');
    tip.className = 'tooltip-copied';
    tip.setAttribute('role', 'status');
    tip.setAttribute('aria-live', 'polite');
    tip.textContent = 'Copied to clipboard';
    target.appendChild(tip);
    // ensure DOM insertion before adding class to trigger transition
    requestAnimationFrame(() => tip.classList.add('visible'));
  } else {
    // reset any hide timers and show again
    clearTimeout(tip._hideTimer);
    tip.classList.add('visible');
  }

  // Show for 1s, then animate out and remove from DOM after transition
  tip._hideTimer = setTimeout(() => {
    tip.classList.remove('visible');
    const onEnd = (ev) => {
      if (ev.propertyName === 'opacity') {
        tip.removeEventListener('transitionend', onEnd);
        // Optionally remove from DOM to keep it clean:
        // tip.remove();
      }
    };
    tip.addEventListener('transitionend', onEnd);
  }, 2000);
}
</script>



























<script>
(function () {
    const SAMPLE_W = 48, SAMPLE_H = 48; // tweak for speed/accuracy
    const WORKER_TIMEOUT_MS = 1500;

    const srgbToLinear = v => {
        v /= 255;
        return v <= 0.04045 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    };
    const linearToSrgb = v => {
        const x = v <= 0.0031308 ? v * 12.92 : 1.055 * Math.pow(v, 1/2.4) - 0.055;
        return Math.max(0, Math.min(255, Math.round(x * 255)));
    };

    // Fallback to main-thread edge average
    function computeEdgeAverageMainThread(img) {
        try {
            const canvas = document.createElement('canvas');
            canvas.width = SAMPLE_W; canvas.height = SAMPLE_H;
            const ctx = canvas.getContext('2d', { willReadFrequently: true });

            const { naturalWidth: w, naturalHeight: h } = img;
            if (!w || !h) return null;

            const scale = Math.min(SAMPLE_W / w, SAMPLE_H / h);
            const dw = Math.max(1, Math.floor(w * scale));
            const dh = Math.max(1, Math.floor(h * scale));
            const dx = Math.floor((SAMPLE_W - dw) / 2);
            const dy = Math.floor((SAMPLE_H - dh) / 2);

            ctx.clearRect(0, 0, SAMPLE_W, SAMPLE_H);
            ctx.drawImage(img, dx, dy, dw, dh);

            const { data, width, height } = ctx.getImageData(0, 0, SAMPLE_W, SAMPLE_H);
            let lr = 0, lg = 0, lb = 0, count = 0;

            const isEdge = (x, y) => (x === 0 || y === 0 || x === width - 1 || y === height - 1);

            for (let y = 0, i = 0; y < height; y++) {
                for (let x = 0; x < width; x++, i += 4) {
                if (!isEdge(x, y)) continue;
                if (data[i + 3] === 0) continue; // skip transparent
                lr += srgbToLinear(data[i]);
                lg += srgbToLinear(data[i + 1]);
                lb += srgbToLinear(data[i + 2]);
                count++;
                }
            }

            if (!count) return null;

            const R = linearToSrgb(lr / count);
            const G = linearToSrgb(lg / count);
            const B = linearToSrgb(lb / count);
            return `rgb(${R}, ${G}, ${B})`;
        } catch {
            return null;
        }
    }

    // Worker code
    const workerSrc = `
        const srgbToLinear = v => { v/=255; return v<=0.04045 ? v/12.92 : Math.pow((v+0.055)/1.055, 2.4); };
        const linearToSrgb = v => { const x=v<=0.0031308?v*12.92:1.055*Math.pow(v,1/2.4)-0.055; return Math.max(0,Math.min(255,Math.round(x*255))); };

        self.onmessage = async (e) => {
        const { id, bitmap, W, H } = e.data;
        try {
            if (typeof OffscreenCanvas === 'undefined') {
                self.postMessage({ id, error: 'no_offscreen' });
                return;
            }
            const canvas = new OffscreenCanvas(W, H);
            const ctx = canvas.getContext('2d', { willReadFrequently: true });

            const iw = bitmap.width, ih = bitmap.height;
            const scale = Math.min(W / iw, H / ih);
            const dw = Math.max(1, Math.floor(iw * scale));
            const dh = Math.max(1, Math.floor(ih * scale));
            const dx = Math.floor((W - dw) / 2);
            const dy = Math.floor((H - dh) / 2);

            ctx.clearRect(0, 0, W, H);
            ctx.drawImage(bitmap, dx, dy, dw, dh);
            if (bitmap.close) bitmap.close();

            const { data, width, height } = ctx.getImageData(0, 0, W, H);

            let lr=0, lg=0, lb=0, count=0;
            const isEdge = (x, y) => (x === 0 || y === 0 || x === width - 1 || y === height - 1);

            for (let y = 0, i = 0; y < height; y++) {
                for (let x = 0; x < width; x++, i += 4) {
                    if (!isEdge(x, y)) continue;
                    if (data[i + 3] === 0) continue;
                    lr += srgbToLinear(data[i]);
                    lg += srgbToLinear(data[i + 1]);
                    lb += srgbToLinear(data[i + 2]);
                    count++;
                }
            }

            if (!count) {
                self.postMessage({ id, error: 'no_pixels' });
                return;
            }

            const R = linearToSrgb(lr / count);
            const G = linearToSrgb(lg / count);
            const B = linearToSrgb(lb / count);

            self.postMessage({ id, r: R, g: G, b: B });
        } catch {
            self.postMessage({ id, error: 'worker_fail' });
        }
        };
    `;

    let worker = null;
    function getWorker() {
        if (worker) return worker;
        if (!('Worker' in window)) return null;
        const blob = new Blob([workerSrc], { type: 'application/javascript' });
        const url = URL.createObjectURL(blob);
        worker = new Worker(url);
        URL.revokeObjectURL(url);
        window.addEventListener('pagehide', () => { try { worker.terminate(); } catch {} }, { once: true });
        return worker;
    }

    let msgId = 0;
    const pending = new Map();

    function processInWorker(img) {
        return new Promise(async (resolve, reject) => {
            const w = getWorker();
            if (!w || !('createImageBitmap' in window)) {
                reject(new Error('no_worker_or_bitmap'));
                return;
            }
            let bmp;
            try {
                bmp = await createImageBitmap(img);
            } catch {
                reject(new Error('bitmap_fail'));
                return;
            }
            const id = ++msgId;
            const timer = setTimeout(() => {
                if (pending.has(id)) {
                    pending.delete(id);
                    reject(new Error('timeout'));
                }
            }, WORKER_TIMEOUT_MS);

            pending.set(id, { resolve, reject, timer });
            w.postMessage({ id, bitmap: bmp, W: SAMPLE_W, H: SAMPLE_H }, [bmp]);

            if (!w._bound) {
                w.onmessage = (ev) => {
                    const { id, r, g, b, error } = ev.data || {};
                    const entry = pending.get(id);
                    if (!entry) return;
                    clearTimeout(entry.timer);
                    pending.delete(id);
                    if (error) entry.reject(new Error(error));
                    else entry.resolve(`rgb(${r}, ${g}, ${b})`);
                };
                w._bound = true;
            }
        });
    }

    function setColor(img, color) {
        const container = img.closest('.item-media') || img.parentElement;
        if (!container) return;
        container.style.setProperty('--avg-bg', color);
        container.style.backgroundColor = color;
        img.dataset.edgeBgComputed = '1';
    }

    function attach(img) {
        if (img.dataset.edgeBgComputed) return;
        const run = async () => {
        try {
            const color = await processInWorker(img);
            setColor(img, color);
        } catch {
            const color = computeEdgeAverageMainThread(img);
            if (color) setColor(img, color);
        }
        };
        if (img.complete && img.naturalWidth) run();
        else img.addEventListener('load', run, { once: true });
    }

    // Process existing images on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.item-media > img').forEach(attach);
    });

    // Public API
    window.edgeAverageBg = { attach };
})();
</script>
