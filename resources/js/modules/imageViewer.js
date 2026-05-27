/**
 * imageViewer.js
 *
 * Fullscreen image viewer using a Bootstrap modal.
 * Delegates click events from `.ebt-viewer-trigger` images in the document.
 * Supports gallery navigation (all images in the same grid).
 *
 * Usage:
 *   import { initImageViewer } from '/js/modules/imageViewer.js';
 *   initImageViewer('modal-image-viewer', 'viewer-img', 'viewer-filename', 'btn-viewer-download');
 *
 * @param {string} modalId         - ID of the Bootstrap modal element.
 * @param {string} imgElId         - ID of the <img> inside the modal.
 * @param {string} filenameElId    - ID of the element showing the filename.
 * @param {string} downloadBtnId   - ID of the <a> download button.
 */
export function initImageViewer(modalId, imgElId, filenameElId, downloadBtnId) {
    const modalEl      = document.getElementById(modalId);
    const imgEl        = document.getElementById(imgElId);
    const filenameEl   = document.getElementById(filenameElId);
    const downloadBtn  = document.getElementById(downloadBtnId);

    if (!modalEl || !imgEl) return;

    const bsModal = new bootstrap.Modal(modalEl);

    // Use event delegation on the document body for dynamically added images
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('.ebt-viewer-trigger');
        if (!trigger) return;

        const src      = trigger.dataset.src      ?? trigger.src;
        const filename = trigger.dataset.filename ?? '';

        _openViewer(src, filename, bsModal, imgEl, filenameEl, downloadBtn);
    });
}

/**
 * Populate the modal and open it.
 * @private
 */
function _openViewer(src, filename, bsModal, imgEl, filenameEl, downloadBtn) {
    // Reset while loading
    imgEl.src = '';
    imgEl.alt = filename;

    if (filenameEl) filenameEl.textContent = filename;

    if (downloadBtn) {
        downloadBtn.href     = src;
        downloadBtn.download = filename;
    }

    // Load the image, then show modal
    const loader = new Image();
    loader.onload = () => {
        imgEl.src = src;
    };
    loader.onerror = () => {
        imgEl.alt = 'No se pudo cargar la imagen.';
    };
    loader.src = src;

    bsModal.show();
}
