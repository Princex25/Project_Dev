<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #000 0%, #333 100%);
            border-radius: var(--border-radius-lg);
            padding: 35px;
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 30px;
            color: #fff;
        }
        
        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
        }
        
        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .profile-role {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .profile-info-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 45px;
            height: 45px;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid var(--accent-cyan);
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-cyan);
            font-size: 18px;
        }
        
        .info-icon.green {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--accent-green);
            color: var(--accent-green);
        }
        
        .info-icon.blue {
            background: rgba(59, 130, 246, 0.1);
            border-color: var(--accent-blue);
            color: var(--accent-blue);
        }
        
        .info-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 500;
        }
        
        .info-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }
        
        .stats-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
        }
        
        .stats-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        
        .stats-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--accent-cyan);
        }
    </style>
</head>
<body>
    <div class="app-container">
<button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
<aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="logo">🏠</span>
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="menu-item">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="users.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Gestion Utilisateurs</span>
                </a>
                <a href="types.php" class="menu-item">
                    <i class="fas fa-tags"></i>
                    <span>Gérer Types</span>
                </a>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </nav>
        </aside>
<main class="main-content">
<header class="header">
                <div class="header-actions">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Retour au Dashboard
                    </a>
                </div>
                <div class="header-title text-center">
                    <h1>Profil Utilisateur</h1>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openEditModal()">
                        <i class="fas fa-edit"></i> Modifier le Profil
                    </button>
                </div>
            </header>
<div class="profile-header">
                <img src="assets/images/avatar.png" alt="Avatar" class="profile-avatar-large" 
                     onerror="this.src='https://ui-avatars.com/api/?name=Ahmed&background=00d4ff&color=fff&size=100'">
                <div>
                    <h2 class="profile-name" id="profileName">Ahmed</h2>
                    <div class="profile-role">
                        <i class="fas fa-shield-alt"></i>
                        <span id="profileRole">Administrateur</span>
                    </div>
                </div>
            </div>
<div class="profile-info-card">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <div class="info-label">Adresse Email</div>
                        <div class="info-value" id="profileEmail">ahmed@example.com</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon green">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <div class="info-label">Rôle dans le Système</div>
                        <div class="info-value" id="profileRoleInfo">Administrateur</div>
                        <div class="info-desc">Accès complet aux fonctionnalités d'administration</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon blue">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="info-label">Membre Depuis</div>
                        <div class="info-value" id="profileDate">15 Janvier 2024</div>
                    </div>
                </div>
            </div>
<h3 class="mb-2">Statistiques d'Activité</h3>
            <div class="grid-3">
                <div class="stats-card">
                    <div class="stats-label">Demandes Créées</div>
                    <div class="stats-value" id="statsCreated">42</div>
                </div>
                <div class="stats-card">
                    <div class="stats-label">Demandes Validées</div>
                    <div class="stats-value" id="statsValidated">38</div>
                </div>
                <div class="stats-card">
                    <div class="stats-label">En Cours</div>
                    <div class="stats-value" id="statsPending">4</div>
                </div>
            </div>
        </main>
    </div>
<div class="modal-overlay" id="editProfileModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Gérer le Compte</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div class="form-group">
                        <label class="form-label">Nom Complet <span class="required">*</span></label>
                        <input type="text" class="form-control" id="editName" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" id="editEmail" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rôle</label>
                        <input type="text" class="form-control" id="editRole" readonly>
                        <div class="form-hint">Le rôle ne peut être modifié que par un administrateur</div>
                    </div>
                    
                    <h4 class="mt-3 mb-2">Changer le Mot de Passe</h4>
                    <div class="form-group">
                        <label class="form-label">Mot de Passe Actuel</label>
                        <input type="password" class="form-control" id="currentPassword" placeholder="Entrez votre mot de passe actuel">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nouveau Mot de Passe</label>
                        <input type="password" class="form-control" id="newPassword" placeholder="Minimum 6 caractères">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmer le Nouveau Mot de Passe</label>
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirmez votre nouveau mot de passe">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="saveProfile()">Sauvegarder les Modifications</button>
                <button class="btn btn-secondary" onclick="closeEditModal()">Annuler</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        const API_URL = 'api/';
        let currentUser = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            loadProfile();
        });
        
        async function loadProfile() {
            try {
                const response = await fetch(API_URL + 'users.php?action=current');
                const data = await response.json();
                
                if (data.success) {
                    currentUser = data.user;
                    displayProfile(currentUser);
                }
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        }
        
        function displayProfile(user) {
            document.getElementById('profileName').textContent = user.nom_complet;
            document.getElementById('profileRole').textContent = user.role;
            document.getElementById('profileEmail').textContent = user.email;
            document.getElementById('profileRoleInfo').textContent = user.role;
            
            const date = new Date(user.date_creation);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            document.getElementById('profileDate').textContent = date.toLocaleDateString('fr-FR', options);

            document.getElementById('editName').value = user.nom_complet;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editRole').value = user.role;
        }
        
        function openEditModal() {
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
            document.getElementById('editProfileModal').classList.add('show');
        }
        
        function closeEditModal() {
            document.getElementById('editProfileModal').classList.remove('show');
        }
        
        async function saveProfile() {
            const name = document.getElementById('editName').value;
            const email = document.getElementById('editEmail').value;
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
                const response = await fetch(API_URL + 'users.php', {
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
                    showToast('Profil mis à jour avec succès');
                    closeEditModal();
                    loadProfile();
                } else {
                    alert(data.message || 'Erreur lors de la mise à jour');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                alert('Erreur lors de la mise à jour');
            }
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
            `;
            toast.innerHTML = `<i class="fas fa-check-circle"></i> <span>${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>
</html>
