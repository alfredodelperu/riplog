<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Impresiones Full Color</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
<div class="container">
    <div class="header">
        <h1>🖨️ Dashboard Full Color</h1>
        <p>Monitoreo en tiempo real de impresiones y procesos RIP</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number" id="totalJobs">-</div>
            <div class="stat-number-selected" id="totalJobsSelected" style="display:none;">Selec: -</div>
            <div class="stat-label">Total Jobs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="ripJobs">-</div>
            <div class="stat-number-selected" id="ripJobsSelected" style="display:none;">Selec: -</div>
            <div class="stat-label">RIP Jobs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="printJobs">-</div>
            <div class="stat-number-selected" id="printJobsSelected" style="display:none;">Selec: -</div>
            <div class="stat-label">Print Jobs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="mlTotal">-</div>
            <div class="stat-number-selected" id="mlTotalSelected" style="display:none;">Selec: -</div>
            <div class="stat-label">ML Total (m)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="m2Total">-</div>
            <div class="stat-number-selected" id="m2TotalSelected" style="display:none;">Selec: -</div>
            <div class="stat-label">M² Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="uniquePcs">-</div>
            <div class="stat-number-selected" id="uniquePcsSelected" style="display:none;">Selec: -</div>
            <div class="stat-label">PCs Únicas</div>
        </div>
    </div>

    <div class="filters-container">
        <h3 style="margin-bottom: 20px; color: #333;">🔍 Filtros</h3>
        
        <div class="filters-row">
            <div class="filter-group">
                <label class="filter-label">📅 Rango de Fechas</label>
                <div style="display: flex; gap: 10px;">
                    <input type="date" class="filter-input" id="dateFrom" style="flex: 1;">
                    <input type="date" class="filter-input" id="dateTo" style="flex: 1;">
                </div>
                <div class="date-shortcuts">
                    <button class="date-shortcut active" onclick="setDateRange('today')">Hoy</button>
                    <button class="date-shortcut" onclick="setDateRange('yesterday')">Ayer</button>
                    <button class="date-shortcut" onclick="setDateRange('thisWeek')">Esta Semana</button>
                    <button class="date-shortcut" onclick="setDateRange('lastWeek')">Semana Pasada</button>
                    <button class="date-shortcut" onclick="setDateRange('thisMonth')">Este Mes</button>
                    <button class="date-shortcut" onclick="setDateRange('lastMonth')">Mes Pasado</button>
                </div>
            </div>

            <div class="filter-group">
                <label class="filter-label">📝 Nombre de Archivo</label>
                <input type="text" class="filter-input" id="filenameFilter" placeholder="Buscar archivos... (separa con comas)">
                <div class="filename-filter-options">
                    <div class="checkbox-container">
                        <input type="radio" name="filenameLogic" value="or" id="filenameOr" checked>
                        <label for="filenameOr">OR (cualquier término)</label>
                    </div>
                    <div class="checkbox-container">
                        <input type="radio" name="filenameLogic" value="and" id="filenameAnd">
                        <label for="filenameAnd">AND (todos los términos)</label>
                    </div>
                </div>
            </div>

            <div class="filter-group">
                <label class="filter-label">💻 Computadoras</label>
                <div class="multi-select" id="pcFilter">
                    <div class="loading">Cargando PCs...</div>
                </div>
            </div>

            <div class="filter-group">
                <label class="filter-label">🎯 Tipo de Evento</label>
                <select class="filter-select" id="eventFilter">
                    <option value="">Todos los eventos</option>
                    <option value="RIP">Solo RIP</option>
                    <option value="PRINT">Solo PRINT</option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">📊 Registro de Actividad</h2>
            <div class="table-controls">
                <div class="show-size-toggle">
                    <label class="switch">
                        <input type="checkbox" id="showSizeColumn">
                        <span class="slider"></span>
                    </label>
                    <span>Mostrar Tamaño (m²)</span>
                </div>
                <div class="export-buttons">
                    <!-- Exportar página actual -->
                    <button class="export-icon" onclick="exportData('excel')" title="Exportar página actual como Excel">📊</button>
                    <button class="export-icon" onclick="exportData('csv')" title="Exportar página actual como CSV">📄</button>
                    <button class="export-icon" onclick="exportData('pdf')" title="Exportar página actual como PDF">📋</button>

                    <hr style="margin: 15px 0; border-color: #ddd;">

                    <!-- Exportar TODO el resultado filtrado -->
                    <button class="export-icon" onclick="exportData('excel', true)" title="Exportar todos los registros filtrados (Excel)">🚀</button>
                    <button class="export-icon" onclick="exportData('csv', true)" title="Exportar todos los registros filtrados (CSV)">🚀</button>
                    <button class="export-icon" onclick="exportData('pdf', true)" title="Exportar todos los registros filtrados (PDF)">🚀</button>

                    <p style="font-size: 0.8em; color: #666; margin-top: 10px;">
                        📌 Haz clic en 🚀 para exportar todos los registros filtrados (hasta 1000).
                    </p>
                </div>
                <div class="auto-refresh">
                    <label class="switch">
                        <input type="checkbox" id="autoRefresh" checked>
                        <span class="slider"></span>
                    </label>
                    <span>Auto-refresh (30s)</span>
                </div>
                <!-- ❌ BOTÓN "ACTUALIZAR" ELIMINADO -->
            </div>
        </div>
        
        <div id="tableContent">
            <div class="loading">
                <div class="spinner"></div>
                Cargando datos...
            </div>
        </div>
        
        <div class="last-update" id="lastUpdate"></div>
    </div>
