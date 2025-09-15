// funciones.js CORREGIDO
let autoRefreshInterval;
let allData = [];
let filteredData = [];
let selectedRows = new Set();
let allPcs = [];
let currentPage = 1;
const itemsPerPage = 20;
let isLoadingData = false; // Prevenir múltiples cargas simultáneas

// Inicializar fechas por defecto (hoy)
function initializeDates() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateFrom').value = today;
    document.getElementById('dateTo').value = today;
    console.log('✅ Fechas inicializadas:', today);
}

// CORREGIDO: setDateRange ahora crea nuevas instancias de Date
function setDateRange(range) {
    console.log('🗓️ Estableciendo rango de fecha:', range);
    
    // Crear nueva instancia para evitar modificar el objeto original
    const today = new Date();
    let startDate, endDate;

    // Remover clase active de todos los botones
    document.querySelectorAll('.date-shortcut').forEach(btn => btn.classList.remove('active'));
    
    switch(range) {
        case 'today':
            startDate = new Date(today);
            endDate = new Date(today);
            break;
            
        case 'yesterday':
            startDate = new Date(today.getTime() - 24 * 60 * 60 * 1000);
            endDate = new Date(today.getTime() - 24 * 60 * 60 * 1000);
            break;
            
        case 'thisWeek':
            // Crear nuevas instancias para evitar modificar today
            const todayCopy = new Date(today);
            const dayOfWeek = todayCopy.getDay();
            const daysFromMonday = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // Lunes como primer día
            startDate = new Date(todayCopy.getTime() - (daysFromMonday * 24 * 60 * 60 * 1000));
            endDate = new Date(today);
            break;
            
        case 'lastWeek':
            const todayCopy2 = new Date(today);
            const dayOfWeek2 = todayCopy2.getDay();
            const daysFromMonday2 = dayOfWeek2 === 0 ? 6 : dayOfWeek2 - 1;
            // Fin de la semana pasada (domingo)
            endDate = new Date(todayCopy2.getTime() - (daysFromMonday2 + 1) * 24 * 60 * 60 * 1000);
            // Inicio de la semana pasada (lunes)
            startDate = new Date(endDate.getTime() - 6 * 24 * 60 * 60 * 1000);
            break;
            
        case 'thisMonth':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today);
            break;
            
        case 'lastMonth':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
    }

    const dateFromStr = startDate.toISOString().split('T')[0];
    const dateToStr = endDate.toISOString().split('T')[0];
    
    document.getElementById('dateFrom').value = dateFromStr;
    document.getElementById('dateTo').value = dateToStr;
    
    console.log('📅 Fechas establecidas:', { from: dateFromStr, to: dateToStr });
    
    // Marcar botón como activo - CORREGIDO: usar event.target si existe
    if (typeof event !== 'undefined' && event.target) {
        event.target.classList.add('active');
    } else {
        // Si se llama programáticamente, encontrar el botón correcto
        document.querySelector(`[onclick="setDateRange('${range}')"]`)?.classList.add('active');
    }
    
    loadData();
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
    
    return (dimension * copiasNum / 100).toFixed(2);
}

function calculateM2(ancho, largo, copias) {
    if (!ancho || !largo || !copias) return 0;
    
    const anchoNum = parseFloat(ancho);
    const largoNum = parseFloat(largo);
    const copiasNum = parseInt(copias);
    
    return ((anchoNum * largoNum * copiasNum) / 10000).toFixed(2);
}

