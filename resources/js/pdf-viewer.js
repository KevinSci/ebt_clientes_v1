// ── PDF.js Setup ─────────────────────────────────────────────────────
pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

// ── State ─────────────────────────────────────────────────────────────
const DPR         = window.devicePixelRatio || 1;
let pdfDoc        = null;
let pageNum       = 1;
let scale         = 1.0;
let renderTask    = null;
let renderPending = false;
let pendingPage   = null;
let pageCache     = {};           // { pageNum: pdfPageObject }

const canvas      = document.getElementById('pdf-canvas');
const ctx         = canvas.getContext('2d');
const container   = document.querySelector('.pdf-viewer-container');
const pageWrapper = document.querySelector('.pdf-page-wrapper');
const loader      = document.getElementById('loader');
const loaderText  = document.getElementById('loader-text');

// Get PDF URL from the global variable defined in the blade template
const url = window.pdfUrl;

// ── Helpers ───────────────────────────────────────────────────────────
function getBaseScale() {
    // On mobile snap PDF width to screen; on desktop use raw scale
    if (window.innerWidth <= 768) {
        return scale; // combined in renderPage below
    }
    return scale;
}

// Pre-fetch and cache the next/prev pages so switching feels instant
function prefetch(nums) {
    nums.forEach(n => {
        if (n < 1 || n > pdfDoc.numPages || pageCache[n]) return;
        pdfDoc.getPage(n).then(p => { pageCache[n] = p; });
    });
}

// ── Rendering ─────────────────────────────────────────────────────────
function renderPage(num, opts = {}) {
    renderPending = true;

    const getPage = pageCache[num]
        ? Promise.resolve(pageCache[num])
        : pdfDoc.getPage(num).then(p => { pageCache[num] = p; return p; });

    getPage.then(page => {
        const baseVp   = page.getViewport({ scale: 1.0 });
        let finalScale = scale;

        if (window.innerWidth <= 768) {
            const fitScale = (window.innerWidth - 32) / baseVp.width;
            finalScale = fitScale * scale;
        }

        const viewport = page.getViewport({ scale: finalScale });

        // ── Scroll anchor: keep viewed region stable ──────────────
        const sw0 = container.scrollWidth;
        const sh0 = container.scrollHeight;
        const rx  = sw0 > 0 ? (container.scrollLeft + container.clientWidth  / 2) / sw0 : 0.5;
        const ry  = sh0 > 0 ? (container.scrollTop  + container.clientHeight / 2) / sh0 : 0.5;

        // Cancel previous render task if still running
        if (renderTask) {
            renderTask.cancel();
            renderTask = null;
        }

        // ── High-DPI canvas ───────────────────────────────────────
        canvas.width        = Math.floor(viewport.width  * DPR);
        canvas.height       = Math.floor(viewport.height * DPR);
        canvas.style.width  = Math.floor(viewport.width)  + 'px';
        canvas.style.height = Math.floor(viewport.height) + 'px';

        // Restore scroll anchor after canvas resize
        requestAnimationFrame(() => {
            const sw1 = container.scrollWidth;
            const sh1 = container.scrollHeight;
            if (sw1 > 0) container.scrollLeft = rx * sw1 - container.clientWidth  / 2;
            if (sh1 > 0) container.scrollTop  = ry * sh1 - container.clientHeight / 2;
        });

        renderTask = page.render({
            canvasContext: ctx,
            transform: DPR !== 1 ? [DPR, 0, 0, DPR, 0, 0] : null,
            viewport,
            intent: 'display',
        });

        renderTask.promise.then(() => {
            renderTask    = null;
            renderPending = false;
            loader.classList.add('hidden');
            document.getElementById('page-num').textContent = num;

            if (pendingPage !== null) {
                const next = pendingPage;
                pendingPage = null;
                renderPage(next);
            } else {
                // Prefetch neighbors after render is done
                prefetch([num - 1, num + 1]);
            }
        }).catch(err => {
            if (err?.name === 'RenderingCancelledException') return;
            console.error('Render error:', err);
        });
    });
}

function queueRenderPage(num) {
    if (renderPending) {
        pendingPage = num;
    } else {
        renderPage(num);
    }
}

// ── Page Navigation ───────────────────────────────────────────────────
function goToPage(n) {
    if (!pdfDoc || n < 1 || n > pdfDoc.numPages) return;
    pageNum = n;
    queueRenderPage(pageNum);
}

function onPrevPage() { goToPage(pageNum - 1); }
function onNextPage() { goToPage(pageNum + 1); }

