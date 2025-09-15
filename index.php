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
                    <button class="export-btn" onclick="exportData('excel')">📊 Exportar página actual (Excel)</button>
                    <button class="export-btn" onclick="exportData('csv')">📄 Exportar página actual (CSV)</button>
                    <button class="export-btn" onclick="exportData('pdf')">📋 Exportar página actual (PDF)</button>

                    <hr style="margin: 15px 0; border-color: #ddd;">

                    <button class="export-btn" style="background: #28a745; color: white;" onclick="exportData('excel', true)">🚀 Exportar TODO (Excel)</button>
                    <button class="export-btn" style="background: #28a745; color: white;" onclick="exportData('csv', true)">🚀 Exportar TODO (CSV)</button>
                    <button class="export-btn" style="background: #28a745; color: white;" onclick="exportData('pdf', true)">🚀 Exportar TODO (PDF)</button>

                    <p style="font-size: 0.8em; color: #666; margin-top: 10px;">
                        📌 *“Exportar TODO” usa todos los registros filtrados (hasta 1000), no solo la página actual.
                    </p>
                </div>
                <div class="auto-refresh">
                    <label class="switch">
                        <input type="checkbox" id="autoRefresh" checked>
                        <span class="slider"></span>
                    </label>
                    <span>Auto-refresh (30s)</span>
                </div>
                <button class="refresh-btn" onclick="loadData()">
                    <span class="spinner" id="spinner" style="display: none;"></span>
                    🔄 Actualizar
                </button>
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
        loadDashboardState();
        loadData();
        setupAutoRefresh();
        setupFilterListeners();
        updateSortIndicators();
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
    document.getElementById('dateFrom').addEventListener('change', function() {
        console.log('📅 Fecha FROM cambiada:', this.value);
        loadData();
        saveDashboardState();
    });
    
    document.getElementById('dateTo').addEventListener('change', function() {
        console.log('📅 Fecha TO cambiada:', this.value);
        loadData();
        saveDashboardState();
    });
    
    let filenameTimeout;
    document.getElementById('filenameFilter').addEventListener('input', function() {
        console.log('🔍 Filtro filename cambiado:', this.value);
        clearTimeout(filenameTimeout);
        filenameTimeout = setTimeout(() => {
            loadData();
            saveDashboardState();
        }, 500);
    });
    
    document.querySelectorAll('input[name="filenameLogic"]').forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('🔗 Lógica filename cambiada:', this.value);
            loadData();
            saveDashboardState();
        });
    });
    
    document.getElementById('eventFilter').addEventListener('change', function() {
        console.log('🎯 Filtro evento cambiado:', this.value);
        loadData();
        saveDashboardState();
    });
    
    document.getElementById('showSizeColumn').addEventListener('change', function() {
        console.log('📏 Toggle tamaño cambiado:', this.checked);
        updateTable();
        saveDashboardState();
    });
    
    document.getElementById('autoRefresh').addEventListener('change', function() {
        console.log('⏰ Auto-refresh cambiado:', this.checked);
        setupAutoRefresh();
        saveDashboardState();
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function toggleDebug() {
    debugMode = !debugMode;
    console.log('🐛 Debug mode:', debugMode ? 'ACTIVADO' : 'DESACTIVADO');
    
    if (debugMode) {
        console.log('📊 Estado actual:', {
            allData: allData.length,
            filteredData: filteredData.length,
            selectedRows: selectedRows.size,
            currentPage: currentPage,
            isLoadingData: isLoadingData
        });
    }
}

function showFilterStatus() {
    if (debugMode) {
        const status = {
            dateFrom: document.getElementById('dateFrom').value,
            dateTo: document.getElementById('dateTo').value,
            filename: document.getElementById('filenameFilter').value,
            filenameLogic: document.querySelector('input[name="filenameLogic"]:checked')?.value,
            event: document.getElementById('eventFilter').value,
            selectedPcs: Array.from(document.querySelectorAll('#pcFilter input[type="checkbox"]:checked:not(#selectAllPcs)')).length
        };
        console.log('🔍 Estado de filtros:', status);
    }
}

document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.altKey && e.key === 'd') {
        e.preventDefault();
        toggleDebug();
    }
    if (e.ctrlKey && e.altKey && e.key === 's') {
        e.preventDefault();
        showFilterStatus();
    }
});

window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        console.log('🧹 Auto-refresh limpiado');
    }
    saveDashboardState();
});
</script>

</body>  
</html>