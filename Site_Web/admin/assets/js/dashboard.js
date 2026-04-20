let currentPage = 1;
const itemsPerPage = 10;
let allDemandes = [];
let filteredDemandes = [];

document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    loadFilters();
    setupFilterListeners();
});

async function loadDashboardData() {
    try {
        const response = await fetch(API_URL + 'dashboard.php');
        const data = await response.json();
        
        if (data.success) {

            document.getElementById('totalDemandes').textContent = data.stats.total;
            document.getElementById('toAssign').textContent = data.stats.to_assign;
            document.getElementById('lateRequests').textContent = data.stats.late;

            renderTypeChart(data.type_distribution);
            renderStatusChart(data.status_distribution);

            allDemandes = data.demandes;
            filteredDemandes = [...allDemandes];
            renderDemandesTable();
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function renderTypeChart(data) {
    const ctx = document.getElementById('typeChart').getContext('2d');

    const colors = ['#0dd6ff', '#ff3eb5', '#34d399', '#8b5cf6', '#fbbf24', '#3b82f6'];
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.nom),
            datasets: [{
                data: data.map(d => d.count),
                backgroundColor: colors,
                borderRadius: 8,
                barThickness: 28
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { color: '#4b5563', font: { weight: '500' } }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#4b5563', font: { weight: '500' } }
                }
            }
        }
    });

    const legendContainer = document.getElementById('typeLegend');
    legendContainer.innerHTML = data.map((d, i) => `
        <div class="legend-item">
            <div class="legend-dot" style="background: ${colors[i % colors.length]}"></div>
            <span>${d.nom} ${d.percentage}%</span>
        </div>
    `).join('');
}

function renderStatusChart(data) {
    const ctx = document.getElementById('statusChart').getContext('2d');

    const colors = {
        'En attente': '#0dd6ff',
        'Validée': '#34d399',
        'Rejetée': '#ef4444',
        'En cours': '#ff3eb5'
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.statut),
            datasets: [{
                data: data.map(d => d.count),
                backgroundColor: data.map(d => colors[d.statut] || '#9ca3af'),
                borderWidth: 0,
                cutout: '72%',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });

    const legendContainer = document.getElementById('statusLegend');
    legendContainer.innerHTML = data.map(d => `
        <div class="legend-item" style="flex-direction: column; align-items: flex-start;">
            <div class="d-flex align-center gap-1">
                <div class="legend-dot" style="background: ${colors[d.statut] || '#9ca3af'}"></div>
                <span style="font-weight: 500;">${d.statut}</span>
            </div>
            <strong style="font-size: 20px; margin-left: 18px; color: #111;">${d.count}</strong>
            <small style="margin-left: 18px; color: #9ca3af;">${d.percentage}%</small>
        </div>
    `).join('');
}

async function loadFilters() {
    try {

        const typesResponse = await fetch(API_URL + 'types.php');
        const typesData = await typesResponse.json();
        
        if (typesData.success) {
            const typeSelect = document.getElementById('filterType');
            typesData.types.forEach(type => {
                const option = document.createElement('option');
                option.value = type.nom;
                option.textContent = type.nom;
                typeSelect.appendChild(option);
            });
        }

        const servicesResponse = await fetch(API_URL + 'services.php');
        const servicesData = await servicesResponse.json();
        
        if (servicesData.success) {
            const serviceSelect = document.getElementById('filterService');
            servicesData.services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.nom;
                option.textContent = service.nom;
                serviceSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading filters:', error);
    }
}

function setupFilterListeners() {
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    const filterService = document.getElementById('filterService');
    
    [searchInput, filterType, filterStatus, filterService].forEach(el => {
        if (el) {
            el.addEventListener('change', applyFilters);
            if (el.tagName === 'INPUT') {
                el.addEventListener('keyup', applyFilters);
            }
        }
    });
}

function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const type = document.getElementById('filterType').value;
    const status = document.getElementById('filterStatus').value;
    const service = document.getElementById('filterService').value;
    
    filteredDemandes = allDemandes.filter(d => {
        const matchSearch = !search || 
            d.demandeur_nom.toLowerCase().includes(search) ||
            d.description.toLowerCase().includes(search);
        const matchType = !type || d.type_nom === type;
        const matchStatus = !status || d.statut === status;
        const matchService = !service || d.service_nom === service;
        
        return matchSearch && matchType && matchStatus && matchService;
    });
    
    currentPage = 1;
    renderDemandesTable();
}

function renderDemandesTable() {
    const tbody = document.getElementById('demandesTableBody');
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = filteredDemandes.slice(start, end);
    
    tbody.innerHTML = pageData.map(d => `
        <tr>
            <td>${d.id}</td>
            <td>${d.demandeur_nom}</td>
            <td>${d.type_nom}</td>
            <td>${d.description}</td>
            <td>
                <select class="form-control form-select" style="width: 130px; padding: 5px 10px;" 
                        onchange="updateStatus(${d.id}, this.value)">
                    <option value="Validée" ${d.statut === 'Validée' ? 'selected' : ''}>Valider</option>
                    <option value="En attente" ${d.statut === 'En attente' ? 'selected' : ''}>En attente</option>
                    <option value="Rejetée" ${d.statut === 'Rejetée' ? 'selected' : ''}>Rejeter</option>
                </select>
            </td>
            <td>
                <span class="badge ${getServiceBadgeClass(d.service_nom || 'Non assigné')}">
                    ${d.service_nom || 'Non assigné'}
                </span>
            </td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="viewDetails(${d.id})">
                    Voir Details <i class="fas fa-chevron-right"></i>
                </button>
            </td>
        </tr>
    `).join('');
    
    renderPagination();
}

function renderPagination() {
    const totalPages = Math.ceil(filteredDemandes.length / itemsPerPage);
    const pagination = document.getElementById('pagination');
    
    let html = '';
    for (let i = 1; i <= Math.min(totalPages, 4); i++) {
        html += `<div class="pagination-item ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</div>`;
    }
    if (totalPages > 4) {
        html += `<div class="pagination-item">...</div>`;
    }
    
    pagination.innerHTML = html;
}

function goToPage(page) {
    currentPage = page;
    renderDemandesTable();
}

async function updateStatus(id, status) {
    try {
        const response = await fetch(API_URL + 'demandes.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, statut: status })
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Statut mis à jour avec succès');
            loadDashboardData();
        } else {
            showToast(data.message || 'Erreur lors de la mise à jour', 'danger');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showToast('Erreur lors de la mise à jour', 'danger');
    }
}

function viewDetails(id) {
    window.location.href = `demande-details.php?id=${id}`;
}