document.getElementById('prev-page').addEventListener('click', onPrevPage);
document.getElementById('next-page').addEventListener('click', onNextPage);

// ── Zoom Controls ─────────────────────────────────────────────────────
let zoomDebounce = null;

function applyZoom(newScale) {
    scale = Math.min(Math.max(newScale, 0.5), 4.0);
    clearTimeout(zoomDebounce);
    zoomDebounce = setTimeout(() => queueRenderPage(pageNum), 60);
}

document.getElementById('zoom-in') .addEventListener('click', () => applyZoom(scale + 0.25));
document.getElementById('zoom-out').addEventListener('click', () => applyZoom(scale - 0.25));

// ── Touch Gestures ────────────────────────────────────────────────────
let pinchStart     = 0;   // initial distance between fingers
let pinchScale     = 1;   // live CSS transform scale during gesture
let pinchOriginX   = 0;   // focal point X relative to wrapper
let pinchOriginY   = 0;   // focal point Y relative to wrapper

let swipeStartX    = 0;
let swipeStartY    = 0;
let swipeStartTime = 0;
let isPinching     = false;

function midpoint(t) {
    return {
        x: (t[0].clientX + t[1].clientX) / 2,
        y: (t[0].clientY + t[1].clientY) / 2,
    };
}

container.addEventListener('touchstart', (e) => {
    if (e.touches.length === 2) {
        isPinching = true;
        pinchStart = Math.hypot(
            e.touches[0].clientX - e.touches[1].clientX,
            e.touches[0].clientY - e.touches[1].clientY
        );
        pinchScale = 1;

        // Focal point: center of two fingers, relative to wrapper
        const mid   = midpoint(e.touches);
        const rect  = pageWrapper.getBoundingClientRect();
        pinchOriginX = mid.x - rect.left;
        pinchOriginY = mid.y - rect.top;

        swipeStartX = 0;
    } else if (e.touches.length === 1 && !isPinching) {
        swipeStartX    = e.touches[0].clientX;
        swipeStartY    = e.touches[0].clientY;
        swipeStartTime = Date.now();
    }
}, { passive: true });

container.addEventListener('touchmove', (e) => {
    if (e.touches.length === 2 && pinchStart > 0) {
        if (e.cancelable) e.preventDefault();

        const dist = Math.hypot(
            e.touches[0].clientX - e.touches[1].clientX,
            e.touches[0].clientY - e.touches[1].clientY
        );
        pinchScale = dist / pinchStart;

        // Apply CSS transform anchored to focal point
        pageWrapper.style.transformOrigin = `${pinchOriginX}px ${pinchOriginY}px`;
        pageWrapper.style.transform       = `scale(${pinchScale})`;
    }
}, { passive: false });

container.addEventListener('touchend', (e) => {
    if (pinchStart > 0) {
        // Commit zoom: update scale and re-render at crisp resolution
        scale = Math.min(Math.max(scale * pinchScale, 0.5), 4.0);
        pageWrapper.style.transform       = 'none';
        pageWrapper.style.transformOrigin = 'center center';
        pinchStart  = 0;
        pinchScale  = 1;
        isPinching  = false;
        queueRenderPage(pageNum);
        return;
    }

    if (swipeStartX > 0 && e.changedTouches.length === 1) {
        const dx       = e.changedTouches[0].clientX - swipeStartX;
        const dy       = e.changedTouches[0].clientY - swipeStartY;
        const dt       = Date.now() - swipeStartTime;
        const velocity = Math.abs(dx) / dt; // px/ms

        const isZoomed = container.scrollWidth > container.clientWidth + 10;

        // Swipe: > 60px OR fast flick (>0.4 px/ms), more horizontal than vertical
        if (!isZoomed && Math.abs(dx) > Math.abs(dy) * 1.5
            && (Math.abs(dx) > 60 || velocity > 0.4)) {
            dx < 0 ? onNextPage() : onPrevPage();
        }
        swipeStartX = 0;
    }
}, { passive: true });

// ── Keyboard Navigation ───────────────────────────────────────────────
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   onPrevPage();
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') onNextPage();
});

// ── Load PDF ──────────────────────────────────────────────────────────
pdfjsLib.getDocument({ url, cMapPacked: true }).promise.then(doc => {
    pdfDoc = doc;
    document.getElementById('page-count').textContent = doc.numPages;
    renderPage(pageNum);
}).catch(err => {
    console.error('Error loading PDF:', err);
    loaderText.innerHTML =
        '<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>' +
        'No se pudo cargar el archivo PDF.</span>';
    document.querySelector('.spinner').style.display = 'none';
});
