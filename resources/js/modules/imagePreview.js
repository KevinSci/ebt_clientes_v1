/**
 * imagePreview.js
 *
 * Generates a visual preview of selected files and folders before form submission.
 * Validates file extensions and sizes, and supports drop-in folders via webkitdirectory inputs.
 */

const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];

/**
 * @param {string} inputId       - The ID of the <input type="file"> element.
 * @param {string} containerId   - The ID of the container element for previews.
 * @param {string} folderInputId - The ID of the optional <input type="file" webkitdirectory> element.
 */
export function initImagePreview(inputId, containerId, folderInputId = null) {
    const input       = document.getElementById(inputId);
    const container   = document.getElementById(containerId);
    const folderInput = folderInputId ? document.getElementById(folderInputId) : null;

    if (!input || !container) return;

    // stagedFiles will hold objects: { file: File, folderName: string|null, folderPath: string|null }
    let stagedFiles = [];

    // Helper to display errors visually
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block mb-3 ebt-preview-error';
        errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${message}`;
        container.parentNode.insertBefore(errorDiv, container);
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        stagedFiles.forEach(item => dataTransfer.items.add(item.file));
        input.files = dataTransfer.files;

        // Sync hidden inputs for folder paths
        const form = input.closest('form');
        if (form) {
            form.querySelectorAll('.ebt-folder-hidden-input').forEach(el => el.remove());
            stagedFiles.forEach(item => {
                if (item.folderName) {
                    const nameInput = document.createElement('input');
                    nameInput.type = 'hidden';
                    nameInput.name = 'attachment_folder_names[]';
                    nameInput.className = 'ebt-folder-hidden-input';
                    nameInput.value = item.folderName;

                    const pathInput = document.createElement('input');
                    pathInput.type = 'hidden';
                    pathInput.name = 'attachment_folder_paths[]';
                    pathInput.className = 'ebt-folder-hidden-input';
                    pathInput.value = item.folderPath || '';

                    form.appendChild(nameInput);
                    form.appendChild(pathInput);
                } else {
                    // Para mantener el orden y sincronía en el backend,
                    // enviamos valores vacíos para archivos individuales
                    const nameInput = document.createElement('input');
                    nameInput.type = 'hidden';
                    nameInput.name = 'attachment_folder_names[]';
                    nameInput.className = 'ebt-folder-hidden-input';
                    nameInput.value = '';

                    const pathInput = document.createElement('input');
                    pathInput.type = 'hidden';
                    pathInput.name = 'attachment_folder_paths[]';
                    pathInput.className = 'ebt-folder-hidden-input';
                    pathInput.value = '';

                    form.appendChild(nameInput);
                    form.appendChild(pathInput);
                }
            });

            // Manage the _has_folder_uploads flag so folderUploader.js can detect folder files
            const hasFolders = stagedFiles.some(item => item.folderName);
            let flagInput = form.querySelector('input[name="_has_folder_uploads"]');
            if (hasFolders && !flagInput) {
                flagInput       = document.createElement('input');
                flagInput.type  = 'hidden';
                flagInput.name  = '_has_folder_uploads';
                flagInput.value = '1';
                form.appendChild(flagInput);
            } else if (!hasFolders && flagInput) {
                flagInput.remove();
            }
        }
    }

    function renderPreview() {
        container.innerHTML = '';
        if (stagedFiles.length === 0) return;

        const grid = document.createElement('div');
        grid.className = 'ebt-file-preview__grid';

        // Separate files into individual and folder groups
        const individuals = stagedFiles.filter(item => !item.folderName);
        const folderGroups = {};

        stagedFiles.forEach(item => {
            if (item.folderName) {
                if (!folderGroups[item.folderName]) {
                    folderGroups[item.folderName] = [];
                }
                folderGroups[item.folderName].push(item);
            }
        });

        // 1. Render folders first
        Object.keys(folderGroups).forEach(folderName => {
            const items = folderGroups[folderName];
            const sizeSum = items.reduce((acc, item) => acc + item.file.size, 0);

            const card = document.createElement('div');
            card.className = 'ebt-file-preview__item ebt-file-preview__item--folder ebt-file-preview__item--doc';

            // Delete folder button
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'ebt-file-preview__delete-btn';
            deleteBtn.innerHTML = '<i class="bi bi-x"></i>';
            deleteBtn.title = `Eliminar carpeta ${folderName}`;
            deleteBtn.setAttribute('aria-label', `Eliminar carpeta ${folderName}`);
            deleteBtn.addEventListener('click', () => {
                stagedFiles = stagedFiles.filter(item => item.folderName !== folderName);
                updateInputFiles();
                renderPreview();
            });
            card.appendChild(deleteBtn);

            const icon = document.createElement('i');
            icon.className = 'bi bi-folder-fill ebt-file-preview__doc-icon text-warning fs-3';

            const name = document.createElement('span');
            name.className = 'ebt-file-preview__name fw-semibold';
            name.textContent = _truncateName(folderName);
            name.title = folderName;

            const size = document.createElement('span');
            size.className = 'ebt-file-preview__size text-muted small';
            size.textContent = `${items.length} archivo(s) (${_formatSize(sizeSum)})`;

            card.appendChild(icon);
            card.appendChild(name);
            card.appendChild(size);
            grid.appendChild(card);
        });

        // 2. Render individual files
        individuals.forEach((item, index) => {
            const file = item.file;
            const isImage = file.type.startsWith('image/');
            const card = document.createElement('div');
            card.className = 'ebt-file-preview__item';

            // Delete file button
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'ebt-file-preview__delete-btn';
            deleteBtn.innerHTML = '<i class="bi bi-x"></i>';
            deleteBtn.title = `Eliminar ${file.name}`;
            deleteBtn.setAttribute('aria-label', `Eliminar ${file.name}`);
            deleteBtn.addEventListener('click', () => {
                // Find index of this item in the main stagedFiles array
                const mainIndex = stagedFiles.indexOf(item);
                if (mainIndex > -1) {
                    stagedFiles.splice(mainIndex, 1);
                }
                updateInputFiles();
                renderPreview();
            });
            card.appendChild(deleteBtn);

            if (isImage) {
                _buildImagePreview(file, card);
            } else {
                _buildDocPreview(file, card);
            }

            grid.appendChild(card);
        });

        container.appendChild(grid);
    }

    // Individual file input change event
    input.addEventListener('change', () => {
        if (!input.files || input.files.length === 0) return;

        Array.from(input.files).forEach((file) => {
            const isDuplicate = stagedFiles.some(item => 
                !item.folderName &&
                item.file.name === file.name && 
                item.file.size === file.size && 
                item.file.lastModified === file.lastModified
            );
            if (!isDuplicate) {
                stagedFiles.push({ file, folderName: null, folderPath: null });
            }
        });

        updateInputFiles();
        renderPreview();
    });

    // Folder input change event
    if (folderInput) {
        folderInput.addEventListener('change', () => {
            if (!folderInput.files || folderInput.files.length === 0) return;

            const filesArray = Array.from(folderInput.files);
            let folderName = '';
            let hasInvalidFile = false;
            let invalidFileName = '';

            // Check files and validate extensions
            for (let i = 0; i < filesArray.length; i++) {
                const file = filesArray[i];
                
                // Extract folder name from webkitRelativePath
                if (!folderName && file.webkitRelativePath) {
                    folderName = file.webkitRelativePath.split('/')[0];
                }

                const ext = file.name.split('.').pop().toLowerCase();
                if (!ALLOWED_EXTENSIONS.includes(ext)) {
                    hasInvalidFile = true;
                    invalidFileName = file.name;
                    break;
                }
            }

            if (hasInvalidFile) {
                showError(`La carpeta contiene archivos no permitidos (ej. "${invalidFileName}"). Se descartó la carpeta completa.`);
                folderInput.value = '';
                return;
            }

            if (!folderName) {
                folderName = 'Carpeta';
            }

            // Check duplicate folder in current selection
            const isFolderDuplicate = stagedFiles.some(item => item.folderName === folderName);
            if (isFolderDuplicate) {
                showError(`La carpeta "${folderName}" ya está agregada.`);
                folderInput.value = '';
                return;
            }

            // Add all folder files to stagedFiles
            filesArray.forEach(file => {
                // Extract the relative path without the file name itself (e.g. "Folder/Subfolder")
                const relativePath = file.webkitRelativePath;
                const pathParts = relativePath.split('/');
                pathParts.pop(); // Remove file name
                const folderPath = pathParts.join('/');

                stagedFiles.push({
                    file,
                    folderName: folderName,
                    folderPath: folderPath
                });
            });

            // Reset folderInput file selection so the same folder can be loaded again if desired
            folderInput.value = '';

            updateInputFiles();
            renderPreview();
        });
    }

    const form = input.closest('form');
    if (form) {
        form.addEventListener('reset', () => {
            stagedFiles = [];
            container.innerHTML = '';
            form.querySelectorAll('.ebt-folder-hidden-input').forEach(el => el.remove());
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
