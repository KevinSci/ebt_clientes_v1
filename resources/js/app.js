// resources/js/app.js
// Bootstrap JS (makes window.bootstrap available for modal, dropdown, etc.)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import { initImagePreview }       from './modules/imagePreview.js';
import { initReadMore }           from './modules/readMore.js';
import { initImageViewer }        from './modules/imageViewer.js';
import { initToast }              from './modules/toast.js';
import { initModalReopen }        from './modules/modalReopen.js';
import { initAttachmentDeletion } from './modules/attachmentDeletion.js';
import { initProjectPage }        from './modules/projectPageInit.js';
import { initClientProjectPage }  from './modules/clientProjectInit.js';

// Re-export Vanilla JS modules
export {
    initImagePreview,
    initReadMore,
    initImageViewer,
    initToast,
    initModalReopen,
    initAttachmentDeletion,
    initProjectPage,
    initClientProjectPage,
};

// Expose globally on the window object
window.initImagePreview       = initImagePreview;
window.initReadMore           = initReadMore;
window.initImageViewer        = initImageViewer;
window.initToast              = initToast;
window.initModalReopen        = initModalReopen;
window.initAttachmentDeletion = initAttachmentDeletion;
window.initProjectPage        = initProjectPage;
window.initClientProjectPage  = initClientProjectPage;

// Auto-initialize global modules (safe to run on every page)
initToast();
initModalReopen();
initAttachmentDeletion();
initProjectPage();
initClientProjectPage();

// Import images/assets so Vite processes them
import.meta.glob([
    '../img/**',
]);