// este es el archivo funciones.js
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

    switch (range) {
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

        console.log('Cargando datos con fechas:', { dateFrom, dateTo }); // DEBUG

        params.append('dateFrom', dateFrom);
        params.append('dateTo', dateTo);

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





