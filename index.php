   
 
        

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Impresiones Full Color</title>
    
    
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-number-selected {
            font-size: 1.2em;
            font-weight: bold;
            color: #ff6b6b;
            margin-top: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .filters-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .filters-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-weight: bold;
            color: #333;
            font-size: 0.9em;
        }

        .filter-input, .filter-select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            background: white;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .filter-input:focus, .filter-select:focus {
            border-color: #667eea;
        }

        .date-shortcuts {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .date-shortcut {
            padding: 6px 12px;
            background: #f0f0f0;
            border: none;
            border-radius: 15px;
            font-size: 0.8em;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .date-shortcut:hover, .date-shortcut.active {
            background: #667eea;
            color: white;
        }

        .filename-filter-options {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 8px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9em;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-title {
            font-size: 1.8em;
            color: #333;
            font-weight: bold;
        }

        .table-controls {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .export-buttons {
            display: flex;
            gap: 10px;
        }

        .export-btn {
            padding: 8px 16px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .export-btn:hover {
            background: #667eea;
            color: white;
        }

        .refresh-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .refresh-btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #667eea;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8em;
            position: sticky;
            top: 0;
        }

        th:first-child { 
            border-radius: 10px 0 0 0; 
            width: 50px;
        }
        th:last-child { border-radius: 0 10px 0 0; }

        .filename-col { width: 35%; min-width: 200px; }
        .event-col { width: 8%; }
        .dimensions-col { width: 12%; }
        .copies-col { width: 8%; }
        .ml-col { width: 10%; }
        .pc-col { width: 12%; }
        .date-col { width: 15%; }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
            font-size: 0.9em;
        }

        tr:hover td {
            background-color: #f8f9ff;
        }

        tr.selected td {
            background-color: #e8f4fd !important;
            border-left: 4px solid #667eea;
        }

        .row-checkbox {
            cursor: pointer;
        }

        .status-rip {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 0.8em;
            display: inline-block;
        }

        .status-print {
            background: #e8f5e8;
            color: #388e3c;
            padding: 4px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 0.8em;
            display: inline-block;
        }

        .file-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
            max-width: 100%;
        }

        .file-name-full {
            white-space: normal;
            word-break: break-all;
        }

        .dimensions {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8em;
        }

        .ml-total {
            font-weight: bold;
            color: #2e7d32;
            background: #e8f5e8;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.9em;
        }

        .pc-badge {
            background: #fff3cd;
            color: #856404;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 0.8em;
            display: inline-block;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.2em;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .auto-refresh {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .page-btn {
            padding: 8px 12px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .page-btn:hover, .page-btn.active {
            background: #667eea;
            color: white;
        }

        .last-update {
            font-size: 0.8em;
            color: #888;
            margin-top: 15px;
            text-align: center;
        }

        .show-size-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .multi-select {
            min-height: 120px;
            max-height: 200px;
            overflow-y: auto;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px;
            background: white;
        }

        .multi-select-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .multi-select-option:hover {
            background-color: #f0f0f0;
        }
    </style>


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
                            <label for="filenameOr">OR</label>
                        </div>
                        <div class="checkbox-container">
                            <input type="radio" name="filenameLogic" value="and" id="filenameAnd">
                            <label for="filenameAnd">AND</label>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">💻 Computadoras</label>
                    <div class="multi-select" id="pcFilter">
                        <!-- Se llena dinámicamente -->
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
                        <span>Mostrar Tamaño</span>
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

    <script>
        let autoRefreshInterval;
        let allData = [];
        let filteredData = [];
        let selectedRows = new Set();
        let allPcs = [];
        let currentPage = 1;
        const itemsPerPage = 20;
        
        // Inicializar fechas por defecto (hoy)
        function initializeDates() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dateFrom').value = today;
            document.getElementById('dateTo').value = today;
        }

        function setDateRange(range) {
            const today = new Date();
            let startDate, endDate;

            // Remover clase active de todos los botones
            document.querySelectorAll('.date-shortcut').forEach(btn => btn.classList.remove('active'));
            
            switch(range) {
                case 'today':
                    startDate = endDate = today;
                    break;
                case 'yesterday':
                    startDate = endDate = new Date(today.getTime() - 24 * 60 * 60 * 1000);
                    break;
                case 'thisWeek':
                    const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                    startDate = startOfWeek;
                    endDate = new Date();
                    break;
                case 'lastWeek':
                    const lastWeekStart = new Date(today.setDate(today.getDate() - today.getDay() - 7));
                    const lastWeekEnd = new Date(today.setDate(today.getDate() - today.getDay() - 1));
                    startDate = lastWeekStart;
                    endDate = lastWeekEnd;
                    break;
                case 'thisMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date();
                    break;
                case 'lastMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
            }

            document.getElementById('dateFrom').value = startDate.toISOString().split('T')[0];
            document.getElementById('dateTo').value = endDate.toISOString().split('T')[0];
            
            // Marcar botón como activo
            event.target.classList.add('active');
            
            applyFilters();
        }

        function calculateML(ancho, largo, copias) {
            if (!ancho || !largo || !copias) return 0;
            
            const anchoNum = parseFloat(ancho);
            const largoNum = parseFloat(largo);
            const copiasNum = parseInt(copias);
            
            let dimension;
            
            if (anchoNum >= 60 || largoNum >= 60) {
                dimension = Math.max(anchoNum, largoNum);
            } else {
                dimension = Math.min(anchoNum, largoNum);
            }
            
            return (dimension * copiasNum / 100).toFixed(2); // Convertir cm a metros
        }

        function calculateM2(ancho, largo, copias) {
            if (!ancho || !largo || !copias) return 0;
            
            const anchoNum = parseFloat(ancho);
            const largoNum = parseFloat(largo);
            const copiasNum = parseInt(copias);
            
            return ((anchoNum * largoNum * copiasNum) / 10000).toFixed(2); // Convertir cm² a m²
        }

        async function loadData() {
            const spinner = document.getElementById('spinner');
            const refreshBtn = document.querySelector('.refresh-btn');
            
            try {
                spinner.style.display = 'inline-block';
                refreshBtn.classList.add('loading');
                
                // Construir parámetros de filtro
                const params = new URLSearchParams();
                params.append('dateFrom', document.getElementById('dateFrom').value);
                params.append('dateTo', document.getElementById('dateTo').value);
                
                const filenameFilter = document.getElementById('filenameFilter').value.trim();
                if (filenameFilter) {
                    params.append('filename', filenameFilter);
                    params.append('filenameLogic', document.querySelector('input[name="filenameLogic"]:checked').value);
                }
                
                const selectedPcs = Array.from(document.querySelectorAll('#pcFilter input[type="checkbox"]:checked:not(#selectAllPcs)')).map(cb => cb.value);
                if (selectedPcs.length > 0) {
                    params.append('pcs', selectedPcs.join(','));
                }
                
                const eventFilter = document.getElementById('eventFilter').value;
                if (eventFilter) {
                    params.append('event', eventFilter);
                }
                
                const response = await fetch(`api.php?${params.toString()}`);
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                
                const result = await response.json();
                console.log('Datos recibidos:', result);
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                allData = result.data || [];
                filteredData = [...allData]; // Los datos ya vienen filtrados del servidor
                
                // Actualizar lista de PCs si está disponible
                if (result.pcs_list) {
                    allPcs = result.pcs_list;
                    updatePcFilter();
                }
                
                // Actualizar estadísticas con datos del servidor
                updateStatsFromServer(result.stats || {});
                
                selectedRows.clear();
                currentPage = 1;
                updateTable();
                
                document.getElementById('lastUpdate').textContent = 
                    `Última actualización: ${new Date().toLocaleString()}`;
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tableContent').innerHTML = `
                    <div class="error">
                        ❌ Error al cargar los datos: ${error.message}
                        <br><br>
                        <button onclick="loadData()" class="refresh-btn">🔄 Reintentar</button>
                    </div>
                `;
            } finally {
                spinner.style.display = 'none';
                refreshBtn.classList.remove('loading');
            }
        }

        function updatePcFilter() {
            const pcFilter = document.getElementById('pcFilter');
            pcFilter.innerHTML = `
                <div class="multi-select-option">
                    <input type="checkbox" id="selectAllPcs" checked onchange="toggleAllPcs(this)">
                    <label for="selectAllPcs"><strong>Seleccionar Todas</strong></label>
                </div>
            ` + allPcs.map(pc => `
                <div class="multi-select-option">
                    <input type="checkbox" id="pc_${pc}" value="${pc}" checked onchange="applyFilters()">
                    <label for="pc_${pc}">${pc}</label>
                </div>
            `).join('');
        }

        function toggleAllPcs(checkbox) {
            const pcCheckboxes = document.querySelectorAll('#pcFilter input[type="checkbox"]:not(#selectAllPcs)');
            pcCheckboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
            applyFilters();
        }

        function selectRow(id) {
            if (selectedRows.has(id)) {
                selectedRows.delete(id);
            } else {
                selectedRows.add(id);
            }
            updateSelectedStats();
            updateTable();
        }

        function toggleSelectAll(checkbox) {
            const visibleRows = filteredData.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
            
            visibleRows.forEach(item => {
                if (checkbox.checked) {
                    selectedRows.add(item.id);
                } else {
                    selectedRows.delete(item.id);
                }
            });
            
            updateSelectedStats();
            updateTable();
        }

        function exportData(format) {
            const params = new URLSearchParams();
            params.append('format', format);
            
            // Si hay filas seleccionadas, exportar solo esas
            if (selectedRows.size > 0) {
                params.append('selected', Array.from(selectedRows).join(','));
            } else {
                // Si no hay selección, aplicar filtros actuales
                params.append('dateFrom', document.getElementById('dateFrom').value);
                params.append('dateTo', document.getElementById('dateTo').value);
                
                const filenameFilter = document.getElementById('filenameFilter').value.trim();
                if (filenameFilter) {
                    params.append('filename', filenameFilter);
                    params.append('filenameLogic', document.querySelector('input[name="filenameLogic"]:checked').value);
                }
                
                const selectedPcs = Array.from(document.querySelectorAll('#pcFilter input[type="checkbox"]:checked:not(#selectAllPcs)')).map(cb => cb.value);
                if (selectedPcs.length > 0) {
                    params.append('pcs', selectedPcs.join(','));
                }
                
                const eventFilter = document.getElementById('eventFilter').value;
                if (eventFilter) {
                    params.append('event', eventFilter);
                }
            }
            
            // Abrir en nueva ventana para descarga
            window.open(`export.php?${params.toString()}`, '_blank');
        }


        function updateStatsFromServer(stats) {
            document.getElementById('totalJobs').textContent = stats.total || 0;
            document.getElementById('ripJobs').textContent = stats.rip_count || 0;
            document.getElementById('printJobs').textContent = stats.print_count || 0;
            document.getElementById('mlTotal').textContent = stats.ml_total || 0;
            document.getElementById('m2Total').textContent = stats.m2_total || 0;
            document.getElementById('uniquePcs').textContent = stats.unique_pcs || 0;
            
            updateSelectedStats();
        }

        function applyFilters() {
            let data = [...allData];
            
            // Filtro de fechas
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            
            if (dateFrom && dateTo) {
                data = data.filter(item => {
                    const itemDate = item.fecha ? item.fecha.split(' ')[0] : '';
                    return itemDate >= dateFrom && itemDate <= dateTo;
                });
            }
            
            // Filtro de nombre de archivo
            const filenameFilter = document.getElementById('filenameFilter').value.trim();
            if (filenameFilter) {
                const terms = filenameFilter.split(',').map(term => term.trim().toLowerCase());
                const isAnd = document.getElementById('filenameAnd').checked;
                
                data = data.filter(item => {
                    const filename = (item.archivo || '').toLowerCase();
                    if (isAnd) {
                        return terms.every(term => filename.includes(term));
                    } else {
                        return terms.some(term => filename.includes(term));
                    }
                });
            }
            
            // Filtro de PCs
            const selectedPcs = Array.from(document.querySelectorAll('#pcFilter input[type="checkbox"]:checked:not(#selectAllPcs)')).map(cb => cb.value);
            if (selectedPcs.length > 0) {
                data = data.filter(item => selectedPcs.includes(item.pc_name));
            }
            
            // Filtro de evento
            const eventFilter = document.getElementById('eventFilter').value;
            if (eventFilter) {
                data = data.filter(item => item.evento === eventFilter);
            }
            
            filteredData = data;
            selectedRows.clear();
            currentPage = 1;
            updateStats();
            updateTable();
        }

        function updateStats() {
            const stats = {
                total: filteredData.length,
                rip_count: filteredData.filter(item => item.evento === 'RIP').length,
                print_count: filteredData.filter(item => item.evento === 'PRINT').length,
                ml_total: filteredData.reduce((sum, item) => sum + parseFloat(item.ml_total || 0), 0).toFixed(2),
                m2_total: filteredData.reduce((sum, item) => sum + parseFloat(item.m2_total || 0), 0).toFixed(2),
                unique_pcs: new Set(filteredData.map(item => item.pc_name).filter(pc => pc)).size
            };

            document.getElementById('totalJobs').textContent = stats.total;
            document.getElementById('ripJobs').textContent = stats.rip_count;
            document.getElementById('printJobs').textContent = stats.print_count;
            document.getElementById('mlTotal').textContent = stats.ml_total;
            document.getElementById('m2Total').textContent = stats.m2_total;
            document.getElementById('uniquePcs').textContent = stats.unique_pcs;
            
            updateSelectedStats();
        }


        function updateSelectedStats() {
            if (selectedRows.size === 0) {
                document.querySelectorAll('.stat-number-selected').forEach(el => {
                    el.style.display = 'none';
                });
                return;
            }
            
            const selectedData = filteredData.filter(item => selectedRows.has(item.id));
            
            const selectedStats = {
                total: selectedData.length,
                rip_count: selectedData.filter(item => item.evento === 'RIP').length,
                print_count: selectedData.filter(item => item.evento === 'PRINT').length,
                ml_total: selectedData.reduce((sum, item) => sum + parseFloat(item.ml_total || 0), 0).toFixed(2),
                m2_total: selectedData.reduce((sum, item) => sum + parseFloat(item.m2_total || 0), 0).toFixed(2),
                unique_pcs: new Set(selectedData.map(item => item.pc_name).filter(pc => pc)).size
            };

            document.getElementById('totalJobsSelected').textContent = `Selec: ${selectedStats.total}`;
            document.getElementById('ripJobsSelected').textContent = `Selec: ${selectedStats.rip_count}`;
            document.getElementById('printJobsSelected').textContent = `Selec: ${selectedStats.print_count}`;
            document.getElementById('mlTotalSelected').textContent = `Selec: ${selectedStats.ml_total}`;
            document.getElementById('m2TotalSelected').textContent = `Selec: ${selectedStats.m2_total}`;
            document.getElementById('uniquePcsSelected').textContent = `Selec: ${selectedStats.unique_pcs}`;
            
            document.querySelectorAll('.stat-number-selected').forEach(el => {
                el.style.display = 'block';
            });
        }

        // Función para actualizar la tabla - ESTA ES LA QUE FALTABA
        function updateTable() {
            const showSizeColumn = document.getElementById('showSizeColumn').checked;
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageData = filteredData.slice(startIndex, endIndex);
            
            if (pageData.length === 0) {
                document.getElementById('tableContent').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <h3>📭 No hay datos para mostrar</h3>
                        <p>Intenta ajustar los filtros o cambiar el rango de fechas</p>
                    </div>
                `;
                return;
            }

            const sizeColumnHeader = showSizeColumn ? '<th>Tamaño</th>' : '';
            
            let tableHTML = `
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" onchange="toggleSelectAll(this)" ${selectedRows.size > 0 && selectedRows.size === pageData.length ? 'checked' : ''}></th>
                            <th class="filename-col">Archivo</th>
                            <th class="event-col">Evento</th>
                            <th class="dimensions-col">Dimensiones</th>
                            <th class="copies-col">Copias</th>
                            <th class="ml-col">ML Total</th>
                            ${sizeColumnHeader}
                            <th class="pc-col">PC</th>
                            <th class="date-col">Fecha/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            pageData.forEach(item => {
                const isSelected = selectedRows.has(item.id);
                const statusClass = item.evento === 'RIP' ? 'status-rip' : 'status-print';
                const dimensions = item.ancho && item.largo ? `${item.ancho} × ${item.largo} cm` : '-';
                const mlTotal = item.ml_total ? parseFloat(item.ml_total).toFixed(2) : '0.00';
                const m2Total = item.m2_total ? parseFloat(item.m2_total).toFixed(2) : '0.00';
                const sizeColumn = showSizeColumn ? `<td><span class="ml-total">${m2Total} m²</span></td>` : '';
                
                tableHTML += `
                    <tr class="${isSelected ? 'selected' : ''}" onclick="selectRow('${item.id}')">
                        <td><input type="checkbox" class="row-checkbox" ${isSelected ? 'checked' : ''} onclick="event.stopPropagation()"></td>
                        <td class="filename-col">
                            <div class="file-name" title="${item.archivo || ''}">${item.archivo || '-'}</div>
                        </td>
                        <td><span class="${statusClass}">${item.evento}</span></td>
                        <td><span class="dimensions">${dimensions}</span></td>
                        <td>${item.copias || '-'}</td>
                        <td><span class="ml-total">${mlTotal} m</span></td>
                        ${sizeColumn}
                        <td><span class="pc-badge">${item.pc_name || '-'}</span></td>
                        <td>${item.fecha ? new Date(item.fecha).toLocaleString() : '-'}</td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                </table>
            `;

            // Agregar paginación
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (totalPages > 1) {
                tableHTML += `
                    <div class="pagination">
                        <button class="page-btn" onclick="changePage(1)" ${currentPage === 1 ? 'disabled' : ''}>« Primera</button>
                        <button class="page-btn" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>‹ Anterior</button>
                        
                        <span style="margin: 0 15px;">Página ${currentPage} de ${totalPages}</span>
                        
                        <button class="page-btn" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>Siguiente ›</button>
                        <button class="page-btn" onclick="changePage(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''}>Última »</button>
                    </div>
                `;
            }

            document.getElementById('tableContent').innerHTML = tableHTML;
        }

        // Función para cambiar de página
        function changePage(page) {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                updateTable();
            }
        }





    </script> 
    </body>  
</html> 

        