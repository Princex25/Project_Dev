<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Types de Besoins</title>
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
                <a href="users.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Gestion Utilisateurs</span>
                </a>
                <a href="types.php" class="menu-item active">
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
                    <h1>Gestion des Types de Besoins</h1>
                    <p class="text-muted">Gérer les catégories de demandes disponibles</p>
                </div>
                <div class="header-actions">
                    <a href="index.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </header>
<button class="btn btn-primary mb-3" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Ajouter un Type de Besoin
            </button>
<div class="grid-4 mb-3" id="typesCardsContainer">
</div>
</main>
    </div>
<div class="modal-overlay" id="addTypeModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un Type de Besoin</h3>
                <button class="modal-close" onclick="closeAddModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addTypeForm">
                    <div class="form-group">
                        <label class="form-label">Nom du Type <span class="required">*</span></label>
                        <input type="text" class="form-control" id="addTypeName" placeholder="Ex: Matériel, Logiciel, Service..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="addTypeDescription" rows="3" placeholder="Décrivez ce type de besoin..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="addType()">Ajouter</button>
                <button class="btn btn-secondary" onclick="closeAddModal()">Annuler</button>
            </div>
        </div>
    </div>
<div class="modal-overlay" id="editTypeModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Modifier le Type de Besoin</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editTypeForm">
                    <input type="hidden" id="editTypeId">
                    <div class="form-group">
                        <label class="form-label">Nom du Type <span class="required">*</span></label>
                        <input type="text" class="form-control" id="editTypeName" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="editTypeDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="updateType()">Modifier</button>
                <button class="btn btn-secondary" onclick="closeEditModal()">Annuler</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        const API_BASE = window.API_URL || 'api/';
        let allTypes = [];
        
        const typeColors = ['#ffc107', '#dc3545', '#0d6efd', '#28a745', '#6f42c1', '#fd7e14', '#20c997'];
        const typeIcons = ['laptop', 'code', 'cogs', 'graduation-cap', 'tools', 'folder', 'clipboard'];
        
        document.addEventListener('DOMContentLoaded', function() {
            loadTypes();
            setupSearch();
        });
        
        async function loadTypes() {
            try {
                const response = await fetch(API_BASE + 'types.php');
                const data = await response.json();
                
                if (data.success) {
                    allTypes = data.types;
                    renderTypesCards(allTypes);
                    renderTypesTable(allTypes);
                }
            } catch (error) {
                console.error('Error loading types:', error);
            }
        }
        
        function renderTypesCards(types) {
            const container = document.getElementById('typesCardsContainer');
            
            container.innerHTML = types.map((type, index) => {
                const color = typeColors[index % typeColors.length];
                const icon = typeIcons[index % typeIcons.length];
                
                return `
                    <div class="type-card">
                        <div class="type-card-header">
                            <div class="type-card-icon" style="background: ${color}20; color: ${color};">
                                <i class="fas fa-${icon}"></i>
                            </div>
                            <div>
                                <div class="type-card-title">${type.nom}</div>
                                <div class="type-card-id">ID: ${type.id}</div>
                            </div>
                        </div>
                        <p class="type-card-description">${type.description || 'Pas de description'}</p>
                        <div class="type-card-actions">
                            <button class="btn btn-success btn-sm" onclick="openEditModal(${type.id})">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteType(${type.id})">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        function renderTypesTable(types) {
            const tbody = document.getElementById('typesTableBody');
            
            tbody.innerHTML = types.map((type, index) => {
                const color = typeColors[index % typeColors.length];
                const icon = typeIcons[index % typeIcons.length];
                
                return `
                    <tr>
                        <td>${type.id}</td>
                        <td>
                            <div class="d-flex align-center gap-1">
                                <span style="color: ${color};"><i class="fas fa-${icon}"></i></span>
                                <strong>${type.nom}</strong>
                            </div>
                        </td>
                        <td>${type.description || '-'}</td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="openEditModal(${type.id})">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteType(${type.id})">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keyup', function() {
                const search = this.value.toLowerCase();
                const filtered = allTypes.filter(type => 
                    type.nom.toLowerCase().includes(search) ||
                    (type.description && type.description.toLowerCase().includes(search))
                );
                renderTypesCards(filtered);
                renderTypesTable(filtered);
            });
        }

        function openAddModal() {
            document.getElementById('addTypeForm').reset();
            document.getElementById('addTypeModal').classList.add('show');
        }
        
        function closeAddModal() {
            document.getElementById('addTypeModal').classList.remove('show');
        }
        
        async function addType() {
            const name = document.getElementById('addTypeName').value;
            const description = document.getElementById('addTypeDescription').value;
            
            if (!name) {
                alert('Le nom est requis');
                return;
            }
            
            try {
                const response = await fetch(API_BASE + 'types.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nom: name, description: description })
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Type ajouté avec succès');
                    closeAddModal();
                    loadTypes();
                } else {
                    alert(data.message || 'Erreur lors de l\'ajout');
                }
            } catch (error) {
                console.error('Error adding type:', error);
                alert('Erreur lors de l\'ajout');
            }
        }
        
        async function openEditModal(id) {
            try {
                const response = await fetch(API_BASE + 'types.php?id=' + id);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('editTypeId').value = data.type.id;
                    document.getElementById('editTypeName').value = data.type.nom;
                    document.getElementById('editTypeDescription').value = data.type.description || '';
                    document.getElementById('editTypeModal').classList.add('show');
                }
            } catch (error) {
                console.error('Error loading type:', error);
            }
        }
        
        function closeEditModal() {
            document.getElementById('editTypeModal').classList.remove('show');
        }
        
        async function updateType() {
            const id = document.getElementById('editTypeId').value;
            const name = document.getElementById('editTypeName').value;
            const description = document.getElementById('editTypeDescription').value;
            
            if (!name) {
                alert('Le nom est requis');
                return;
            }
            
            try {
                const response = await fetch(API_BASE + 'types.php?id=' + id, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, nom: name, description: description })
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Type modifié avec succès');
                    closeEditModal();
                    loadTypes();
                } else {
                    alert(data.message || 'Erreur lors de la modification');
                }
            } catch (error) {
                console.error('Error updating type:', error);
                alert('Erreur lors de la modification');
            }
        }
        
        async function deleteType(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce type ?')) {
                return;
            }
            
            try {
                const response = await fetch(API_BASE + 'types.php?id=' + id, {
                    method: 'DELETE'
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Type supprimé avec succès');
                    loadTypes();
                } else {
                    alert(data.message || 'Erreur lors de la suppression');
                }
            } catch (error) {
                console.error('Error deleting type:', error);
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
    </script>
</body>
</html>
