document.addEventListener('DOMContentLoaded', function() {

    const menuToggle = document.getElementById('menuToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
    if (sidebarHidden && window.innerWidth > 992) {
        sidebar.classList.add('hidden');
        if (mainContent) mainContent.classList.add('sidebar-hidden');
    }
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
            if (mainContent) mainContent.classList.toggle('sidebar-hidden');

            localStorage.setItem('sidebarHidden', sidebar.classList.contains('hidden'));
        });
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    }

    const notificationBell = document.getElementById('notificationBell');
    const notificationsPanel = document.getElementById('notificationsPanel');
    
    if (notificationBell && notificationsPanel) {
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationsPanel.classList.toggle('show');

            const userDropdownMenu = document.getElementById('userDropdownMenu');
            if (userDropdownMenu) userDropdownMenu.classList.remove('show');
        });

        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target)) {
                notificationsPanel.classList.remove('show');
            }
        });
    }

    const userDropdownToggle = document.getElementById('userDropdownToggle');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    if (userDropdownToggle && userDropdownMenu) {
        userDropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('show');

            if (notificationsPanel) notificationsPanel.classList.remove('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!userDropdownToggle.contains(e.target)) {
                userDropdownMenu.classList.remove('show');
            }
        });
    }

    function handleResize() {
        if (window.innerWidth <= 992) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('open');
            if (mainContent) mainContent.classList.add('sidebar-hidden');
        } else {
            const savedState = localStorage.getItem('sidebarHidden') === 'true';
            if (!savedState) {
                sidebar.classList.remove('hidden');
                if (mainContent) mainContent.classList.remove('sidebar-hidden');
            }
        }
    }

    handleResize();

    window.addEventListener('resize', handleResize);

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {

                const form = searchInput.closest('form');
                if (form) {
                    form.submit();
                } else {
                    filterTable();
                }
            }, 500);
        });
    }

    function filterTable() {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const table = document.querySelector('.data-table tbody');
        
        if (!table) return;
        
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const typeValue = typeFilter ? typeFilter.value : '';
        const statusValue = statusFilter ? statusFilter.value : '';
        
        const rows = table.querySelectorAll('tr');
        
        rows.forEach(row => {
            const type = row.querySelector('td:nth-child(1)')?.textContent || '';
            const description = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            const status = row.querySelector('td:nth-child(3)')?.textContent || '';
            
            const matchesSearch = description.includes(searchTerm);
            const matchesType = !typeValue || type.includes(typeValue);
            const matchesStatus = !statusValue || status.includes(statusValue);
            
            row.style.display = matchesSearch && matchesType && matchesStatus ? '' : 'none';
        });
    }

    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    if (typeFilter) {
        typeFilter.addEventListener('change', filterTable);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }

    const fileInput = document.getElementById('piecesJointes');
    const fileList = document.getElementById('fileList');
    
    if (fileInput && fileList) {
        fileInput.addEventListener('change', function() {
            fileList.innerHTML = '';
            
            if (this.files.length > 0) {
                Array.from(this.files).forEach(file => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                        <i class="bi bi-paperclip"></i>
                        <span>${file.name}</span>
                        <span class="text-gray">(${(file.size / 1024).toFixed(2)} KB)</span>
                    `;
                    fileList.appendChild(fileItem);
                });
            }
        });
    }

    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;

            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    });

    const chartBars = document.querySelectorAll('.chart-bar');
    
    if (chartBars.length > 0) {

        chartBars.forEach(bar => {
            const targetHeight = bar.style.height;
            bar.style.height = '0';
            
            setTimeout(() => {
                bar.style.height = targetHeight;
            }, 100);
        });
    }

    const deleteButtons = document.querySelectorAll('[data-confirm]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || 'Êtes-vous sûr de vouloir effectuer cette action ?';
            
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 10);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    const flashMessage = document.querySelector('[data-flash-message]');
    if (flashMessage) {
        showToast(flashMessage.dataset.flashMessage, flashMessage.dataset.flashType || 'success');
    }
});

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}
