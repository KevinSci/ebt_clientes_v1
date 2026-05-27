/**
 * readMore.js
 *
 * Implements "Ver más / Ver menos" expand/collapse for long post descriptions.
 * Works purely via DOM manipulation — no page reload.
 *
 * Expected markup:
 *   <div class="ebt-read-more" data-needs-trim="true">
 *     <p class="ebt-read-more__text">
 *       <span class="ebt-read-more__preview">First 150 chars…</span>
 *       <span class="ebt-read-more__ellipsis">…</span>
 *       <span class="ebt-read-more__full d-none">rest of text</span>
 *     </p>
 *     <button class="ebt-read-more__btn" aria-expanded="false">Ver más</button>
 *   </div>
 *
 * Usage:
 *   import { initReadMore } from '/js/modules/readMore.js';
 *   initReadMore();
 */

export function initReadMore() {
    document.querySelectorAll('.ebt-read-more[data-needs-trim="true"]').forEach((widget) => {
        const btn      = widget.querySelector('.ebt-read-more__btn');
        const full     = widget.querySelector('.ebt-read-more__full');
        const ellipsis = widget.querySelector('.ebt-read-more__ellipsis');

        if (!btn || !full) return;

        btn.addEventListener('click', () => {
            const isExpanded = btn.getAttribute('aria-expanded') === 'true';

            if (isExpanded) {
                // Collapse
                full.classList.add('d-none');
                ellipsis?.classList.remove('d-none');
                btn.textContent = 'Ver más';
                btn.setAttribute('aria-expanded', 'false');
            } else {
                // Expand
                full.classList.remove('d-none');
                ellipsis?.classList.add('d-none');
                btn.textContent = 'Ver menos';
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });
}
