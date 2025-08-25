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
                <div class="stat-label">Total Jobs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="ripJobs">-</div>
                <div class="stat-label">RIP Jobs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="printJobs">-</div>
                <div class="stat-label">Print Jobs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="todayJobs">-</div>
                <div class="stat-label">Hoy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalCopies">-</div>
                <div class="stat-label">Total Copias</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="uniquePcs">-</div>
                <div class="stat-label">PCs Únicas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="syncCount">-</div>
                <div class="stat-label">Sincronizados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="seqCount">-</div>
                <div class="stat-label">Secuenciados</div>
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
        let currentPage = 1;
        const itemsPerPage = 20;
        
        async function loadData() {
            const spinner = document.getElementById('spinner');
            const refreshBtn = document.querySelector('.refresh-btn');
            
            try {
                spinner.style.display = 'inline-block';
                refreshBtn.classList.add('loading');
                
                const response = await fetch('api.php');
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                
                const result = await response.json();
                console.log('Datos recibidos:', result);
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                allData = result.data || [];
                updateStats(result.stats || {});
                applyFilter();
                
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

        function updateStats(stats) {
            document.getElementById('totalJobs').textContent = stats.total || 0;
            document.getElementById('ripJobs').textContent = stats.rip_count || 0;
            document.getElementById('printJobs').textContent = stats.print_count || 0;
            document.getElementById('todayJobs').textContent = stats.today_count || 0;
            document.getElementById('totalCopies').textContent = stats.total_copies || 0;
            document.getElementById('uniquePcs').textContent = stats.unique_pcs || 0;
            document.getElementById('syncCount').textContent = stats.synchronized_count || 0;
            document.getElementById('seqCount').textContent = stats.sequenced_count || 0;
        }

        function applyFilter() {
            const filter = document.getElementById('eventFilter').value;
            filteredData = filter ? allData.filter(item => item.evento === filter) : allData;
            currentPage = 1;
            updateTable();
        }

        function updateTable() {
            if (!filteredData || filteredData.length === 0) {
                document.getElementById('tableContent').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #666;">
                        📭 No hay datos para mostrar
                    </div>
                `;
                return;
            }

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageData = filteredData.slice(startIndex, endIndex);

            const table = `
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Evento</th>
                            <th>Archivo</th>
                            <th>Tamaño</th>
                            <th>Dimensiones</th>
                            <th>Copias</th>
                            <th>PC</th>
                            <th>Estado</th>
                            <th>Fecha/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${pageData.map(item => `
                            <tr>
                                <td>${item.id}</td>
                                <td>
                                    <span class="status-${item.evento.toLowerCase()}">
                                        ${item.evento}
                                    </span>
                                </td>
                                <td>
                                    <span class="file-name" title="${item.archivo || 'N/A'}">
                                        ${item.archivo || 'N/A'}
                                    </span>
                                </td>
                                <td>${item.tamano || 'N/A'}</td>
                                <td>
                                    ${item.ancho && item.largo ? 
                                        `<span class="dimensions">${item.ancho} × ${item.largo}</span>` : 'N/A'
                                    }
                                </td>
                                <td>${item.copias || 1}</td>
                                <td>
                                    ${item.pc_name ? 
                                        `<span class="pc-badge">${item.pc_name}</span>` : 'N/A'
                                    }
                                </td>
                                <td>
                                    <span class="status-badge ${item.sincronizado ? 'sync-yes' : 'sync-no'}">
                                        ${item.sincronizado ? '✓ Sync' : '✗ Sync'}
                                    </span>
                                    <span class="status-badge ${item.secuenciado ? 'seq-yes' : 'seq-no'}">
                                        ${item.secuenciado ? '✓ Seq' : '✗ Seq'}
                                    </span>
                                </td>
                                <td>
                                    ${formatDate(item.fecha)}
                                    ${item.hora ? `<br><small>${item.hora}</small>` : ''}
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                ${createPagination()}
            `;

            document.getElementById('tableContent').innerHTML = table;
        }

        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr);
            return date.toLocaleString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
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
        document.getElementById('eventFilter').addEventListener('change', applyFilter);

        document.getElementById('autoRefresh').addEventListener('change', function() {
            if (this.checked) {
                autoRefreshInterval = setInterval(loadData, 30000);
            } else {
                clearInterval(autoRefreshInterval);
            }
        });

        // Inicializar datos
        loadData();
        autoRefreshInterval = setInterval(loadData, 30000);
    </script>
</body>
</html>