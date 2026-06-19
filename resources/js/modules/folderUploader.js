/**
 * folderUploader.js
 *
 * Intercepts new-post or edit-post form submission when:
 *   1. Folder uploads are staged (flag `_has_folder_uploads` is present) OR
 *   2. The total number of files staged exceeds 15 (bypassing PHP's max_file_uploads & body limits).
 *
 * Instead of sending all files in a single synchronous request, it:
 *   1. Sends post metadata (and deletion flags) to the AJAX store/update endpoint.
 *   2. Uploads ALL files sequentially in a Promise chain.
 *   3. Displays a unified progress toast.
 */

// ─── Progress Toast ──────────────────────────────────────────────────────────

class UploadProgressToast {
    constructor(total, isEdit = false) {
        this.total  = total;
        this.isEdit = isEdit;
        this._el    = null;
    }

    show() {
        const wrapper = document.createElement('div');
        wrapper.id        = 'ebt-folder-upload-wrapper';
        wrapper.className = 'position-fixed bottom-0 end-0 p-3';
        wrapper.style.zIndex = '1100';

        const actionText = this.isEdit ? 'Guardando cambios' : 'Subiendo archivos';

        wrapper.innerHTML = `
            <div class="toast show border shadow-lg" role="status" aria-live="polite" style="min-width:300px; max-width:340px;">
                <div class="toast-header">
                    <i class="bi bi-cloud-arrow-up-fill text-primary me-2 fs-5"></i>
                    <strong class="me-auto">${actionText}</strong>
                    <span class="badge bg-secondary rounded-pill ms-2" id="ebt-up-counter">0 / ${this.total}</span>
                </div>
                <div class="toast-body pt-2 pb-3 px-3">
                    <div class="progress mb-2" style="height:8px; border-radius:4px;">
                        <div id="ebt-up-bar"
                             class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                             role="progressbar"
                             style="width:0%; transition: width 0.3s ease;">
                        </div>
                    </div>
                    <small class="text-muted d-block" id="ebt-up-msg">Procesando publicación...</small>
                </div>
            </div>
        `;

        document.body.appendChild(wrapper);
        this._el = wrapper;
    }

    update(done, errors) {
        const pct = Math.round((done / this.total) * 100);
        this._set('ebt-up-counter', `${done} / ${this.total}`);
        this._setStyle('ebt-up-bar', 'width', `${pct}%`);
        this._set('ebt-up-msg', errors > 0
            ? `Subiendo… (${errors} con error)`
            : `Subiendo archivos… ${pct}%`);
    }

    complete(uploaded, errors) {
        const bar = document.getElementById('ebt-up-bar');
        if (bar) {
            bar.style.width = '100%';
            bar.classList.remove('progress-bar-striped', 'progress-bar-animated', 'bg-primary');
            bar.classList.add(errors > 0 ? 'bg-warning' : 'bg-success');
        }
        this._set('ebt-up-counter', `${uploaded} / ${this.total}`);
        this._set('ebt-up-msg', errors > 0
            ? `✔ ${uploaded} subidos   ⚠ ${errors} con error — redirigiendo…`
            : `✔ ${uploaded} archivos subidos — redirigiendo…`);
    }

    serverError(msg) {
        const bar = document.getElementById('ebt-up-bar');
        if (bar) {
            bar.style.width = '100%';
            bar.classList.remove('progress-bar-striped', 'progress-bar-animated', 'bg-primary');
            bar.classList.add('bg-danger');
        }
        this._set('ebt-up-msg', `✗ ${msg}`);
    }

    remove() {
        if (this._el) this._el.remove();
    }

    _set(id, text) {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    }

    _setStyle(id, prop, val) {
        const el = document.getElementById(id);
        if (el) el.style[prop] = val;
    }
}

// ─── Validation error helpers ─────────────────────────────────────────────────

function _showFieldError(form, name, message) {
    const input = form.querySelector(`[name="${name}"]`);
    if (!input) return;
    input.classList.add('is-invalid');

    let fb = input.parentElement.querySelector('.invalid-feedback');
    if (!fb) {
        fb = document.createElement('div');
        fb.className = 'invalid-feedback d-block';
        input.parentElement.appendChild(fb);
    }
    fb.textContent = message;
}

function _clearFieldErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
}

function _applyServerErrors(form, errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        _showFieldError(form, field, Array.isArray(messages) ? messages[0] : messages);
    });
}

// ─── Main initializer ─────────────────────────────────────────────────────────

