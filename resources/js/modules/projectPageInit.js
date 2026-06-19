/**
 * projectPageInit.js
 *
 * Orchestrates initialization of all interactive JS modules on the
 * admin project show page: image preview, image viewer, and read more.
 *
 * Reads dynamic post IDs from a data attribute to initialize per-post
 * image preview instances for edit modals.
 *
 * Expected markup:
 *   <div id="project-page-init"
 *        data-post-ids='[1,2,3]'>
 *   </div>
 *
 * Usage:
 *   import { initProjectPage } from './modules/projectPageInit.js';
 *   initProjectPage();
 */

export function initProjectPage() {
    document.addEventListener('DOMContentLoaded', function () {
        const initEl = document.getElementById('project-page-init');
        if (!initEl) return;

        // Initialize image preview for the "new post" form
        if (typeof window.initImagePreview === 'function') {
            window.initImagePreview('attachments', 'file-preview-container', 'folder-picker');
        }

        // Initialize fullscreen image viewer
        if (typeof window.initImageViewer === 'function') {
            window.initImageViewer('modal-image-viewer', 'viewer-img', 'viewer-filename', 'btn-viewer-download');
        }

        // Initialize folder contents viewer
        if (typeof window.initFolderViewer === 'function') {
            window.initFolderViewer('modal-folder-viewer', 'modal-folder-viewer-label', 'folder-viewer-body');
        }

        // Initialize read more/less toggle
        if (typeof window.initReadMore === 'function') {
            window.initReadMore();
        }

        // Initialize async folder uploader for the "new post" form
        if (typeof window.initFolderUploader === 'function') {
            const ajaxStoreUrl      = initEl.dataset.ajaxStoreUrl || '';
            const uploadUrlTemplate = initEl.dataset.uploadUrlTemplate || '';
            if (ajaxStoreUrl && uploadUrlTemplate) {
                window.initFolderUploader('form-new-post', ajaxStoreUrl, uploadUrlTemplate);
            }
        }

        // Initialize image previews for each post edit modal
        let postIds = [];
        try {
            postIds = JSON.parse(initEl.dataset.postIds || '[]');
        } catch (e) {
            postIds = [];
        }

        postIds.forEach(function (postId) {
            if (typeof window.initImagePreview === 'function') {
                window.initImagePreview('attachments-' + postId, 'file-preview-container-' + postId, 'folder-picker-' + postId);
            }
            if (typeof window.initFolderUploader === 'function') {
                const ajaxUpdateUrlTemplate = initEl.dataset.ajaxUpdateUrlTemplate || '';
                const uploadUrlTemplate     = initEl.dataset.uploadUrlTemplate || '';
                const ajaxUpdateUrl         = ajaxUpdateUrlTemplate.replace('__POST_ID__', postId);
                if (ajaxUpdateUrl && uploadUrlTemplate) {
                    window.initFolderUploader('form-edit-post-' + postId, ajaxUpdateUrl, uploadUrlTemplate, true);
                }
            }
        });
    });
}
