const API_URL = 'api/';

function ensureModalRoot() {
    let root = document.getElementById('modalRoot');
    if (!root) {
        root = document.createElement('div');
        root.id = 'modalRoot';
        document.body.appendChild(root);
    }
    return root;
}

function mountModalsToRoot() {
    const root = ensureModalRoot();
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(modal => {
        if (modal.parentElement !== root) {
            root.appendChild(modal);
        }
    });
}

document.addEventListener('DOMContentLoaded', mountModalsToRoot);

document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
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
});

document.addEventListener('DOMContentLoaded', function() {
    const userDropdownToggle = document.getElementById('userDropdownToggle');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    if (userDropdownToggle && userDropdownMenu) {
        userDropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('show');

            const notificationsPanel = document.getElementById('notificationsPanel');
            if (notificationsPanel) notificationsPanel.classList.remove('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!userDropdownToggle.contains(e.target)) {
                userDropdownMenu.classList.remove('show');
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
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

    loadNotifications();
});

async function loadNotifications() {
    try {
        const response = await fetch(API_URL + 'notifications.php');
        const data = await response.json();
        
        if (data.success) {
            renderNotifications(data.notifications);
            updateNotificationCount(data.unread_count);
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

function renderNotifications(notifications) {
    const container = document.getElementById('notificationsList');
    if (!container) return;
    
    container.innerHTML = notifications.map(notif => `
        <div class="notification-item ${notif.lu ? '' : 'unread'}">
            <div class="notification-dot ${notif.lu ? '' : 'unread'}"></div>
            <div class="notification-content">
                <span class="notification-id">ID: ${notif.id}</span>
                <span class="notification-time">${notif.time_ago}</span>
                <p class="notification-text">${notif.message}</p>
            </div>
        </div>
    `).join('');
}

function updateNotificationCount(count) {
    const badge = document.getElementById('notificationCount');
    const notifBadge = document.getElementById('notifBadge');
    
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
    if (notifBadge) {
        notifBadge.textContent = count;
    }
}

async function markAllAsRead() {
    try {
        const response = await fetch(API_URL + 'notifications.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'mark_all_read' })
        });
        const data = await response.json();
        
        if (data.success) {
            loadNotifications();
        }
    } catch (error) {
        console.error('Error marking notifications as read:', error);
    }
}

function openAccountModal() {
    const modal = document.getElementById('accountModal');
    if (modal) {
        modal.classList.add('show');
        loadUserData();
    }
}

function closeAccountModal() {
    const modal = document.getElementById('accountModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

async function loadUserData() {
    try {
        const response = await fetch(API_URL + 'user.php?action=current');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('accountName').value = data.user.nom_complet;
            document.getElementById('accountEmail').value = data.user.email;
            document.getElementById('accountRole').value = data.user.role;
        }
    } catch (error) {
        console.error('Error loading user data:', error);
    }
}

async function saveAccountChanges() {
    const name = document.getElementById('accountName').value;
    const email = document.getElementById('accountEmail').value;
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (!name || !email) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }
    
    if (newPassword && newPassword !== confirmPassword) {
        alert('Les mots de passe ne correspondent pas');
        return;
    }
    
    if (newPassword && newPassword.length < 6) {
        alert('Le mot de passe doit contenir au moins 6 caractères');
        return;
    }
    
    try {
        const response = await fetch(API_URL + 'user.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                nom_complet: name,
                email: email,
                current_password: currentPassword,
                new_password: newPassword
            })
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Modifications enregistrées avec succès');
            closeAccountModal();

            const nameEl = document.getElementById('currentUserName');
            if (nameEl) nameEl.textContent = name;
        } else {
            alert(data.message || 'Erreur lors de la mise à jour');
        }
    } catch (error) {
        console.error('Error saving account changes:', error);
        alert('Erreur lors de la sauvegarde');
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    const intervals = {
        année: 31536000,
        mois: 2592000,
        semaine: 604800,
        jour: 86400,
        heure: 3600,
        minute: 60
    };
    
    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return interval + ' ' + unit + (interval > 1 && unit !== 'mois' ? 's' : '');
        }
    }
    return 'À l\'instant';
}

function getStatusBadgeClass(status) {
    const classes = {
        'En attente': 'badge-warning',
        'Validée': 'badge-success',
        'Rejetée': 'badge-danger',
        'En cours': 'badge-info'
    };
    return classes[status] || 'badge-secondary';
}

function getRoleBadgeClass(role) {
    const classes = {
        'Administrateur': 'badge-danger',
        'Validateur': 'badge-info',
        'Demandeur': 'badge-success'
    };
    return classes[role] || 'badge-secondary';
}

function getServiceBadgeClass(service) {
    const classes = {
        'Support IT': 'badge-info',
        'Agent IT': 'badge-success',
        'Service RH': 'badge-danger',
        'Service Finance': 'badge-warning',
        'Service Logistique': 'badge-purple'
    };
    return classes[service] || 'badge-secondary';
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'times-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
