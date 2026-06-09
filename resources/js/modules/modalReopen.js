/**
 * modalReopen.js
 *
 * Automatically reopens a Bootstrap modal when the page loads with
 * validation errors, using data attributes instead of inline PHP in JS.
 *
 * Expected markup (rendered by Blade):
 *   <!-- Simple case: always reopen the same modal -->
 *   <div data-reopen-modal-id="modal-create-client"></div>
 *
 *   <!-- Form-based case: reopen based on which form failed -->
 *   <div data-reopen-form-id="edit_client"
 *        data-modal-map='{"edit_client":"modal-edit-client","create_project":"modal-create-project"}'></div>
 *
 *   <!-- Dynamic post modal case -->
 *   <div data-reopen-form-id="edit_post_5"
 *        data-reopen-post-prefix="edit_post_"
 *        data-reopen-post-modal-prefix="modal-edit-post-"></div>
 *
 * Usage:
 *   import { initModalReopen } from './modules/modalReopen.js';
 *   initModalReopen();
 */

export function initModalReopen() {
    document.addEventListener('DOMContentLoaded', function () {
        // Case 1: Simple modal reopen (single modal ID)
        const simpleReopen = document.querySelector('[data-reopen-modal-id]');
        if (simpleReopen) {
            const modalId = simpleReopen.dataset.reopenModalId;
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
            return;
        }

        // Case 2: Form-based modal reopen (multiple modals, keyed by form_id)
        const formReopen = document.querySelector('[data-reopen-form-id]');
        if (!formReopen) return;

        const formId = formReopen.dataset.reopenFormId;
        if (!formId) return;

        // Check if it matches a post edit modal pattern
        const postPrefix      = formReopen.dataset.reopenPostPrefix || '';
        const postModalPrefix = formReopen.dataset.reopenPostModalPrefix || '';

        if (postPrefix && formId.startsWith(postPrefix)) {
            const postId  = formId.replace(postPrefix, '');
            const modalEl = document.getElementById(postModalPrefix + postId);
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
            return;
        }

        // Check the modal map for standard form_id → modal_id mapping
        const mapJson = formReopen.dataset.modalMap;
        if (mapJson) {
            try {
                const modalMap = JSON.parse(mapJson);
                const modalId  = modalMap[formId];
                if (modalId) {
                    const modalEl = document.getElementById(modalId);
                    if (modalEl) {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                }
            } catch (e) {
                // Silently ignore JSON parse errors
            }
        }
    });
}
