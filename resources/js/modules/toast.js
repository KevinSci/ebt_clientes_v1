/**
 * toast.js
 *
 * Auto-initializes Bootstrap toast notifications from a <template> element.
 * Skips display on back/forward navigation to prevent flash-on-history.
 *
 * Expected markup (rendered by Blade only when a flash message exists):
 *   <div class="toast-container ...">
 *     <template id="toast-template">
 *       <div class="toast ...">...</div>
 *     </template>
 *   </div>
 *
 * Usage:
 *   import { initToast } from './modules/toast.js';
 *   initToast();
 */

/**
 * Check if the current navigation is a back/forward history traversal.
 * @returns {boolean}
 */
function isBackForwardNavigation() {
    const entries = window.performance && window.performance.getEntriesByType
        ? window.performance.getEntriesByType('navigation')
        : [];
    return entries.length > 0 && entries[0].type === 'back_forward';
}

/**
 * Initialize toast display from the #toast-template element.
 * Does nothing if the template is absent (no flash messages).
 */
export function initToast() {
    document.addEventListener('DOMContentLoaded', function () {
        // Abort on back/forward navigation to prevent flash-on-history
        if (isBackForwardNavigation()) {
            return;
        }

        const template  = document.getElementById('toast-template');
        const container = document.querySelector('.toast-container');

        if (!template || !container) return;

        // Clone the toast from the template and inject it into the container
        const clone   = template.content.cloneNode(true);
        const toastEl = clone.querySelector('.toast');

        if (toastEl) {
            container.appendChild(toastEl);

            const toast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 5000 });
            toast.show();
        }
    });
}
