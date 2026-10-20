{{-- Contextual User Manual — book icon beside breadcrumb --}}
@php
    $ug = $userGuide ?? \App\Support\UserGuide::forRoute();
@endphp

<style>
  /* Book icon — does not alter breadcrumb flex of the page */
  .ug-book-li {
    list-style: none !important;
    margin-left: 8px !important;
    padding-left: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
  }
  .ug-book-li::before { content: none !important; display: none !important; }
  .ug-book-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    background: var(--primary-color, #940000);
    color: #fff !important;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,.18);
    transition: filter .15s ease;
  }
  .ug-book-btn:hover,
  .ug-book-btn:focus {
    filter: brightness(1.1);
    color: #fff !important;
    text-decoration: none !important;
    outline: none;
  }
  .ug-book-btn i { font-size: 15px; }

  /* Modal */
  .ug-overlay {
    position: fixed;
    inset: 0;
    z-index: 20000;
    background: rgba(20, 20, 20, .55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
    overflow: auto;
  }
  .ug-overlay.is-open { display: flex; }

  .ug-shell {
    --ug-scale: 1;
    width: min(980px, 100%);
    max-height: calc(100vh - 32px);
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 16px 48px rgba(0,0,0,.28);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(var(--ug-scale));
    transform-origin: center center;
    transition: transform .15s ease;
    flex-shrink: 0;
  }

  .ug-banner {
    background: var(--primary-color, #940000);
    color: #fff;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }
  .ug-banner-crumb {
    font-size: 13px;
    opacity: .9;
    margin-bottom: 2px;
  }
  .ug-banner h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #fff;
    line-height: 1.25;
  }
  .ug-banner-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
  }
  .ug-zoom {
    display: inline-flex;
    align-items: center;
    gap: 2px;
    background: rgba(0,0,0,.2);
    border-radius: 8px;
    padding: 3px;
  }
  .ug-zoom button {
    border: 0;
    background: transparent;
    color: #fff;
    width: 36px;
    height: 34px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 700;
    font-size: 16px;
    line-height: 1;
  }
  .ug-zoom button:hover { background: rgba(255,255,255,.18); }
  .ug-zoom button:disabled { opacity: .35; cursor: default; }
  .ug-zoom em {
    font-style: normal;
    font-size: 12px;
    font-weight: 700;
    min-width: 48px;
    text-align: center;
  }
  .ug-close {
    border: 0;
    background: rgba(0,0,0,.25);
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
  }
  .ug-close:hover { background: rgba(0,0,0,.4); }

  .ug-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 0;
    flex: 1;
    overflow: hidden;
  }
  @media (max-width: 768px) {
    .ug-body { grid-template-columns: 1fr; overflow: auto; }
  }

  .ug-visual {
    background: #f0f0f0;
    border-right: 1px solid #e5e5e5;
    padding: 14px;
    overflow: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .ug-shot-wrap {
    position: relative;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    min-height: 280px;
    flex: 1;
  }
  .ug-shot {
    display: block;
    width: 100%;
    height: auto;
    max-height: none;
    vertical-align: top;
  }
  .ug-shot-loading,
  .ug-shot-fallback {
    padding: 28px 18px;
    text-align: center;
    color: #666;
    font-size: 14px;
  }
  .ug-shot-loading i { margin-right: 6px; color: var(--primary-color, #940000); }
  .ug-marker {
    position: absolute;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--primary-color, #940000);
    color: #fff;
    font-weight: 800;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color, #940000) 28%, transparent);
    z-index: 2;
    pointer-events: none;
  }
  .ug-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: var(--primary-color, #940000);
    color: #fff;
    font-weight: 800;
    font-size: 13px;
    margin-right: 8px;
    flex-shrink: 0;
  }
  .ug-visual-hint {
    font-size: 12px;
    color: #777;
    line-height: 1.4;
    margin: 0;
  }

  .ug-desc {
    padding: 20px 22px 22px;
    overflow: auto;
    background: #fff;
  }
  .ug-pill {
    display: inline-block;
    background: var(--primary-color, #940000);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 999px;
    margin-bottom: 12px;
  }
  .ug-desc h3 {
    margin: 0 0 6px;
    font-size: 20px;
    font-weight: 700;
    color: #222;
  }
  .ug-desc h3::before {
    content: '◆ ';
    color: var(--primary-color, #940000);
    font-size: .85em;
  }
  .ug-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: #888;
    margin: 12px 0 4px;
  }
  .ug-overview {
    font-size: 15px;
    line-height: 1.55;
    color: #333;
    margin: 0 0 14px;
  }
  .ug-steps {
    list-style: none;
    padding: 0;
    margin: 0 0 16px;
  }
  .ug-steps li {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-bottom: 12px;
    font-size: 15px;
    line-height: 1.5;
    color: #333;
  }
  .ug-tip {
    background: color-mix(in srgb, var(--primary-color, #940000) 10%, #fff);
    border-left: 4px solid var(--primary-color, #940000);
    border-radius: 0 8px 8px 0;
    padding: 12px 14px;
  }
  .ug-tip strong {
    display: block;
    color: var(--primary-color, #940000);
    font-size: 13px;
    margin-bottom: 4px;
    letter-spacing: .04em;
  }
  .ug-tip p {
    margin: 0;
    font-size: 14px;
    line-height: 1.45;
    color: #333;
  }

  .ug-footer {
    border-top: 1px solid #eee;
    padding: 10px 16px;
    font-size: 12px;
    color: #777;
    display: flex;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    background: #fafafa;
  }
  .ug-footer b { color: var(--primary-color, #940000); }

  html[data-theme="dark"] .ug-shell { background: #161b22; }
  html[data-theme="dark"] .ug-visual { background: #0d1117; border-right-color: #30363d; }
  html[data-theme="dark"] .ug-card,
  html[data-theme="dark"] .ug-desc { background: #161b22; color: #e6edf3; }
  html[data-theme="dark"] .ug-desc h3,
  html[data-theme="dark"] .ug-overview,
  html[data-theme="dark"] .ug-steps li,
  html[data-theme="dark"] .ug-tip p { color: #e6edf3; }
  html[data-theme="dark"] .ug-card-line { background: #30363d; }
  html[data-theme="dark"] .ug-card-box { background: #0d1117; border-color: #30363d; }
  html[data-theme="dark"] .ug-footer { background: #0d1117; border-top-color: #30363d; color: #8b949e; }
</style>

<div class="ug-overlay" id="userGuideOverlay" aria-hidden="true" role="dialog" aria-modal="true" aria-label="User guide">
  <div class="ug-shell" id="ugShell">
    <div class="ug-banner">
      <div style="min-width:0;">
        <div class="ug-banner-crumb" id="ugBreadcrumb">{{ implode(' › ', $ug['breadcrumb'] ?? []) }}</div>
        <h2 id="ugTitle">{{ $ug['title'] ?? 'User Guide' }}</h2>
      </div>
      <div class="ug-banner-actions">
        <div class="ug-zoom" title="Zoom whole card">
          <button type="button" id="ugZoomOut" aria-label="Zoom out"><i class="fa fa-search-minus"></i></button>
          <em id="ugZoomLabel">100%</em>
          <button type="button" id="ugZoomIn" aria-label="Zoom in"><i class="fa fa-search-plus"></i></button>
          <button type="button" id="ugZoomReset" aria-label="Reset zoom" title="Reset">⟲</button>
        </div>
        <button type="button" class="ug-close" id="ugCloseBtn" title="Close" aria-label="Close">&times;</button>
      </div>
    </div>

    <div class="ug-body">
      <div class="ug-visual">
        <div class="ug-shot-wrap" id="ugShotWrap">
          <div class="ug-shot-loading" id="ugShotLoading">
            <i class="fa fa-spinner fa-spin"></i> Capturing this page…
          </div>
          <img class="ug-shot" id="ugShot" alt="Page screenshot" style="display:none;">
          <div class="ug-shot-fallback" id="ugShotFallback" style="display:none;">
            Screenshot could not be captured. Use the steps on the right.
          </div>
        </div>
        <p class="ug-visual-hint">This is a live screenshot of the page you are on. Numbered badges match the steps on the right.</p>
      </div>

      <div class="ug-desc">
        <span class="ug-pill">Description</span>
        <h3 id="ugDescTitle">{{ $ug['title'] ?? 'Guide' }}</h3>
        <div class="ug-label">Overview</div>
        <p class="ug-overview" id="ugOverview">{{ $ug['overview'] ?? '' }}</p>
        <div class="ug-label">Steps</div>
        <ul class="ug-steps" id="ugSteps">
          @foreach(($ug['steps'] ?? []) as $step)
            <li>
              <span class="ug-badge">{{ $step['n'] }}</span>
              <span>{{ $step['text'] }}</span>
            </li>
          @endforeach
        </ul>
        <div class="ug-tip">
          <strong>TIP</strong>
          <p id="ugTip">{{ $ug['tip'] ?? '' }}</p>
        </div>
      </div>
    </div>

    <div class="ug-footer">
      <span><b>UMOJA LUTHERAN</b> · User Manual</span>
      <span>Zoom whole card · Esc to close</span>
    </div>
  </div>
</div>

<script>
(function () {
  var guide = @json($ug);
  var overlay = document.getElementById('userGuideOverlay');
  var shell = document.getElementById('ugShell');
  if (!overlay || !shell) return;

  var MIN = 0.75, MAX = 1.5, STEP = 0.1, DEFAULT = 1;
  var zoom = parseFloat(localStorage.getItem('ug_card_zoom') || DEFAULT);
  if (isNaN(zoom)) zoom = DEFAULT;

  function clamp(n) {
    return Math.min(MAX, Math.max(MIN, Math.round(n * 100) / 100));
  }

  function applyZoom() {
    zoom = clamp(zoom);
    shell.style.setProperty('--ug-scale', String(zoom));
    var label = document.getElementById('ugZoomLabel');
    if (label) label.textContent = Math.round(zoom * 100) + '%';
    localStorage.setItem('ug_card_zoom', String(zoom));
    var outB = document.getElementById('ugZoomOut');
    var inB = document.getElementById('ugZoomIn');
    if (outB) outB.disabled = zoom <= MIN;
    if (inB) inB.disabled = zoom >= MAX;
  }

  function injectBookButtons() {
    document.querySelectorAll('.app-breadcrumb').forEach(function (crumb) {
      if (crumb.querySelector('.ug-book-btn')) return;
      var li = document.createElement('li');
      li.className = 'breadcrumb-item ug-book-li';
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'ug-book-btn';
      btn.title = 'User manual';
      btn.setAttribute('aria-label', 'Open user manual');
      btn.innerHTML = '<i class="fa fa-book" aria-hidden="true"></i>';
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openGuide();
      });
      li.appendChild(btn);
      crumb.appendChild(li);
    });
  }

  function fillGuide(data) {
    guide = data || guide;
    document.getElementById('ugBreadcrumb').textContent = (guide.breadcrumb || []).join(' › ');
    document.getElementById('ugTitle').textContent = guide.title || 'User Guide';
    document.getElementById('ugDescTitle').textContent = guide.title || 'Guide';
    document.getElementById('ugOverview').textContent = guide.overview || '';
    document.getElementById('ugTip').textContent = guide.tip || '';

    var stepsEl = document.getElementById('ugSteps');
    stepsEl.innerHTML = '';
    (guide.steps || []).forEach(function (step, i) {
      var li = document.createElement('li');
      li.innerHTML = '<span class="ug-badge"></span><span></span>';
      li.querySelector('.ug-badge').textContent = step.n || (i + 1);
      li.querySelector('span:last-child').textContent = step.text || '';
      stepsEl.appendChild(li);
    });
    placeMarkers(guide.markers || []);
  }

  function placeMarkers(markers) {
    var wrap = document.getElementById('ugShotWrap');
    if (!wrap) return;
    wrap.querySelectorAll('.ug-marker').forEach(function (el) { el.remove(); });
    var defaults = [
      { n: 1, top: '16%', left: '78%' },
      { n: 2, top: '38%', left: '18%' },
      { n: 3, top: '62%', left: '55%' }
    ];
    var list = (markers && markers.length) ? markers : defaults.slice(0, (guide.steps || []).length || 2);
    list.forEach(function (m, i) {
      var span = document.createElement('span');
      span.className = 'ug-marker';
      span.style.top = m.top || defaults[i % defaults.length].top;
      span.style.left = m.left || defaults[i % defaults.length].left;
      span.textContent = m.n || (i + 1);
      wrap.appendChild(span);
    });
  }

  function loadHtml2Canvas() {
    return new Promise(function (resolve, reject) {
      if (window.html2canvas) return resolve(window.html2canvas);
      var existing = document.querySelector('script[data-ug-h2c]');
      if (existing) {
        existing.addEventListener('load', function () { resolve(window.html2canvas); });
        existing.addEventListener('error', reject);
        return;
      }
      var s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
      s.async = true;
      s.setAttribute('data-ug-h2c', '1');
      s.onload = function () { resolve(window.html2canvas); };
      s.onerror = reject;
      document.head.appendChild(s);
    });
  }

  function showShotState(mode) {
    var loading = document.getElementById('ugShotLoading');
    var shot = document.getElementById('ugShot');
    var fallback = document.getElementById('ugShotFallback');
    if (loading) loading.style.display = mode === 'loading' ? 'block' : 'none';
    if (shot) shot.style.display = mode === 'shot' ? 'block' : 'none';
    if (fallback) fallback.style.display = mode === 'fallback' ? 'block' : 'none';
  }

  function captureScreenshot() {
    showShotState('loading');
    var target = document.querySelector('.app-content');
    if (!target) {
      showShotState('fallback');
      return Promise.resolve();
    }

    // Hide overlay while capturing so it is not in the shot
    var wasOpen = overlay.classList.contains('is-open');
    if (wasOpen) {
      overlay.style.visibility = 'hidden';
    }

    return loadHtml2Canvas()
      .then(function (html2canvas) {
        return html2canvas(target, {
          scale: 0.65,
          useCORS: true,
          allowTaint: true,
          logging: false,
          backgroundColor: '#ffffff',
          scrollX: 0,
          scrollY: -window.scrollY,
          windowWidth: document.documentElement.clientWidth,
          windowHeight: Math.min(target.scrollHeight, 1600),
          ignoreElements: function (el) {
            return el.id === 'userGuideOverlay' || el.classList.contains('ug-overlay');
          }
        });
      })
      .then(function (canvas) {
        var shot = document.getElementById('ugShot');
        if (!shot) return;
        shot.src = canvas.toDataURL('image/jpeg', 0.86);
        showShotState('shot');
        placeMarkers(guide.markers || []);
      })
      .catch(function () {
        showShotState('fallback');
      })
      .finally(function () {
        if (wasOpen) overlay.style.visibility = '';
      });
  }

  function openGuide() {
    fillGuide(guide);
    applyZoom();
    showShotState('loading');
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    // Capture after a tick so layout is stable; hide overlay briefly during capture
    setTimeout(function () {
      captureScreenshot();
    }, 40);
  }

  function closeGuide() {
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden', 'true');
    overlay.style.visibility = '';
    document.body.style.overflow = '';
  }

  document.getElementById('ugCloseBtn').addEventListener('click', closeGuide);
  document.getElementById('ugZoomIn').addEventListener('click', function () {
    zoom += STEP;
    applyZoom();
  });
  document.getElementById('ugZoomOut').addEventListener('click', function () {
    zoom -= STEP;
    applyZoom();
  });
  document.getElementById('ugZoomReset').addEventListener('click', function () {
    zoom = DEFAULT;
    applyZoom();
  });
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) closeGuide();
  });
  document.addEventListener('keydown', function (e) {
    if (!overlay.classList.contains('is-open')) return;
    if (e.key === 'Escape') closeGuide();
    if ((e.ctrlKey || e.metaKey) && (e.key === '=' || e.key === '+')) {
      e.preventDefault();
      zoom += STEP;
      applyZoom();
    }
    if ((e.ctrlKey || e.metaKey) && e.key === '-') {
      e.preventDefault();
      zoom -= STEP;
      applyZoom();
    }
    if ((e.ctrlKey || e.metaKey) && e.key === '0') {
      e.preventDefault();
      zoom = DEFAULT;
      applyZoom();
    }
  });

  applyZoom();
  window.UmojaUserGuide = { open: openGuide, close: closeGuide, set: fillGuide };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', injectBookButtons);
  } else {
    injectBookButtons();
  }
})();
</script>
