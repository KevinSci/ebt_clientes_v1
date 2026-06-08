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
    const prevBtn      = document.getElementById('btn-viewer-prev');
    const nextBtn      = document.getElementById('btn-viewer-next');

    if (!modalEl || !imgEl) return;

    const bsModal = new bootstrap.Modal(modalEl);

    let currentImages = [];
    let currentFilenames = [];
    let currentIndex = 0;

    function showImage(index) {
        if (index < 0 || index >= currentImages.length) return;
        currentIndex = index;

        const src = currentImages[currentIndex];
        const filename = currentFilenames[currentIndex] || '';

        imgEl.src = '';
        imgEl.alt = filename;
        if (filenameEl) filenameEl.textContent = filename;
        if (downloadBtn) {
            downloadBtn.href = src;
            downloadBtn.download = filename;
        }

        // Mostrar u ocultar botones de navegación según la cantidad de imágenes
        if (prevBtn && nextBtn) {
            if (currentImages.length > 1) {
                prevBtn.classList.remove('d-none');
                nextBtn.classList.remove('d-none');
            } else {
                prevBtn.classList.add('d-none');
                nextBtn.classList.add('d-none');
            }
        }

        // Cargar imagen de forma asíncrona
        const loader = new Image();
        loader.onload = () => {
            imgEl.src = src;
        };
        loader.onerror = () => {
            imgEl.alt = 'No se pudo cargar la imagen.';
        };
        loader.src = src;
    }

    // Delegación de eventos en el cuerpo del documento
    document.addEventListener('click', (event) => {
        let trigger = event.target.closest('.ebt-viewer-trigger');
        
        // Si hicieron clic en el overlay "+N", obtener la imagen correspondiente del mismo item
        if (!trigger) {
            const overlay = event.target.closest('.ebt-img-grid__overlay');
            if (overlay) {
                trigger = overlay.parentElement.querySelector('.ebt-viewer-trigger');
            }
        }

        if (!trigger) return;

        // Intentar parsear las imágenes y nombres del dataset
        try {
            currentImages = JSON.parse(trigger.dataset.images || '[]');
            currentFilenames = JSON.parse(trigger.dataset.filenames || '[]');
            currentIndex = parseInt(trigger.dataset.index || '0', 10);
        } catch (e) {
            const src = trigger.dataset.src ?? trigger.src;
            const filename = trigger.dataset.filename ?? '';
            currentImages = [src];
            currentFilenames = [filename];
            currentIndex = 0;
        }

        if (currentImages.length === 0) {
            const src = trigger.dataset.src ?? trigger.src;
            const filename = trigger.dataset.filename ?? '';
            currentImages = [src];
            currentFilenames = [filename];
            currentIndex = 0;
        }

        showImage(currentIndex);
        bsModal.show();
    });

    // Control de navegación Anterior (Prev)
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            let newIndex = currentIndex - 1;
            if (newIndex < 0) {
                newIndex = currentImages.length - 1; // Bucle al final
            }
            showImage(newIndex);
        });
    }

    // Control de navegación Siguiente (Next)
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            let newIndex = currentIndex + 1;
            if (newIndex >= currentImages.length) {
                newIndex = 0; // Bucle al inicio
            }
            showImage(newIndex);
        });
    }

    // Soporte para navegación con teclas de flecha
    document.addEventListener('keydown', (e) => {
        if (!modalEl.classList.contains('show') || currentImages.length <= 1) return;

        if (e.key === 'ArrowLeft') {
            let newIndex = currentIndex - 1;
            if (newIndex < 0) {
                newIndex = currentImages.length - 1;
            }
            showImage(newIndex);
        } else if (e.key === 'ArrowRight') {
            let newIndex = currentIndex + 1;
            if (newIndex >= currentImages.length) {
                newIndex = 0;
            }
            showImage(newIndex);
        }
    });
}
