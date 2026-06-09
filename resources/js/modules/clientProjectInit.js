/**
 * clientProjectInit.js
 *
 * Initializes interactive JS modules on the client project show page:
 * read more/less toggle and fullscreen image viewer.
 *
 * Expected markup:
 *   <div id="client-project-init"></div>
 *
 * Usage:
 *   import { initClientProjectPage } from './modules/clientProjectInit.js';
 *   initClientProjectPage();
 */

export function initClientProjectPage() {
    document.addEventListener('DOMContentLoaded', function () {
        const initEl = document.getElementById('client-project-init');
        if (!initEl) return;

        if (typeof window.initReadMore === 'function') {
            window.initReadMore();
        }

        if (typeof window.initImageViewer === 'function') {
            window.initImageViewer('modal-image-viewer', 'viewer-img', 'viewer-filename', 'btn-viewer-download');
        }
    });
}
