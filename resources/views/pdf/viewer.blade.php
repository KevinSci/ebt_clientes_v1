<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $fileName }} - Visor PDF</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

    @vite(['resources/css/pdf-viewer.css'])
</head>
<body>

    <!-- Toolbar -->
    <div class="pdf-toolbar">
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.close();" class="btn-control" title="Cerrar pestaña">
                <i class="bi bi-x-lg"></i>
                <span>Cerrar</span>
            </button>
            <div class="pdf-title" title="{{ $fileName }}">{{ $fileName }}</div>
        </div>

        <div class="pdf-controls">
            <!-- Navigation -->
            <button id="prev-page" class="btn-control" title="Página anterior">
                <i class="bi bi-chevron-up"></i>
                <span>Ant</span>
            </button>
            <div class="page-indicator">
                <span id="page-num">1</span> / <span id="page-count">-</span>
            </div>
            <button id="next-page" class="btn-control" title="Página siguiente">
                <i class="bi bi-chevron-down"></i>
                <span>Sig</span>
            </button>

            <!-- Zoom -->
            <button id="zoom-out" class="btn-control" title="Alejar">
                <i class="bi bi-zoom-out"></i>
            </button>
            <button id="zoom-in" class="btn-control" title="Acercar">
                <i class="bi bi-zoom-in"></i>
            </button>

            <!-- Download -->
            <a href="{{ asset('storage/' . $filePath) }}" class="btn-control btn-control-danger" download="{{ $fileName }}" title="Descargar PDF">
                <i class="bi bi-download"></i>
                <span>Descargar</span>
            </a>
        </div>
    </div>

    <!-- Spinner Loader -->
    <div id="loader" class="loader-wrapper">
        <div class="spinner"></div>
        <div class="loader-text" id="loader-text">Cargando documento...</div>
    </div>

    <!-- PDF Viewer Area -->
    <div class="pdf-viewer-container">
        <div class="pdf-page-wrapper">
            <canvas id="pdf-canvas"></canvas>
        </div>
    </div>

    <script>
        window.pdfUrl = "{{ asset('storage/' . $filePath) }}";
    </script>
    @vite(['resources/js/pdf-viewer.js'])
</body>
</html>
