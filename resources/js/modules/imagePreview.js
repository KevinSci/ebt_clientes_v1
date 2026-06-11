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

    let stagedFiles = [];

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        stagedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }

    function renderPreview() {
        container.innerHTML = '';
        if (stagedFiles.length === 0) return;

        const grid = document.createElement('div');
        grid.className = 'ebt-file-preview__grid';

        stagedFiles.forEach((file, index) => {
            const isImage = file.type.startsWith('image/');
            const item    = document.createElement('div');
            item.className = 'ebt-file-preview__item';

            // Botón de eliminar archivo
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'ebt-file-preview__delete-btn';
            deleteBtn.innerHTML = '<i class="bi bi-x"></i>';
            deleteBtn.title = 'Eliminar';
            deleteBtn.setAttribute('aria-label', `Eliminar ${file.name}`);
            deleteBtn.addEventListener('click', () => {
                stagedFiles.splice(index, 1);
                updateInputFiles();
                renderPreview();
            });
            item.appendChild(deleteBtn);

            if (isImage) {
                _buildImagePreview(file, item);
            } else {
                _buildDocPreview(file, item);
            }

            grid.appendChild(item);
        });

        container.appendChild(grid);
    }

    input.addEventListener('change', () => {
        if (!input.files || input.files.length === 0) return;

        Array.from(input.files).forEach((file) => {
            // Evitar duplicados por nombre, tamaño y fecha de modificación
            const isDuplicate = stagedFiles.some(f => 
                f.name === file.name && 
                f.size === file.size && 
                f.lastModified === file.lastModified
            );
            if (!isDuplicate) {
                stagedFiles.push(file);
            }
        });

        updateInputFiles();
        renderPreview();
    });

    const form = input.closest('form');
    if (form) {
        form.addEventListener('reset', () => {
            stagedFiles = [];
            container.innerHTML = '';
        });
    }
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
    const config = _getFileConfig(file.name);
    
    item.classList.add('ebt-file-preview__item--doc');
    item.classList.add(config.colorClass);

    const icon = document.createElement('i');
    icon.className  = `bi ${config.icon} ebt-file-preview__doc-icon ${config.iconColorClass}`;
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
 * Get the config configuration (icon class, container class, icon color class) based on extension.
 * @private
 */
function _getFileConfig(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    switch (ext) {
        case 'pdf':
            return {
                icon: 'bi-file-earmark-pdf-fill',
                colorClass: 'ebt-file-preview__item--pdf',
                iconColorClass: 'text-danger'
            };
        case 'doc':
        case 'docx':
            return {
                icon: 'bi-file-earmark-word-fill',
                colorClass: 'ebt-file-preview__item--word',
                iconColorClass: 'text-primary'
            };
        case 'xls':
        case 'xlsx':
            return {
                icon: 'bi-file-earmark-excel-fill',
                colorClass: 'ebt-file-preview__item--excel',
                iconColorClass: 'text-success'
            };
        case 'zip':
        case 'rar':
            return {
                icon: 'bi-file-earmark-zip-fill',
                colorClass: 'ebt-file-preview__item--zip',
                iconColorClass: 'text-warning'
            };
        default:
            return {
                icon: 'bi-file-earmark-arrow-down-fill',
                colorClass: 'ebt-file-preview__item--generic',
                iconColorClass: 'text-secondary'
            };
    }
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
