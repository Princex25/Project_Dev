<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Demande</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .detail-card {
            background: rgba(255, 255, 255, 0.40);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        }
        
        .detail-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            width: 200px;
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .detail-value {
            flex: 1;
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
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
                <div class="header-title">
                    <h1>Détails de la Demande #<span id="demandeId">-</span></h1>
                </div>
                <div class="header-actions">
                    <a href="index.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </header>
<div class="detail-card">
                <h3 class="mb-3">Informations de la Demande</h3>
                <div class="detail-row">
                    <div class="detail-label">ID</div>
                    <div class="detail-value" id="detailId">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Demandeur</div>
                    <div class="detail-value" id="detailDemandeur">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value" id="detailEmail">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Type</div>
                    <div class="detail-value" id="detailType">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Description</div>
                    <div class="detail-value" id="detailDescription">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Statut</div>
                    <div class="detail-value" id="detailStatut">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Priorité</div>
                    <div class="detail-value" id="detailPriorite">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Service Assigné</div>
                    <div class="detail-value" id="detailService">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Date de Création</div>
                    <div class="detail-value" id="detailDate">-</div>
                </div>
            </div>
<div class="detail-card">
                <h3 class="mb-3">Actions</h3>
                <div class="form-group">
                    <label class="form-label">Changer le Statut</label>
                    <select class="form-control form-select" id="statusSelect" style="max-width: 300px;">
                        <option value="En attente">En attente</option>
                        <option value="En cours de validation">En cours de validation</option>
                        <option value="En cours">En cours</option>
                        <option value="Validée">Validée</option>
                        <option value="Traitée">Traitée</option>
                        <option value="Rejetée">Rejetée</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assigner à un Service</label>
                    <select class="form-control form-select" id="serviceSelect" style="max-width: 300px;">
                        <option value="">Non assigné</option>
                    </select>
                </div>
                <div class="form-group" id="rejectReasonGroup" style="display: none;">
                    <label class="form-label">Raison du Rejet</label>
                    <textarea class="form-control" id="rejectReason" rows="3" placeholder="Expliquez la raison du rejet..."></textarea>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="saveChanges()">
                        <i class="fas fa-save"></i> Sauvegarder
                    </button>
                    <button class="btn btn-danger" onclick="deleteDemande()">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>

        const API_BASE = window.API_URL || 'api/';
        let currentDemande = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            
            if (id) {
                loadDemande(id);
                loadServices();
            } else {
                alert('ID de demande manquant');
                window.location.href = 'index.php';
            }

            document.getElementById('statusSelect').addEventListener('change', function() {
                document.getElementById('rejectReasonGroup').style.display = 
                    this.value === 'Rejetée' ? 'block' : 'none';
            });
        });
        
        async function loadDemande(id) {
            try {
                const response = await fetch(API_BASE + 'demandes.php?id=' + id);
                const data = await response.json();
                
                if (data.success) {
                    currentDemande = data.demande;
                    displayDemande(currentDemande);
                } else {
                    alert('Demande non trouvée');
                    window.location.href = 'index.php';
                }
            } catch (error) {
                console.error('Error loading demande:', error);
            }
        }
        
        async function loadServices() {
            try {
                const response = await fetch(API_BASE + 'services.php');
                const data = await response.json();
                
                if (data.success) {
                    const select = document.getElementById('serviceSelect');
                    data.services.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;
                        option.textContent = service.nom;
                        select.appendChild(option);
                    });
                    
                    if (currentDemande && currentDemande.service_id) {
                        select.value = currentDemande.service_id;
                    }
                }
            } catch (error) {
                console.error('Error loading services:', error);
            }
        }
        
        function displayDemande(demande) {
            document.getElementById('demandeId').textContent = demande.id;
            document.getElementById('detailId').textContent = demande.id;
            document.getElementById('detailDemandeur').textContent = demande.demandeur_nom;
            document.getElementById('detailEmail').textContent = demande.demandeur_email;
            document.getElementById('detailType').textContent = demande.type_nom;
            document.getElementById('detailDescription').textContent = demande.description;
            
            const statusBadge = `<span class="badge ${getStatusBadgeClass(demande.statut)}">${demande.statut}</span>`;
            document.getElementById('detailStatut').innerHTML = statusBadge;
            
            const priorityBadge = `<span class="badge ${getPriorityBadgeClass(demande.priorite)}">${demande.priorite}</span>`;
            document.getElementById('detailPriorite').innerHTML = priorityBadge;
            
            document.getElementById('detailService').textContent = demande.service_nom || 'Non assigné';
            
            const date = new Date(demande.date_creation);
            document.getElementById('detailDate').textContent = date.toLocaleString('fr-FR');
            
            document.getElementById('statusSelect').value = demande.statut;
            if (demande.service_id) {
                document.getElementById('serviceSelect').value = demande.service_id;
            }
            
            if (demande.statut === 'Rejetée') {
                document.getElementById('rejectReasonGroup').style.display = 'block';
                document.getElementById('rejectReason').value = demande.raison_rejet || '';
            }
        }
        
        function getStatusBadgeClass(status) {
            const classes = {
                'En attente': 'badge-warning',
                'Validée': 'badge-success',
                'Rejetée': 'badge-danger',
                'En cours': 'badge-info',
                'En cours de validation': 'badge-info',
                'Traitée': 'badge-success'
            };
            return classes[status] || 'badge-secondary';
        }
        
        function getPriorityBadgeClass(priority) {
            const classes = {
                'Urgente': 'badge-danger',
                'Haute': 'badge-warning',
                'Moyenne': 'badge-info',
                'Normale': 'badge-secondary'
            };
            return classes[priority] || 'badge-secondary';
        }
        
        async function saveChanges() {
            const status = document.getElementById('statusSelect').value;
            const serviceId = document.getElementById('serviceSelect').value;
            const rejectReason = document.getElementById('rejectReason').value;
            
            try {
                const response = await fetch(API_BASE + 'demandes.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: currentDemande.id,
                        statut: status,
                        service_id: serviceId || null,
                        raison_rejet: status === 'Rejetée' ? rejectReason : null
                    })
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Modifications enregistrées avec succès');
                    loadDemande(currentDemande.id);
                } else {
                    alert(data.message || 'Erreur lors de la sauvegarde');
                }
            } catch (error) {
                console.error('Error saving changes:', error);
                alert('Erreur lors de la sauvegarde');
            }
        }
        
        async function deleteDemande() {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')) {
                return;
            }
            
            try {
                const response = await fetch(API_BASE + 'demandes.php?id=' + currentDemande.id, {
                    method: 'DELETE'
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Demande supprimée avec succès');
                    window.location.href = 'index.php';
                } else {
                    alert(data.message || 'Erreur lors de la suppression');
                }
            } catch (error) {
                console.error('Error deleting demande:', error);
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
