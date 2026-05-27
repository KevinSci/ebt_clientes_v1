/**
 * imagePreview.js
 *
 * Generates a visual preview of selected image files before form submission.
 * Non-image files (PDFs) are represented with a document icon chip.
 *
 * Usage:
 *   import { initImagePreview } from '/js/modules/imagePreview.js';
 *   initImagePreview('input-id', 'preview-container-id');
 */

/**
 * @param {string} inputId       - The ID of the <input type="file"> element.
 * @param {string} containerId   - The ID of the container element for previews.
 */
export function initImagePreview(inputId, containerId) {
    const input     = document.getElementById(inputId);
    const container = document.getElementById(containerId);

    if (!input || !container) return;

    input.addEventListener('change', () => {
        container.innerHTML = '';

        if (input.files.length === 0) return;

        const grid = document.createElement('div');
        grid.className = 'ebt-file-preview__grid';

        Array.from(input.files).forEach((file) => {
            const isImage = file.type.startsWith('image/');
            const item    = document.createElement('div');
            item.className = 'ebt-file-preview__item';

            if (isImage) {
                _buildImagePreview(file, item);
            } else {
                _buildDocPreview(file, item);
            }

            grid.appendChild(item);
        });

        container.appendChild(grid);
    });
}

/**
 * Build an image thumbnail preview using FileReader.
 * @private
 */
function _buildImagePreview(file, item) {
    const reader = new FileReader();

    reader.onload = (e) => {
        const img = document.createElement('img');
        img.src       = e.target.result;
        img.alt       = file.name;
        img.className = 'ebt-file-preview__img';

        const name = document.createElement('span');
        name.className   = 'ebt-file-preview__name';
        name.textContent = _truncateName(file.name);

        item.appendChild(img);
        item.appendChild(name);
    };

    reader.readAsDataURL(file);
}

/**
 * Build a document chip preview.
 * @private
 */
function _buildDocPreview(file, item) {
    item.classList.add('ebt-file-preview__item--doc');

    const icon = document.createElement('i');
    icon.className  = 'bi bi-file-earmark-pdf-fill ebt-file-preview__doc-icon';
    icon.setAttribute('aria-hidden', 'true');

    const name = document.createElement('span');
    name.className   = 'ebt-file-preview__name';
    name.textContent = _truncateName(file.name);

    const size = document.createElement('span');
    size.className   = 'ebt-file-preview__size';
    size.textContent = _formatSize(file.size);

    item.appendChild(icon);
    item.appendChild(name);
    item.appendChild(size);
}

/**
 * Truncate a long filename for display.
 * @private
 */
function _truncateName(name, maxLength = 20) {
    if (name.length <= maxLength) return name;
    const ext   = name.substring(name.lastIndexOf('.'));
    const base  = name.substring(0, maxLength - ext.length - 1);
    return `${base}…${ext}`;
}

/**
 * Format bytes into a human-readable string.
 * @private
 */
function _formatSize(bytes) {
    if (bytes < 1024)       return `${bytes} B`;
    if (bytes < 1048576)    return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / 1048576).toFixed(1)} MB`;
}
