// resources/js/app.js
// Bootstrap JS (makes window.bootstrap available for modal, dropdown, etc.)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Re-export Vanilla JS modules so Blade views can import them
// via: import { initXxx } from '/js/modules/xxx.js'
// (Vite copies them to public/js/modules/ during build)
export { initImagePreview } from './modules/imagePreview.js';
export { initReadMore }     from './modules/readMore.js';
export { initImageViewer }  from './modules/imageViewer.js';

// Import images/assets so Vite processes them
import.meta.glob([
    '../img/**',
]);