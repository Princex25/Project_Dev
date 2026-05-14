<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-container">
<button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
<aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
               
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="menu-item">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="users.php" class="menu-item active">
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
                <div class="header-title">
                    <h1>Gestion des Utilisateurs</h1>
                    <p class="text-muted">Gérer les comptes utilisateurs du système</p>
                </div>
                <div class="header-actions">
                    <a href="index.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </header>
<div class="card mb-3">
                <div class="d-flex align-center gap-2" style="flex-wrap: wrap;">
                    <div class="search-input" style="flex: 1; min-width: 250px;">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" placeholder="Rechercher par nom ou email..." id="searchInput">
                    </div>
                    <select class="form-control form-select" id="filterRole" style="width: 200px;">
                        <option value="">Tous les rôles</option>
                        <option value="Administrateur">Administrateur</option>
                        <option value="Validateur">Validateur</option>
                        <option value="Demandeur">Demandeur</option>
                    </select>
                    <select class="form-control form-select" id="filterStatus" style="width: 200px;">
                        <option value="">Tous les statuts</option>
                        <option value="Actif">Actif</option>
                        <option value="Inactif">Inactif</option>
                    </select>
                </div>
            </div>
<button class="btn btn-primary mb-3" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Ajouter un Utilisateur
            </button>
<div class="table-container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
</tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
<div class="modal-overlay" id="addUserModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un Utilisateur</h3>
                <button class="modal-close" onclick="closeAddModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label class="form-label">Nom Complet <span class="required">*</span></label>
                        <input type="text" class="form-control" id="addName" placeholder="Entrez le nom complet" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" id="addEmail" placeholder="email@example.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rôle <span class="required">*</span></label>
                        <select class="form-control form-select" id="addRole" required>
                            <option value="Demandeur">Demandeur</option>
                            <option value="Validateur">Validateur</option>
                            <option value="Administrateur">Administrateur</option>
                        </select>
                    </div>
                    <div class="form-group" id="addTeamCreateGroup" style="display: none;">
                        <label class="form-label">Nom de l'équipe (créée pour ce validateur) <span class="required">*</span></label>
                        <input type="text" class="form-control" id="addTeamName" placeholder="Ex: Équipe Support Nord">
                        <small class="text-muted">Une nouvelle équipe sera créée et rattachée à ce validateur.</small>
                    </div>
                    <div class="form-group" id="addTeamGroup">
                        <label class="form-label">Équipe (Validateur)</label>
                        <select class="form-control form-select" id="addTeam">
                            <option value="">Sélectionnez une équipe</option>
                        </select>
                        <small class="text-muted">Obligatoire pour un demandeur afin de rattacher un validateur.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de Passe <span class="required">*</span></label>
                        <input type="password" class="form-control" id="addPassword" placeholder="Minimum 6 caractères" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <select class="form-control form-select" id="addStatus">
                            <option value="Actif">Actif</option>
                            <option value="Inactif">Inactif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addUserForm" class="btn btn-primary">Ajouter</button>
                <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Annuler</button>
            </div>
        </div>
    </div>
