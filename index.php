<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Impresiones Full Color</title>
    <!-- CSS externo -->
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
                    <button class="export-btn" onclick="exportData('excel')">📊 Excel</button>
                    <button class="export-btn" onclick="exportData('csv')">📄 CSV</button>
                    <button class="export-btn" onclick="exportData('pdf')">📋 PDF</button>
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

<!-- JavaScript externo -->
<script src="funciones.js"></script>

<script>
// Variables globales para debug
let debugMode = false;

// INICIALIZACIÓN MEJORADA CON DEBUGGING
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard inicializando...');
    
    try {
        // 1. Inicializar fechas por defecto
        initializeDates();
        console.log('✅ Fechas inicializadas');
        
        // 2. Cargar datos inmediatamente
        loadData();
        console.log('✅ Carga inicial de datos solicitada');
        
        // 3. Configurar auto-refresh mejorado
        setupAutoRefresh();
        console.log('✅ Auto-refresh configurado');
        
        // 4. CORREGIDO: Event listeners para filtros con debounce
        setupFilterListeners();
        console.log('✅ Event listeners configurados');
        
        console.log('🎉 Dashboard inicializado correctamente');
        
    } catch (error) {
        console.error('❌ Error en inicialización:', error);
    }
});

// NUEVA: Configuración mejorada de auto-refresh
function setupAutoRefresh() {
    const autoRefreshCheckbox = document.getElementById('autoRefresh');
    
    function updateAutoRefresh() {
        // Limpiar interval anterior si existe
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
        
        // Configurar nuevo interval si está habilitado
        if (autoRefreshCheckbox.checked) {
            autoRefreshInterval = setInterval(() => {
                console.log('⏰ Auto-refresh ejecutando...');
                if (!isLoadingData) { // Solo si no hay carga en progreso
                    loadData();
                }
            }, 30000); // 30 segundos
            console.log('🔄 Auto-refresh activado');
        } else {
            console.log('⏸️ Auto-refresh desactivado');
        }
    }
    
    // Configurar auto-refresh inicial
    updateAutoRefresh();
    
    // Escuchar cambios en el checkbox
    autoRefreshCheckbox.addEventListener('change', updateAutoRefresh);
}

// NUEVA: Configuración de event listeners para filtros
function setupFilterListeners() {
    // Filtros de fecha - cargan inmediatamente
    document.getElementById('dateFrom').addEventListener('change', function() {
        console.log('📅 Fecha FROM cambiada:', this.value);
        loadData();
    });
    
    document.getElementById('dateTo').addEventListener('change', function() {
        console.log('📅 Fecha TO cambiada:', this.value);
        loadData();
    });
    
    // CORREGIDO: Filtro de nombre de archivo con debounce mejorado
    let filenameTimeout;
    document.getElementById('filenameFilter').addEventListener('input', function() {
        console.log('🔍 Filtro filename cambiado:', this.value);
        
        clearTimeout(filenameTimeout);
        filenameTimeout = setTimeout(() => {
            loadData();
        }, 500); // 500ms de delay para typing
    });
    
    // CORREGIDO: Radio buttons de lógica de filename
    document.querySelectorAll('input[name="filenameLogic"]').forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('🔗 Lógica filename cambiada:', this.value);
            loadData();
        });
    });
    
    // Filtro de eventos
    document.getElementById('eventFilter').addEventListener('change', function() {
        console.log('🎯 Filtro evento cambiado:', this.value);
        loadData();
    });
    
    // Toggle de mostrar tamaño
    document.getElementById('showSizeColumn').addEventListener('change', function() {
        console.log('📏 Toggle tamaño cambiado:', this.checked);
        updateTable(); // Solo actualiza la tabla, no recarga datos
    });
    
    console.log('🎧 Event listeners configurados');
}

// Función helper para debounce mejorado
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

// NUEVA: Función para toggle de debug
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

// NUEVA: Función para mostrar estado de filtros
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

// Limpiar interval al cerrar la página
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        console.log('🧹 Auto-refresh limpiado');
    }
});

// NUEVA: Mostrar atajos de teclado para debug (opcional)
document.addEventListener('keydown', function(e) {
    // Ctrl + Alt + D para toggle debug
    if (e.ctrlKey && e.altKey && e.key === 'd') {
        e.preventDefault();
        toggleDebug();
    }
    
    // Ctrl + Alt + S para mostrar estado
    if (e.ctrlKey && e.altKey && e.key === 's') {
        e.preventDefault();
        showFilterStatus();
    }
});
</script>

</body>  
</html>