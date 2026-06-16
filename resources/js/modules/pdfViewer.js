/**
 * pdfViewer.js
 *
 * Intercepts clicks on PDF document links on mobile devices,
 * and opens them in the custom PDF.js viewer route instead
 * of forcing a download or opening natively.
 */
export function initPdfViewer() {
    // Detect mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
        || (window.innerWidth <= 768);

    if (!isMobile) return;

    // Use event delegation on the body/document to intercept clicks on .ebt-pdf-link
    document.addEventListener('click', function (event) {
        const link = event.target.closest('.ebt-pdf-link');
        if (!link) return;

        // Prevent default browser behavior (opening direct URL or downloading)
        event.preventDefault();

        const filePath = link.dataset.filePath;
        const fileName = link.dataset.fileName;
        if (!filePath) return;

        // Open the custom PDF.js viewer in a new tab/window
        let viewerUrl = `/pdf-viewer?file=${encodeURIComponent(filePath)}`;
        if (fileName) {
            viewerUrl += `&name=${encodeURIComponent(fileName)}`;
        }
        window.open(viewerUrl, '_blank');
    });
}