<div class="modal-overlay" id="editUserModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Modifier l'Utilisateur</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId">
                    <div class="form-group">
                        <label class="form-label">Nom Complet <span class="required">*</span></label>
                        <input type="text" class="form-control" id="editName" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" id="editEmail" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rôle <span class="required">*</span></label>
                        <select class="form-control form-select" id="editRole" required>
                            <option value="Demandeur">Demandeur</option>
                            <option value="Validateur">Validateur</option>
                            <option value="Administrateur">Administrateur</option>
                        </select>
                    </div>
                    <div class="form-group" id="editTeamGroup">
                        <label class="form-label">Équipe (Validateur)</label>
                        <select class="form-control form-select" id="editTeam">
                            <option value="">Sélectionnez une équipe</option>
                        </select>
                        <small class="text-muted">Obligatoire pour un demandeur afin de rattacher un validateur.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <select class="form-control form-select" id="editStatus">
                            <option value="Actif">Actif</option>
                            <option value="Inactif">Inactif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="editUserForm" class="btn btn-primary">Modifier</button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Annuler</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        const API_BASE = window.API_URL || 'api/';
        let allUsers = [];
        let managedTeams = [];
        let teamsLoaded = false;
        
        document.addEventListener('DOMContentLoaded', function() {
            ['addUserModal', 'editUserModal'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal) {
                    modal.style.display = 'none';
                    modal.style.visibility = 'hidden';
                    modal.style.opacity = '0';
                }
            });

            loadUsers();
            loadTeams();
            setupFilters();
            const addUserForm = document.getElementById('addUserForm');
            const editUserForm = document.getElementById('editUserForm');
            if (addUserForm) {
                addUserForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    addUser();
                });
                const addRole = document.getElementById('addRole');
                if (addRole) {
                    addRole.addEventListener('change', () => toggleTeamField('add'));
                    toggleTeamField('add');
                }
            }
            if (editUserForm) {
                editUserForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    updateUser();
                });
                const editRole = document.getElementById('editRole');
                if (editRole) {
                    editRole.addEventListener('change', () => toggleTeamField('edit'));
                }
            }
        });
        
        async function loadUsers() {
            try {
                const response = await fetch(API_BASE + 'users.php');
                const data = await response.json();
                
                if (data.success) {
                    allUsers = data.users;
                    renderUsers(allUsers);
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        async function loadTeams() {
            try {
                const response = await fetch(API_BASE + 'users.php?action=teams');
                const data = await response.json();
                if (data.success) {
                    managedTeams = data.teams || [];
                    teamsLoaded = true;
                    refreshTeamSelects();
                }
            } catch (error) {
                console.error('Error loading teams:', error);
            }
        }

        function refreshTeamSelects() {
            fillTeamSelect('addTeam');
            fillTeamSelect('editTeam');
            toggleTeamField('add');
            toggleTeamField('edit');
        }

        function fillTeamSelect(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return;
            const current = select.value;
            select.innerHTML = '<option value="">Sélectionnez une équipe</option>';
            managedTeams.forEach(team => {
                const option = document.createElement('option');
                option.value = team.id;
                const label = team.validateurs ? `${team.nom} (${team.validateurs})` : team.nom;
                option.textContent = label;
                select.appendChild(option);
            });
            if (!managedTeams.length) {
                const opt = document.createElement('option');
                opt.disabled = true;
                opt.textContent = 'Aucune équipe avec validateur disponible';
                select.appendChild(opt);
            }
            if (current) {
                select.value = current;
            }
        }
        
        function renderUsers(users) {
            const tbody = document.getElementById('usersTableBody');
            
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.nom_complet}</td>
                    <td>${user.email}</td>
                    <td><span class="badge ${getRoleBadgeClass(user.role)}">${user.role}</span></td>
                    <td><span class="badge ${user.statut === 'Actif' ? 'badge-success' : 'badge-warning'}">${user.statut}</span></td>
                    <td>
                        <button class="btn btn-edit-black btn-sm" onclick="openEditModal(${user.id})">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </td>
                </tr>
            `).join('');
        }
        
        function getRoleBadgeClass(role) {
            const classes = {
                'Administrateur': 'badge-danger',
                'Validateur': 'badge-info',
                'Demandeur': 'badge-success'
            };
            return classes[role] || 'badge-secondary';
        }
        
        function setupFilters() {
            const searchInput = document.getElementById('searchInput');
            const filterRole = document.getElementById('filterRole');
            const filterStatus = document.getElementById('filterStatus');
            
            [searchInput, filterRole, filterStatus].forEach(el => {
                el.addEventListener('change', applyFilters);
                if (el.tagName === 'INPUT') {
                    el.addEventListener('keyup', applyFilters);
                }
            });
        }
        
        function applyFilters() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const role = document.getElementById('filterRole').value;
            const status = document.getElementById('filterStatus').value;
            
            const filtered = allUsers.filter(user => {
                const matchSearch = !search || 
                    user.nom_complet.toLowerCase().includes(search) ||
                    user.email.toLowerCase().includes(search);
                const matchRole = !role || user.role === role;
                const matchStatus = !status || user.statut === status;
                
                return matchSearch && matchRole && matchStatus;
            });
            
            renderUsers(filtered);
        }

        function toggleTeamField(context) {
            const roleEl = document.getElementById(context === 'edit' ? 'editRole' : 'addRole');
            const group = document.getElementById(context === 'edit' ? 'editTeamGroup' : 'addTeamGroup');
            const createGroup = document.getElementById(context === 'edit' ? 'editTeamCreateGroup' : 'addTeamCreateGroup');
            const isDemandeur = roleEl && roleEl.value === 'Demandeur';
            const isValidateur = roleEl && roleEl.value === 'Validateur';
            if (!roleEl || !group) return;
            if (isDemandeur && !teamsLoaded) {
                loadTeams();
            }
            group.style.display = isDemandeur ? 'block' : 'none';
            if (!isDemandeur) {
                const select = document.getElementById(context === 'edit' ? 'editTeam' : 'addTeam');
                if (select) select.value = '';
            }
            if (createGroup) {
                createGroup.style.display = isValidateur ? 'block' : 'none';
                if (!isValidateur) {
                    const input = document.getElementById(context === 'edit' ? 'editTeamName' : 'addTeamName');
                    if (input) input.value = '';
                }
            }
        }
        
        function openAddModal() {
            const modal = document.getElementById('addUserModal');
            const form = document.getElementById('addUserForm');
            if (form) form.reset();
            if (modal) {
                const root = document.getElementById('modalRoot');
                if (root && modal.parentElement !== root) {
                    root.appendChild(modal);
                }
                modal.style.display = 'flex';
                modal.style.visibility = 'visible';
                modal.style.opacity = '1';
                modal.classList.add('show', 'force-show', 'modal-opened');
                updateBodyScrollLock();
            }
            toggleTeamField('add');
        }
        
        function closeAddModal() {
            const modal = document.getElementById('addUserModal');
            if (modal) {
                modal.classList.remove('show', 'force-show', 'modal-opened');
                modal.style.display = 'none';
                modal.style.visibility = 'hidden';
                modal.style.opacity = '0';
            }
            updateBodyScrollLock();
        }
        
        async function addUser() {
            const name = document.getElementById('addName').value;
            const email = document.getElementById('addEmail').value;
            const role = document.getElementById('addRole').value;
            const teamId = document.getElementById('addTeam') ? document.getElementById('addTeam').value : '';
            const teamName = document.getElementById('addTeamName') ? document.getElementById('addTeamName').value.trim() : '';
            const password = document.getElementById('addPassword').value;
            const status = document.getElementById('addStatus').value;
            
            if (!name || !email || !password) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            if (password.length < 6) {
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return;
            }

            if (role === 'Demandeur') {
                if (!managedTeams.length) {
                    alert('Aucune équipe disposant d\'un validateur n\'est disponible.');
                    return;
                }
                if (!teamId) {
                    alert('Sélectionnez une équipe pour rattacher ce demandeur.');
                    return;
                }
            }

            if (role === 'Validateur') {
                if (!teamName) {
                    alert('Saisissez le nom de l\'équipe à créer pour ce validateur.');
                    return;
                }
            }
            
            try {
                const response = await fetch(API_BASE + 'users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        nom_complet: name,
                        email: email,
                        role: role,
                        equipe_id: role === 'Demandeur' ? teamId : null,
                        equipe_nom: role === 'Validateur' ? teamName : null,
                        mot_de_passe: password,
                        statut: status
                    })
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Utilisateur ajouté avec succès');
                    closeAddModal();
                    loadUsers();
                } else {
                    alert(data.message || 'Erreur lors de l\'ajout');
                }
            } catch (error) {
                console.error('Error adding user:', error);
                alert('Erreur lors de l\'ajout');
            }
        }
        
        async function openEditModal(id) {
            try {
                const response = await fetch(API_BASE + 'users.php?id=' + id);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('editUserId').value = data.user.id;
                    document.getElementById('editName').value = data.user.nom_complet;
                    document.getElementById('editEmail').value = data.user.email;
                    document.getElementById('editRole').value = data.user.role;
                    document.getElementById('editStatus').value = data.user.statut;
                    await loadTeams();
                    fillTeamSelect('editTeam');
                    const editTeam = document.getElementById('editTeam');
                    if (editTeam) {
                        editTeam.value = data.user.equipe_id || '';
                    }
                    const modal = document.getElementById('editUserModal');
                    if (modal) {
                        const root = document.getElementById('modalRoot');
                        if (root && modal.parentElement !== root) {
                            root.appendChild(modal);
                        }
                        modal.style.display = 'flex';
                        modal.style.visibility = 'visible';
                        modal.style.opacity = '1';
                        modal.classList.add('show', 'force-show', 'modal-opened');
                        updateBodyScrollLock();
                    }
                    toggleTeamField('edit');
                }
            } catch (error) {
                console.error('Error loading user:', error);
            }
        }
        
        function closeEditModal() {
            const modal = document.getElementById('editUserModal');
            if (modal) {
                modal.classList.remove('show', 'force-show', 'modal-opened');
                modal.style.display = 'none';
                modal.style.visibility = 'hidden';
                modal.style.opacity = '0';
            }
            updateBodyScrollLock();
        }
        
        async function updateUser() {
            const id = document.getElementById('editUserId').value;
            const name = document.getElementById('editName').value;
            const email = document.getElementById('editEmail').value;
            const role = document.getElementById('editRole').value;
            const teamId = document.getElementById('editTeam') ? document.getElementById('editTeam').value : '';
            const status = document.getElementById('editStatus').value;

            if (role === 'Demandeur') {
                if (!managedTeams.length) {
                    alert('Aucune équipe disposant d\'un validateur n\'est disponible.');
                    return;
                }
                if (!teamId) {
                    alert('Sélectionnez une équipe pour rattacher ce demandeur.');
                    return;
                }
            }
            
            try {
                const response = await fetch(API_BASE + 'users.php?id=' + id, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: id,
                        nom_complet: name,
                        email: email,
                        role: role,
                        equipe_id: role === 'Demandeur' ? teamId : null,
                        statut: status
                    })
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Utilisateur modifié avec succès');
                    closeEditModal();
                    loadUsers();
                } else {
                    alert(data.message || 'Erreur lors de la modification');
                }
            } catch (error) {
                console.error('Error updating user:', error);
                alert('Erreur lors de la modification');
            }
        }
        
        async function deleteUser(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                return;
            }
            
            try {
                const response = await fetch(API_BASE + 'users.php?id=' + id, {
                    method: 'DELETE'
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Utilisateur supprimé avec succès');
                    loadUsers();
                } else {
                    alert(data.message || 'Erreur lors de la suppression');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                alert('Erreur lors de la suppression');
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

        function updateBodyScrollLock() {
            const activeModal = document.querySelector('.modal-overlay.show, .modal-overlay.modal-opened');
            if (activeModal) {
                document.body.classList.add('modal-open');
            } else {
                document.body.classList.remove('modal-open');
            }
        }
    </script>
</body>
</html>
