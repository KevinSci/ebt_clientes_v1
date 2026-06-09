/**
 * attachmentDeletion.js
 *
 * Handles toggling the "marked for deletion" state of existing file
 * attachments in the post edit modal. Uses event delegation instead
 * of inline onclick handlers.
 *
 * Expected markup:
 *   <button type="button"
 *           class="ebt-file-preview__delete-btn ebt-attachment-delete-toggle"
 *           data-attachment-id="42"
 *           title="Marcar para eliminar">
 *       <i class="bi bi-x"></i>
 *   </button>
 *
 *   <!-- Hidden checkbox and overlay must follow the naming convention: -->
 *   <input id="del-att-42" type="checkbox" ...>
 *   <div id="del-overlay-42" ...></div>
 *
 * Usage:
 *   import { initAttachmentDeletion } from './modules/attachmentDeletion.js';
 *   initAttachmentDeletion();
 */

export function initAttachmentDeletion() {
    document.addEventListener('click', function (event) {
        const btn = event.target.closest('.ebt-attachment-delete-toggle');
        if (!btn) return;

        const attachmentId = btn.dataset.attachmentId;
        if (!attachmentId) return;

        const checkbox = document.getElementById('del-att-' + attachmentId);
        const card     = checkbox ? checkbox.closest('.ebt-existing-attachment') : null;
        const overlay  = document.getElementById('del-overlay-' + attachmentId);

        if (!checkbox || !card) return;

        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            card.classList.add('border-danger');
            card.style.opacity = '0.5';
            if (overlay) {
                overlay.classList.remove('d-none');
                overlay.classList.add('d-flex');
            }
            btn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i>';
            btn.title = 'Deshacer';
            btn.style.backgroundColor = '#6c757d';
        } else {
            card.classList.remove('border-danger');
            card.style.opacity = '1';
            if (overlay) {
                overlay.classList.remove('d-flex');
                overlay.classList.add('d-none');
            }
            btn.innerHTML = '<i class="bi bi-x"></i>';
            btn.title = 'Marcar para eliminar';
            btn.style.backgroundColor = '';
        }
    });
}
