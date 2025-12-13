document.addEventListener('DOMContentLoaded', function() {

    initSidebarToggle();

    initNotificationPanel();

    initUserDropdown();

    initStatusDropdowns();
});

function initSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (mainContent) {
                mainContent.classList.toggle('expanded');
            }
        });
    }
}

function initNotificationPanel() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationsPanel = document.getElementById('notificationsPanel');
    
    if (notificationBell && notificationsPanel) {
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationsPanel.classList.toggle('show');

            const userDropdown = document.querySelector('.user-dropdown');
            if (userDropdown) {
                userDropdown.classList.remove('show');
            }
        });

        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target)) {
                notificationsPanel.classList.remove('show');
            }
        });
    }
}

function initUserDropdown() {
    const userDropdownToggle = document.getElementById('userDropdownToggle');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userDropdownToggle && userDropdown) {
        userDropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');

            const notificationsPanel = document.getElementById('notificationsPanel');
            if (notificationsPanel) {
                notificationsPanel.classList.remove('show');
            }
        });

        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }
}

function markAllAsRead() {
    fetch('api/notifications.php?action=mark_all_read', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            document.querySelectorAll('.notification-dot.unread').forEach(dot => {
                dot.classList.remove('unread');
            });
            const badge = document.getElementById('notificationCount');
            if (badge) badge.style.display = 'none';
        }
    })
    .catch(err => console.error('Error:', err));
}

function initStatusDropdowns() {
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            updateStatusClass(this);
        });
    });
}

function updateStatusClass(select) {

    select.classList.remove('status-valider', 'status-en-attente', 'status-rejeter', 'status-traitée');

    const status = select.value.toLowerCase().replace(' ', '-');
    select.classList.add('status-' + status);
}

function updateStatus(selectElement) {
    const demandeId = selectElement.getAttribute('data-demande-id');
    const newStatus = selectElement.value;

    updateStatusClass(selectElement);

    const formData = new FormData();
    formData.append('demande_id', demandeId);
    formData.append('status', newStatus);
    
    fetch('update_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to update status:', data.error);
            alert('Erreur lors de la mise à jour du statut');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur de connexion');
    });
}

function initCharts(demandesTraitees, demandesRejetees, demandesNonTraitees) {

    const statsCtx = document.getElementById('statsChart');
    if (statsCtx) {
        new Chart(statsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Validées', 'Rejetées'],
                datasets: [{
                    data: [demandesTraitees, demandesRejetees],
                    backgroundColor: [
                        '#3b82f6', // Blue
                        '#f97316'  // Orange
                    ],
                    borderWidth: 0,
                    cutout: '65%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleColor: '#ffffff',
                        bodyColor: '#a0a0a0',
                        borderColor: '#333333',
                        borderWidth: 1
                    }
                }
            }
        });
    }

    const donutCtx = document.getElementById('donutChart');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'Expirées'],
                datasets: [{
                    data: [demandesNonTraitees, 0],
                    backgroundColor: [
                        '#06b6d4', // Cyan
                        '#666666'  // Gray
                    ],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleColor: '#ffffff',
                        bodyColor: '#a0a0a0',
                        borderColor: '#333333',
                        borderWidth: 1
                    }
                }
            }
        });
    }
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#ef4444';
            isValid = false;
        } else {
            field.style.borderColor = '#333333';
        }
    });
    
    return isValid;
}

function confirmAction(message) {
    return confirm(message);
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    const filter = input.value.toLowerCase();
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}
