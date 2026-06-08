// resources/js/app.js
// Bootstrap JS (makes window.bootstrap available for modal, dropdown, etc.)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import { initImagePreview } from './modules/imagePreview.js';
import { initReadMore }     from './modules/readMore.js';
import { initImageViewer }  from './modules/imageViewer.js';

// Re-export Vanilla JS modules
export { initImagePreview, initReadMore, initImageViewer };

// Expose globally on the window object
window.initImagePreview = initImagePreview;
window.initReadMore     = initReadMore;
window.initImageViewer  = initImageViewer;

// Import images/assets so Vite processes them
import.meta.glob([
    '../img/**',
]);