// CORREGIDO: loadData con mejor manejo de errores y debugging
async function loadData() {
    if (isLoadingData) {
        console.log('⚠️ Ya hay una carga en progreso, ignorando...');
        return;
    }
    
    isLoadingData = true;
    const spinner = document.getElementById('spinner');
    const refreshBtn = document.querySelector('.refresh-btn');
    
    try {
        spinner.style.display = 'inline-block';
        refreshBtn.classList.add('loading');
        
        // Construir parámetros de filtro
        const params = new URLSearchParams();
        
        const dateFromValue = document.getElementById('dateFrom').value;
        const dateToValue = document.getElementById('dateTo').value;
        
        console.log('📊 Cargando datos con filtros:', {
            dateFrom: dateFromValue, 
            dateTo: dateToValue
        });
        
        if (dateFromValue) params.append('dateFrom', dateFromValue);
        if (dateToValue) params.append('dateTo', dateToValue);
        
        // CORREGIDO: Filtro de nombre de archivo mejorado
        const filenameFilter = document.getElementById('filenameFilter').value.trim();
        if (filenameFilter) {
            params.append('filename', filenameFilter);
            const filenameLogic = document.querySelector('input[name="filenameLogic"]:checked')?.value || 'or';
            params.append('filenameLogic', filenameLogic);
            console.log('🔍 Filtro de archivo:', { filename: filenameFilter, logic: filenameLogic });
        }
        
        // CORREGIDO: Filtro de PCs mejorado
        const selectedPcs = Array.from(document.querySelectorAll('#pcFilter input[type="checkbox"]:checked:not(#selectAllPcs)')).map(cb => cb.value);
        const allPcsChecked = document.getElementById('selectAllPcs')?.checked;
        
        console.log('💻 Estado PCs:', { 
            allChecked: allPcsChecked, 
            selected: selectedPcs.length,
            total: allPcs.length 
        });
        
        // Solo enviar filtro de PCs si no están todas seleccionadas
        if (!allPcsChecked && selectedPcs.length > 0) {
            params.append('pcs', selectedPcs.join(','));
            console.log('💻 Filtro PCs aplicado:', selectedPcs);
        }
        
        const eventFilter = document.getElementById('eventFilter').value;
        if (eventFilter) {
            params.append('event', eventFilter);
            console.log('🎯 Filtro evento:', eventFilter);
        }
        
        const apiUrl = `api.php?${params.toString()}`;
        console.log('🌐 URL API:', apiUrl);
        
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📦 Respuesta recibida:', {
            totalRecords: result.data?.length || 0,
            stats: result.stats,
            pcsCount: result.pcs_list?.length || 0
        });
        
        if (result.error) {
            throw new Error(result.error);
        }
        
        allData = result.data || [];
        filteredData = [...allData];
        
        // Actualizar lista de PCs SOLO si viene del servidor y hay cambios
        if (result.pcs_list && JSON.stringify(result.pcs_list) !== JSON.stringify(allPcs)) {
            console.log('🔄 Actualizando lista de PCs');
            allPcs = result.pcs_list;
            updatePcFilter();
        }
        
        // Actualizar estadísticas
        updateStatsFromServer(result.stats || {});
        
        selectedRows.clear();
        currentPage = 1;
        updateTable();
        
        document.getElementById('lastUpdate').textContent = 
            `Última actualización: ${new Date().toLocaleString()}`;
            
        console.log('✅ Datos cargados exitosamente');
        
    } catch (error) {
        console.error('❌ Error al cargar datos:', error);
        document.getElementById('tableContent').innerHTML = `
            <div class="error" style="text-align: center; padding: 40px; color: #dc3545;">
                <h3>❌ Error al cargar los datos</h3>
                <p style="margin: 15px 0;">${error.message}</p>
                <button onclick="loadData()" class="refresh-btn" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    🔄 Reintentar
                </button>
            </div>
        `;
    } finally {
        spinner.style.display = 'none';
        refreshBtn.classList.remove('loading');
        isLoadingData = false;
    }
}

// CORREGIDO: updatePcFilter sin auto-reload
function updatePcFilter() {
    const pcFilter = document.getElementById('pcFilter');
    
    console.log('🔧 Actualizando filtro de PCs:', allPcs.length);
    
    let html = `
        <div class="multi-select-option pc-option">
            <input type="checkbox" id="selectAllPcs" checked onchange="toggleAllPcs(this)">
            <label for="selectAllPcs"><strong>Seleccionar Todas (${allPcs.length})</strong></label>
        </div>
    `;
    
    html += allPcs.map(pc => `
        <div class="multi-select-option pc-option">
            <input type="checkbox" id="pc_${pc.replace(/[^a-zA-Z0-9]/g, '_')}" value="${pc}" checked onchange="onPcChange(this)">
            <label for="pc_${pc.replace(/[^a-zA-Z0-9]/g, '_')}">${pc}</label>
        </div>
    `).join('');
    
    pcFilter.innerHTML = html;
}

// NUEVA: Función para manejar cambios individuales de PC
function onPcChange(checkbox) {
    console.log('💻 PC cambiada:', checkbox.value, checkbox.checked);
    
    // Actualizar estado del "Seleccionar Todas"
    updateSelectAllPcsState();
    
    // Cargar datos con debounce
    debounceLoadData();
}

// NUEVA: Actualizar estado del checkbox "Seleccionar Todas"
function updateSelectAllPcsState() {
    const selectAllPcs = document.getElementById('selectAllPcs');
    const pcCheckboxes = document.querySelectorAll('#pcFilter input[type="checkbox"]:not(#selectAllPcs)');
    const checkedPcs = document.querySelectorAll('#pcFilter input[type="checkbox"]:not(#selectAllPcs):checked');
    
    if (checkedPcs.length === 0) {
        selectAllPcs.indeterminate = false;
        selectAllPcs.checked = false;
    } else if (checkedPcs.length === pcCheckboxes.length) {
        selectAllPcs.indeterminate = false;
        selectAllPcs.checked = true;
    } else {
        selectAllPcs.indeterminate = true;
        selectAllPcs.checked = false;
    }
}

// CORREGIDO: toggleAllPcs sin auto-reload inmediato
function toggleAllPcs(checkbox) {
    console.log('🔄 Toggle todas las PCs:', checkbox.checked);
    
    const pcCheckboxes = document.querySelectorAll('#pcFilter input[type="checkbox"]:not(#selectAllPcs)');
    pcCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    
    // Cargar datos con debounce para evitar múltiples llamadas
    debounceLoadData();
}

