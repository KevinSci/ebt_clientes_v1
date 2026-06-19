/**
 * folderViewer.js
 *
 * Handles clicking on folder chips, opening the folder viewer modal,
 * and rendering its content dynamically as a nested, expandable/collapsible tree view.
 */

export function initFolderViewer(modalId, titleElId, bodyElId) {
    const modalEl = document.getElementById(modalId);
    const titleEl = document.getElementById(titleElId);
    const bodyEl  = document.getElementById(bodyElId);

    if (!modalEl || !bodyEl) return;

    const bsModal = new bootstrap.Modal(modalEl);

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.ebt-folder-trigger');
        if (!trigger) return;

        event.preventDefault();

        const folderName = trigger.dataset.folderName || 'Carpeta';
        let files = [];
        try {
            files = JSON.parse(trigger.dataset.files || '[]');
        } catch (e) {
            console.error('Error parsing folder files:', e);
            return;
        }

        if (titleEl) {
            titleEl.textContent = folderName;
        }

        renderFolderContent(bodyEl, files, folderName);
        bsModal.show();
    });
}

/**
 * Builds a nested folder tree structure from a flat array of attachment files.
 */
function buildTree(rootName, files) {
    const root = {
        name: rootName,
        isFolder: true,
        children: [],
        collapsed: false
    };

    files.forEach(file => {
        const path = file.folder_path || '';
        const parts = path ? path.split('/') : [];
        
        let currentNode = root;
        
        // Start after the root folder name if parts[0] matches the root name
        const startIndex = (parts.length > 0 && parts[0] === rootName) ? 1 : 0;
        
        for (let i = startIndex; i < parts.length; i++) {
            const part = parts[i];
            if (!part) continue;
            
            let dirNode = currentNode.children.find(child => child.isFolder && child.name === part);
            if (!dirNode) {
                dirNode = {
                    name: part,
                    isFolder: true,
                    children: [],
                    collapsed: false
                };
                currentNode.children.push(dirNode);
            }
            currentNode = dirNode;
        }
        
        currentNode.children.push({
            name: file.file_name,
            isFolder: false,
            fileData: file
        });
    });

    return root;
}

/**
 * Renders the folder tree as nested DOM elements inside the container.
 */
