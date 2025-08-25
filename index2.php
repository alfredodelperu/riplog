<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Impresiones Full Color</title>
    <style>
        /* Estilos anteriores */
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
            max-width: 1400px;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

        .stat-label {
            color: #666;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
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

        .controls {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
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

        .filter-select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            background: white;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .filter-select:focus {
            border-color: #667eea;
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
        }

        th:first-child { border-radius: 10px 0 0 0; }
        th:last-child { border-radius: 0 10px 0 0; }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
            font-size: 0.9em;
        }

        tr:hover td {
            background-color: #f8f9ff;
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
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .dimensions {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8em;
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

        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7em;
            font-weight: bold;
            margin: 2px;
            display: inline-block;
        }

        .sync-yes {
            background: #d4edda;
            color: #155724;
        }

        .sync-no {
            background: #f8d7da;
            color: #721c24;
        }

        .seq-yes {
            background: #cce5ff;
            color: #004085;
        }

        .seq-no {
            background: #f0f0f0;
            color: #6c6c6c;
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
        /* ... (mantén los mismos estilos base) ... */

        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .filter-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: end;
            margin-bottom: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 150px;
        }

        .filter-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 0.9em;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9em;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: normal;
        }

        .selected-row {
            background-color: #e3f2fd !important;
        }

        .checkbox-col {
            width: 50px;
            text-align: center;
        }

        .export-section {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .export-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .export-excel {
            background-color: #217346;
            color: white;
        }

        .export-csv {
            background-color: #00a1f1;
            color: white;
        }

        .export-pdf {
            background-color: #f44336;
            color: white;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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
            <div class="stat-card"><div class="stat-number" id="totalJobs">-</div><div class="stat-label">Total Jobs</div></div>
            <div class="stat-card"><div class="stat-number" id="ripJobs">-</div><div class="stat-label">RIP Jobs</div></div>
            <div class="stat-card"><div class="stat-number" id="printJobs">-</div><div class="stat-label">Print Jobs</div></div>
            <div class="stat-card"><div class="stat-number" id="todayJobs">-</div><div class="stat-label">Hoy</div></div>
            <div class="stat-card"><div class="stat-number" id="totalCopies">-</div><div class="stat-label">Total Copias</div></div>
            <div class="stat-card"><div class="stat-number" id="uniquePcs">-</div><div class="stat-label">PCs Únicas</div></div>
        </div>

        <div class="filter-section">
            <h3>🔍 Filtros</h3>
            <div class="filter-row">
                <div class="filter-group">
                    <label>Fecha rápida</label>
                    <select id="dateFilter">
                        <option value="today">Hoy</option>
                        <option value="yesterday">Ayer</option>
                        <option value="thisWeek">Esta semana (lunes a domingo)</option>
                        <option value="lastWeek">Semana anterior</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>
                <div class="filter-group" id="customDateGroup" style="display: none;">
                    <label>Desde</label>
                    <input type="date" id="startDate">
                </div>
                <div class="filter-group" id="customDateGroupEnd" style="display: none;">
                    <label>Hasta</label>
                    <input type="date" id="endDate">
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label>Archivo</label>
                    <input type="text" id="fileFilter" placeholder="Buscar archivo...">
                    <div class="checkbox-group">
                        <input type="checkbox" id="fileAndOr">
                        <label for="fileAndOr">Buscar todos los términos (AND)</label>
                    </div>
                </div>
                <div class="filter-group">
                    <label>PC</label>
                    <select id="pcFilter" multiple size="3">
                        <!-- Se llenará dinámicamente -->
                    </select>
                </div>
                <div class="filter-group">
                    <label>Refrescar cada</label>
                    <select id="refreshInterval">
                        <option value="10000">10 segundos</option>
                        <option value="30000" selected>30 segundos</option>
                        <option value="60000">1 minuto</option>
                        <option value="120000">2 minutos</option>
                        <option value="300000">5 minutos</option>
                        <option value="0">Manual</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">📊 Registro de Actividad</h2>
                <div class="controls">
                    <select class="filter-select" id="eventFilter">
                        <option value="">Todos los eventos</option>
                        <option value="RIP">Solo RIP</option>
                        <option value="PRINT">Solo PRINT</option>
                    </select>
                    <button class="refresh-btn" onclick="loadData()">
                        <span class="spinner" id="spinner" style="display: none;"></span>
                        🔄 Actualizar
                    </button>
                </div>
            </div>

            <div id="tableContent">
                <div class="loading"><div class="spinner"></div> Cargando datos...</div>
            </div>

            <div class="export-section">
                <button class="export-btn export-excel" onclick="exportData('excel')">📊 Excel</button>
                <button class="export-btn export-csv" onclick="exportData('csv')">📄 CSV</button>
                <button class="export-btn export-pdf" onclick="exportData('pdf')">📑 PDF</button>
            </div>

            <div class="last-update" id="lastUpdate"></div>
        </div>
    </div>

    <!-- Librerías para exportación -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <script>
        let autoRefreshInterval;
        let allData = [];
        let filteredData = [];
        let selectedRows = new Set();
        let pcs = [];
        let currentPage = 1;
        const itemsPerPage = 20;

        function startAutoRefresh() {
            clearInterval(autoRefreshInterval);
            const interval = parseInt(document.getElementById('refreshInterval').value);
            if (interval > 0) {
                autoRefreshInterval = setInterval(loadData, interval);
            }
        }

        async function loadData() {
            const spinner = document.getElementById('spinner');
            try {
                spinner.style.display = 'inline-block';
                const response = await fetch('api.php');
                const result = await response.json();
                allData = result.data || [];
                pcs = [...new Set(allData.map(item => item.pc_name).filter(Boolean))];
                populatePcFilter();
                applyFilters();
                document.getElementById('lastUpdate').textContent = `Última actualización: ${new Date().toLocaleString()}`;
            } catch (error) {
                console.error(error);
                document.getElementById('tableContent').innerHTML = `<div class="error">❌ Error al cargar los datos</div>`;
            } finally {
                spinner.style.display = 'none';
            }
        }

        function populatePcFilter() {
            const select = document.getElementById('pcFilter');
            select.innerHTML = '';
            pcs.forEach(pc => {
                const option = document.createElement('option');
                option.value = pc;
                option.textContent = pc;
                select.appendChild(option);
            });
        }

        function applyFilters() {
            const dateFilter = document.getElementById('dateFilter').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const fileFilter = document.getElementById('fileFilter').value.toLowerCase();
            const fileAndOr = document.getElementById('fileAndOr').checked;
            const selectedPcs = Array.from(document.getElementById('pcFilter').selectedOptions).map(o => o.value);
            const eventFilter = document.getElementById('eventFilter').value;

            filteredData = allData.filter(item => {
                const itemDate = new Date(item.fecha);
                if (dateFilter !== 'custom') {
                    const now = new Date();
                    const startOfWeek = new Date(now);
                    const day = now.getDay();
                    startOfWeek.setDate(now.getDate() - day + (day === 0 ? -6 : 1));
                    startOfWeek.setHours(0, 0, 0, 0);
                    const endOfWeek = new Date(startOfWeek);
                    endOfWeek.setDate(startOfWeek.getDate() + 6);
                    endOfWeek.setHours(23, 59, 59, 999);

                    if (dateFilter === 'today') {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        return itemDate >= today;
                    } else if (dateFilter === 'yesterday') {
                        const yesterday = new Date();
                        yesterday.setDate(yesterday.getDate() - 1);
                        yesterday.setHours(0, 0, 0, 0);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        return itemDate >= yesterday && itemDate < today;
                    } else if (dateFilter === 'thisWeek') {
                        return itemDate >= startOfWeek && itemDate <= endOfWeek;
                    } else if (dateFilter === 'lastWeek') {
                        const lastWeekStart = new Date(startOfWeek);
                        lastWeekStart.setDate(startOfWeek.getDate() - 7);
                        const lastWeekEnd = new Date(endOfWeek);
                        lastWeekEnd.setDate(endOfWeek.getDate() - 7);
                        return itemDate >= lastWeekStart && itemDate <= lastWeekEnd;
                    }
                } else {
                    if (startDate && endDate) {
                        return itemDate >= new Date(startDate) && itemDate <= new Date(endDate);
                    }
                }

                return true;
            });

            if (fileFilter) {
                const terms = fileFilter.split(' ').filter(t => t);
                filteredData = filteredData.filter(item => {
                    const file = (item.archivo || '').toLowerCase();
                    if (fileAndOr) {
                        return terms.every(t => file.includes(t));
                    } else {
                        return terms.some(t => file.includes(t));
                    }
                });
            }

            if (selectedPcs.length > 0) {
                filteredData = filteredData.filter(item => selectedPcs.includes(item.pc_name));
            }

            if (eventFilter) {
                filteredData = filteredData.filter(item => item.evento === eventFilter);
            }

            currentPage = 1;
            updateTable();
        }

        function updateTable() {
            const startIndex = (currentPage - 1) * itemsPerPage;
            const pageData = filteredData.slice(startIndex, startIndex + itemsPerPage);

            const table = `
                <table>
                    <thead>
                        <tr>
                            <th class="checkbox-col"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                            <th>ID</th>
                            <th>Evento</th>
                            <th>Archivo</th>
                            <th>Tamaño</th>
                            <th>Dimensiones</th>
                            <th>Copias</th>
                            <th>PC</th>
                            <th>Fecha/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${pageData.map(item => `
                            <tr data-id="${item.id}" onclick="toggleRowSelection(${item.id})" class="${selectedRows.has(item.id) ? 'selected-row' : ''}">
                                <td class="checkbox-col"><input type="checkbox" class="row-checkbox" data-id="${item.id}" ${selectedRows.has(item.id) ? 'checked' : ''} onclick="event.stopPropagation()"></td>
                                <td>${item.id}</td>
                                <td><span class="status-${item.evento.toLowerCase()}">${item.evento}</span></td>
                                <td><span class="file-name" title="${item.archivo || 'N/A'}">${item.archivo || 'N/A'}</span></td>
                                <td>${item.tamano || 'N/A'}</td>
                                <td>${item.ancho && item.largo ? `<span class="dimensions">${item.ancho} × ${item.largo}</span>` : 'N/A'}</td>
                                <td>${item.copias || 1}</td>
                                <td>${item.pc_name ? `<span class="pc-badge">${item.pc_name}</span>` : 'N/A'}</td>
                                <td>${formatDate(item.fecha)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                ${createPagination()}
            `;

            document.getElementById('tableContent').innerHTML = table;
        }

        function toggleRowSelection(id) {
            if (selectedRows.has(id)) {
                selectedRows.delete(id);
            } else {
                selectedRows.add(id);
            }
            updateTable();
        }

        function toggleSelectAll(checkbox) {
            const ids = filteredData.map(item => item.id);
            if (checkbox.checked) {
                ids.forEach(id => selectedRows.add(id));
            } else {
                ids.forEach(id => selectedRows.delete(id));
            }
            updateTable();
        }

        function exportData(format) {
            const dataToExport = selectedRows.size > 0
                ? filteredData.filter(item => selectedRows.has(item.id))
                : filteredData;

            if (dataToExport.length === 0) {
                alert('No hay datos para exportar.');
                return;
            }

            const headers = ['ID', 'Evento', 'Archivo', 'Tamaño', 'Dimensiones', 'Copias', 'PC', 'Fecha/Hora'];
            const rows = dataToExport.map(item => [
                item.id,
                item.evento,
                item.archivo || 'N/A',
                item.tamano || 'N/A',
                item.ancho && item.largo ? `${item.ancho} × ${item.largo}` : 'N/A',
                item.copias || 1,
                item.pc_name || 'N/A',
                formatDate(item.fecha)
            ]);

            if (format === 'excel') {
                const ws = XLSX.utils.aoa_to_sheet([headers, ...rows]);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Datos');
                XLSX.writeFile(wb, 'dashboard_impresiones.xlsx');
            } else if (format === 'csv') {
                const csvContent = [headers, ...rows].map(r => r.join(',')).join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'dashboard_impresiones.csv';
                link.click();
            } else if (format === 'pdf') {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                doc.setFontSize(12);
                doc.text('Registro de Actividad - Impresiones Full Color', 14, 16);
                doc.autoTable({
                    startY: 22,
                    head: [headers],
                    body: rows,
                });
                doc.save('dashboard_impresiones.pdf');
            }
        }

        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            return new Date(dateStr).toLocaleString('es-ES');
        }

        function createPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (totalPages <= 1) return '';
            let pagination = '<div class="pagination">';
            if (currentPage > 1) {
                pagination += `<button class="page-btn" onclick="changePage(${currentPage - 1})">‹ Anterior</button>`;
            }
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                pagination += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
            }
            if (currentPage < totalPages) {
                pagination += `<button class="page-btn" onclick="changePage(${currentPage + 1})">Siguiente ›</button>`;
            }
            pagination += '</div>';
            return pagination;
        }

        function changePage(page) {
            currentPage = page;
            updateTable();
        }

        // Event listeners
        document.getElementById('dateFilter').addEventListener('change', function () {
            const isCustom = this.value === 'custom';
            document.getElementById('customDateGroup').style.display = isCustom ? 'block' : 'none';
            document.getElementById('customDateGroupEnd').style.display = isCustom ? 'block' : 'none';
            applyFilters();
        });

        ['startDate', 'endDate', 'fileFilter', 'fileAndOr', 'pcFilter', 'eventFilter'].forEach(id => {
            document.getElementById(id).addEventListener('change', applyFilters);
        });

        document.getElementById('refreshInterval').addEventListener('change', startAutoRefresh);

        // Inicializar
        loadData();
        startAutoRefresh();
    </script>
</body>
</html>