// NUEVA: Debounce para evitar múltiples llamadas a loadData
let loadDataTimeout;
function debounceLoadData() {
    clearTimeout(loadDataTimeout);
    loadDataTimeout = setTimeout(() => {
        loadData();
    }, 300); // 300ms de delay
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
        const dateFromValue = document.getElementById('dateFrom').value;
        const dateToValue = document.getElementById('dateTo').value;
        
        if (dateFromValue) params.append('dateFrom', dateFromValue);
        if (dateToValue) params.append('dateTo', dateToValue);
        
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

// ELIMINADA: applyFilters() ya no es necesaria - todo se maneja en loadData()

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

// CORREGIDA: updateTable con mejor manejo de estilos
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
                <small>Total de registros en BD: ${allData.length}</small>
            </div>
        `;
        return;
    }

    const sizeColumnHeader = showSizeColumn ? '<th>Tamaño (m²)</th>' : '';
    
    let tableHTML = `
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 12px; text-align: left;">
                            <input type="checkbox" onchange="toggleSelectAll(this)" 
                                   ${selectedRows.size > 0 && selectedRows.size === pageData.length ? 'checked' : ''}>
                        </th>
                        <th style="padding: 12px; text-align: left;">Archivo</th>
                        <th style="padding: 12px; text-align: left;">Evento</th>
                        <th style="padding: 12px; text-align: left;">Dimensiones</th>
                        <th style="padding: 12px; text-align: left;">Copias</th>
                        <th style="padding: 12px; text-align: left;">ML Total</th>
                        ${sizeColumnHeader}
                        <th style="padding: 12px; text-align: left;">PC</th>
                        <th style="padding: 12px; text-align: left;">Fecha/Hora</th>
                    </tr>
                </thead>
                <tbody>
    `;

    pageData.forEach(item => {
        const isSelected = selectedRows.has(item.id);
        const statusClass = item.evento === 'RIP' ? 'rip-event' : 'print-event';
        const dimensions = item.ancho && item.largo ? `${item.ancho} × ${item.largo} cm` : '-';
        const mlTotal = item.ml_total ? parseFloat(item.ml_total).toFixed(2) : '0.00';
        const m2Total = item.m2_total ? parseFloat(item.m2_total).toFixed(2) : '0.00';
        const sizeColumn = showSizeColumn ? `<td style="padding: 12px;">${m2Total}</td>` : '';
        
        const rowStyle = isSelected ? 'background-color: #e3f2fd;' : '';
        
        tableHTML += `
            <tr style="border-bottom: 1px solid #eee; cursor: pointer; ${rowStyle}" 
                onclick="selectRow('${item.id}')"
                onmouseover="this.style.backgroundColor='#f8f9fa'" 
                onmouseout="this.style.backgroundColor='${isSelected ? '#e3f2fd' : 'transparent'}'">
                <td style="padding: 12px;">
                    <input type="checkbox" ${isSelected ? 'checked' : ''} 
                           onclick="event.stopPropagation()" onchange="selectRow('${item.id}')">
                </td>
                <td style="padding: 12px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                    title="${item.archivo || ''}">
                    ${item.archivo || '-'}
                </td>
                <td style="padding: 12px;">
                    <span class="${statusClass}" style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                          background: ${item.evento === 'RIP' ? '#e3f2fd' : '#f3e5f5'}; 
                          color: ${item.evento === 'RIP' ? '#1976d2' : '#7b1fa2'};">
                        ${item.evento}
                    </span>
                </td>
                <td style="padding: 12px;">${dimensions}</td>
                <td style="padding: 12px;">${item.copias || 1}</td>
                <td style="padding: 12px;">${mlTotal} m</td>
                ${sizeColumn}
                <td style="padding: 12px;">
                    <span style="background: #e8f5e8; color: #2e7d32; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                        ${item.pc_name || '-'}
                    </span>
                </td>
                <td style="padding: 12px; font-size: 12px;">
                    ${item.fecha ? new Date(item.fecha).toLocaleString('es-ES') : '-'}
                </td>
            </tr>
        `;
    });

    tableHTML += `
                </tbody>
            </table>
        </div>
    `;

    // Paginación
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (totalPages > 1) {
        tableHTML += `
            <div class="pagination" style="display: flex; justify-content: center; align-items: center; padding: 20px; gap: 10px;">
                <button onclick="changePage(1)" ${currentPage === 1 ? 'disabled' : ''} 
                        style="padding: 8px 12px; border: 1px solid #ddd; background: white; cursor: pointer; border-radius: 4px;">
                    « Primera
                </button>
                <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}
                        style="padding: 8px 12px; border: 1px solid #ddd; background: white; cursor: pointer; border-radius: 4px;">
                    ‹ Anterior
                </button>
                
                <span style="margin: 0 15px; font-weight: bold;">
                    Página ${currentPage} de ${totalPages} 
                    <small>(${filteredData.length} registros)</small>
                </span>
                
                <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}
                        style="padding: 8px 12px; border: 1px solid #ddd; background: white; cursor: pointer; border-radius: 4px;">
                    Siguiente ›
                </button>
                <button onclick="changePage(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''}
                        style="padding: 8px 12px; border: 1px solid #ddd; background: white; cursor: pointer; border-radius: 4px;">
                    Última »
                </button>
            </div>
        `;
    }

    document.getElementById('tableContent').innerHTML = tableHTML;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        updateTable();
    }
}