/**
 * @param {string} formId              ID of the form (new or edit post).
 * @param {string} ajaxEndpointUrl     URL for Step 1 post metadata submission.
 * @param {string} uploadUrlTemplate   URL template with '__POST_ID__' placeholder for single-file upload.
 * @param {boolean} isEdit             Whether this is an edit form or a new post form.
 */
export function initFolderUploader(formId, ajaxEndpointUrl, uploadUrlTemplate, isEdit = false) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        const fileInput = form.querySelector('input[type="file"][name="attachments[]"]');
        const allFiles  = fileInput ? Array.from(fileInput.files) : [];
        const hasFolders = !!form.querySelector('input[name="_has_folder_uploads"]');

        // We only intercept if folders are present OR if total files exceeds 15
        const shouldIntercept = hasFolders || allFiles.length > 15;
        if (!shouldIntercept) return;

        e.preventDefault();
        _clearFieldErrors(form);

        // Client-side validation for required fields (mainly for creation/edit)
        const titleVal = form.querySelector('[name="title"]')?.value?.trim();
        const descVal  = form.querySelector('[name="description"]')?.value?.trim();
        if (!titleVal) { _showFieldError(form, 'title', 'El campo título es obligatorio.'); return; }
        if (!descVal)  { _showFieldError(form, 'description', 'El campo descripción es obligatorio.'); return; }

        // ── Step 1: Submit post metadata via AJAX ───────────────────────────
        const step1Data = new FormData();

        // Copy all non-file elements from the form to step1Data (handles spoofing, tokens, checkbox lists, etc.)
        const formElements = form.elements;
        for (let i = 0; i < formElements.length; i++) {
            const el = formElements[i];
            if (!el.name) continue;
            if (el.type === 'file') continue;
            if (el.name.startsWith('attachment_folder_')) continue;
            if (el.name === '_has_folder_uploads') continue;

            // Handle checkboxes (like delete_attachments[])
            if (el.type === 'checkbox') {
                if (el.checked) {
                    step1Data.append(el.name, el.value);
                }
            } else {
                step1Data.append(el.name, el.value);
            }
        }

        // Show progress toast
        const toast = new UploadProgressToast(allFiles.length, isEdit);
        toast.show();

        let postData;
        try {
            // Determine HTTP method (for AJAX requests, POST is used with _method=PUT inside FormData for Laravel updates)
            const res = await fetch(ajaxEndpointUrl, {
                method : 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body   : step1Data,
            });

            if (res.status === 422) {
                const json = await res.json();
                toast.serverError('Error de validación. Revisa los campos.');
                _applyServerErrors(form, json.errors || {});
                setTimeout(() => toast.remove(), 4000);
                return;
            }

            if (!res.ok) {
                toast.serverError('Error al procesar la publicación. Intenta de nuevo.');
                return;
            }

            postData = await res.json();
        } catch {
            toast.serverError('Error de red. Verifica tu conexión.');
            return;
        }

        const { post_id, redirect_url } = postData;
        const uploadUrl = uploadUrlTemplate.replace('__POST_ID__', post_id);

        // ── Step 2: Upload ALL files sequentially ────────────────────────────
        const folderNameInputs = Array.from(form.querySelectorAll('input.ebt-folder-hidden-input[name="attachment_folder_names[]"]'));
        const folderPathInputs = Array.from(form.querySelectorAll('input.ebt-folder-hidden-input[name="attachment_folder_paths[]"]'));
        const csrfToken        = form.querySelector('input[name="_token"]')?.value || '';

        let done   = 0;
        let errors = 0;

        for (let idx = 0; idx < allFiles.length; idx++) {
            const file       = allFiles[idx];
            const folderName = folderNameInputs[idx]?.value || '';
            const folderPath = folderPathInputs[idx]?.value || '';

            const fd = new FormData();
            fd.append('_token',      csrfToken);
            fd.append('attachment',  file);
            fd.append('folder_name', folderName);
            fd.append('folder_path', folderPath);

            try {
                const res = await fetch(uploadUrl, {
                    method : 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body   : fd,
                });
                res.ok ? done++ : errors++;
            } catch {
                errors++;
            }

            toast.update(done + errors, errors);
        }

        // ── Step 3: Complete and redirect ─────────────────────────────────────
        toast.complete(done, errors);
        setTimeout(() => {
            window.location.href = redirect_url;
        }, errors > 0 ? 2500 : 1200);
    });
}