function renderFolderContent(container, files, rootFolderName) {
    container.innerHTML = '';

    if (files.length === 0) {
        container.innerHTML = '<div class="text-center py-3 text-muted">Esta carpeta está vacía.</div>';
        return;
    }

    // Build the hierarchical tree structure
    const tree = buildTree(rootFolderName, files);

    // Pre-extract all images in the folder to enable swiping/gallery features
    const allImages = files.filter(f => f.type === 'image');
    const imageUrls = allImages.map(img => img.url);
    const imageNames = allImages.map(img => img.file_name);

    /**
     * Recursive function to render a tree node.
     */
    function renderNode(node) {
        if (!node.isFolder) {
            const file = node.fileData;
            const isImage = file.type === 'image';
            
            const fileEl = document.createElement('div');
            fileEl.className = 'ebt-tree-file py-1 px-2 d-flex align-items-center justify-content-between rounded my-1';

            if (isImage) {
                const imgIndex = allImages.findIndex(img => img.url === file.url);
                
                fileEl.innerHTML = `
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <i class="bi bi-image text-info fs-5 ebt-tree-file-icon"></i>
                        <span class="text-truncate small cursor-pointer text-primary ebt-viewer-trigger fw-medium" 
                              data-src="${file.url}" 
                              data-filename="${file.file_name}"
                              data-images='${JSON.stringify(imageUrls)}'
                              data-filenames='${JSON.stringify(imageNames)}'
                              data-index="${imgIndex}"
                              title="${file.file_name}">${file.file_name}</span>
                    </div>
                    <a href="${file.url}" target="_blank" download class="btn btn-link btn-sm p-0 text-muted lh-1" title="Descargar">
                        <i class="bi bi-download fs-6"></i>
                    </a>
                `;
            } else {
                const isPdf = file.is_pdf;
                const pdfClass = isPdf ? 'ebt-pdf-link' : '';
                const pdfData = isPdf ? `data-file-path="${file.file_path}" data-file-name="${file.file_name}"` : '';
                
                const iconClass = file.icon ? file.icon.icon : 'bi-file-earmark-arrow-down-fill';
                const colorClass = file.icon ? file.icon.color : 'text-secondary';

                fileEl.innerHTML = `
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <i class="bi ${iconClass} ${colorClass} fs-5 ebt-tree-file-icon"></i>
                        <a href="${file.url}" target="_blank" rel="noopener noreferrer" 
                           class="${pdfClass} text-truncate small text-dark text-decoration-none fw-medium" 
                           ${pdfData}
                           title="${file.file_name}">${file.file_name}</a>
                    </div>
                    <a href="${file.url}" target="_blank" download class="btn btn-link btn-sm p-0 text-muted lh-1" title="Descargar">
                        <i class="bi bi-download fs-6"></i>
                    </a>
                `;
            }
            return fileEl;
        } else {
            const folderEl = document.createElement('div');
            folderEl.className = 'ebt-tree-folder mb-1';
            
            const rowEl = document.createElement('div');
            rowEl.className = 'ebt-tree-row d-flex align-items-center gap-2 py-1 px-2 rounded cursor-pointer select-none';
            
            const caretEl = document.createElement('i');
            caretEl.className = 'bi bi-caret-down-fill text-muted ebt-tree-caret transition-transform';
            caretEl.style.fontSize = '0.75rem';
            
            if (node.children.length === 0) {
                caretEl.style.visibility = 'hidden';
            }
            
            const iconEl = document.createElement('i');
            iconEl.className = 'bi bi-folder-fill text-warning fs-5';
            
            const nameEl = document.createElement('span');
            nameEl.className = 'fw-semibold text-dark text-truncate small';
            nameEl.textContent = node.name;
            nameEl.title = node.name;

            const filesCount = node.children.filter(c => !c.isFolder).length;
            const subfoldersCount = node.children.filter(c => c.isFolder).length;
            let countText = '';
            if (filesCount > 0 && subfoldersCount > 0) {
                countText = `${subfoldersCount} carp., ${filesCount} arch.`;
            } else if (filesCount > 0) {
                countText = `${filesCount} arch.`;
            } else if (subfoldersCount > 0) {
                countText = `${subfoldersCount} carp.`;
            }
            
            rowEl.appendChild(caretEl);
            rowEl.appendChild(iconEl);
            rowEl.appendChild(nameEl);

            if (countText) {
                const badgeEl = document.createElement('span');
                badgeEl.className = 'badge bg-light text-muted border rounded-pill ms-auto font-monospace';
                badgeEl.style.fontSize = '0.65rem';
                badgeEl.textContent = countText;
                rowEl.appendChild(badgeEl);
            }
            
            const childrenEl = document.createElement('div');
            childrenEl.className = 'ebt-tree-children';
            
            const sortedChildren = [...node.children].sort((a, b) => {
                if (a.isFolder && !b.isFolder) return -1;
                if (!a.isFolder && b.isFolder) return 1;
                return a.name.localeCompare(b.name);
            });
            
            sortedChildren.forEach(child => {
                childrenEl.appendChild(renderNode(child));
            });

            folderEl.appendChild(rowEl);
            folderEl.appendChild(childrenEl);

            // Expand/collapse triggers (single click for high mobile responsiveness, dblclick for desktop feel)
            const toggleCollapse = () => {
                const isCollapsed = folderEl.classList.toggle('ebt-collapsed');
                if (isCollapsed) {
                    caretEl.classList.remove('bi-caret-down-fill');
                    caretEl.classList.add('bi-caret-right-fill');
                    iconEl.classList.remove('bi-folder-fill');
                    iconEl.classList.add('bi-folder');
                } else {
                    caretEl.classList.remove('bi-caret-right-fill');
                    caretEl.classList.add('bi-caret-down-fill');
                    iconEl.classList.remove('bi-folder');
                    iconEl.classList.add('bi-folder-fill');
                }
            };

            rowEl.addEventListener('click', (e) => {
                // Prevent toggling when clicking download links or tags inside if any
                if (e.target.closest('a') || e.target.closest('button')) return;
                toggleCollapse();
            });

            rowEl.addEventListener('dblclick', (e) => {
                e.stopPropagation();
                // Already toggled by single-click, so do nothing here or support dblclick naturally.
            });

            return folderEl;
        }
    }

    // Render the root node children directly into the container
    const sortedRootChildren = [...tree.children].sort((a, b) => {
        if (a.isFolder && !b.isFolder) return -1;
        if (!a.isFolder && b.isFolder) return 1;
        return a.name.localeCompare(b.name);
    });

    sortedRootChildren.forEach(child => {
        container.appendChild(renderNode(child));
    });
}