</div>

<script src="funciones.js"></script>

<script>
let debugMode = false;

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard inicializando...');
    
    try {
        initializeDates();
        
        // Primero: cargar datos para poblar los PCs
        loadData();

        // Luego: cargar estado desde localStorage (ya hay PCs cargados)
        loadDashboardState();

        // Finalmente: configurar eventos
        setupAutoRefresh();
        setupFilterListeners();
        updateSortIndicators();
        document.addEventListener('click', handleColumnClick);

        console.log('🎉 Dashboard inicializado correctamente');
    } catch (error) {
        console.error('❌ Error en inicialización:', error);
    }
});

function setupAutoRefresh() {
    const autoRefreshCheckbox = document.getElementById('autoRefresh');
    
    function updateAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
        if (autoRefreshCheckbox.checked) {
            autoRefreshInterval = setInterval(() => {
                if (!isLoadingData) {
                    loadData();
                }
            }, 30000);
            console.log('🔄 Auto-refresh activado');
        } else {
            console.log('⏸️ Auto-refresh desactivado');
        }
    }
    
    updateAutoRefresh();
    autoRefreshCheckbox.addEventListener('change', updateAutoRefresh);
}

function setupFilterListeners() {
    document.getElementById('dateFrom').addEventListener('change', () => { loadData(); saveDashboardState(); });
    document.getElementById('dateTo').addEventListener('change', () => { loadData(); saveDashboardState(); });

    let filenameTimeout;
    document.getElementById('filenameFilter').addEventListener('input', () => {
        clearTimeout(filenameTimeout);
        filenameTimeout = setTimeout(() => { loadData(); saveDashboardState(); }, 500);
    });

    document.querySelectorAll('input[name="filenameLogic"]').forEach(radio => {
        radio.addEventListener('change', () => { loadData(); saveDashboardState(); });
    });

    document.getElementById('eventFilter').addEventListener('change', () => { loadData(); saveDashboardState(); });
    document.getElementById('showSizeColumn').addEventListener('change', () => { updateTable(); saveDashboardState(); });
    document.getElementById('autoRefresh').addEventListener('change', () => { setupAutoRefresh(); saveDashboardState(); });
}
</script>

</body>  
</html>