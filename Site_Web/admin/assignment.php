<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affectation des Demandes</title>
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
                    <h1>Affectation des Demandes</h1>
                    <p class="text-muted">Assigner les demandes en attente aux services appropriés</p>
                </div>
                <div class="header-actions">
                    <a href="index.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </header>
<div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Mode Affectation Activé</strong>
                    <p style="margin: 5px 0 0 0; opacity: 0.9;">Sélectionnez le service approprié pour chaque demande en attente d'affectation.</p>
                </div>
            </div>
<div class="grid-3 mb-3">
                <div class="card card-glass">
                    <div class="stat-label">Demandes en Attente</div>
                    <div class="stat-value" id="pendingCount">0</div>
                </div>
                <div class="card card-glass">
                    <div class="stat-label">Non Assignées</div>
                    <div class="stat-value" id="unassignedCount">0</div>
                </div>
                <div class="card card-glass">
                    <div class="stat-label">Modifications en Cours</div>
                    <div class="stat-value" id="modifiedCount">0</div>
                </div>
            </div>
<div class="table-container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Demandeur</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Service Actuel</th>
                                <th>Nouveau Service</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="demandesTableBody">
</tbody>
                    </table>
                </div>
            </div>
<div class="d-flex justify-center gap-2 mt-3">
                <button class="btn btn-primary btn-lg" onclick="saveChanges()">
                    <i class="fas fa-save"></i> Mettre à Jour les Modifications
                </button>
                <button class="btn btn-secondary btn-lg" onclick="cancelChanges()">
                    <i class="fas fa-times"></i> Annuler
                </button>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        const API_BASE = window.API_URL || 'api/';
        let services = [];
        let demandes = [];
        let changes = {};
        
        document.addEventListener('DOMContentLoaded', function() {
            loadServices();
            loadDemandes();
        });
        
        async function loadServices() {
            try {
                const response = await fetch(API_BASE + 'services.php');
                const data = await response.json();
                if (data.success) {
                    services = data.services;
                }
            } catch (error) {
                console.error('Error loading services:', error);
            }
        }
        
        async function loadDemandes() {
            try {
                const response = await fetch(API_BASE + 'demandes.php?action=assignment');
                const data = await response.json();
                
                if (data.success) {
                    demandes = data.demandes;
                    document.getElementById('pendingCount').textContent = data.stats.pending;
                    document.getElementById('unassignedCount').textContent = data.stats.unassigned;
                    document.getElementById('modifiedCount').textContent = Object.keys(changes).length;
                    renderTable();
                }
            } catch (error) {
                console.error('Error loading demandes:', error);
            }
        }
        
        function renderTable() {
            const tbody = document.getElementById('demandesTableBody');
            
            tbody.innerHTML = demandes.map(d => {
                const currentService = d.service_nom || 'Non assigné';
                const serviceClass = d.service_nom ? getServiceBadgeClass(d.service_nom) : 'badge-secondary';
                
                return `
                    <tr>
                        <td>${d.id}</td>
                        <td>${d.demandeur_nom}</td>
                        <td>${d.type_nom}</td>
                        <td>${d.description}</td>
                        <td><span class="badge ${serviceClass}">${currentService}</span></td>
                        <td>
                            <select class="form-control form-select" style="width: 180px;" 
                                    onchange="trackChange(${d.id}, this.value)" id="service-${d.id}">
                                <option value="">Non assigné</option>
                                ${services.map(s => `
                                    <option value="${s.id}" ${d.service_id == s.id ? 'selected' : ''}>${s.nom}</option>
                                `).join('')}
                            </select>
                        </td>
                        <td>${formatDate(d.date_creation)}</td>
                    </tr>
                `;
            }).join('');
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
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toISOString().split('T')[0];
        }
        
        function trackChange(demandeId, serviceId) {
            const demande = demandes.find(d => d.id == demandeId);
            const originalServiceId = demande.service_id || '';
            
            if (serviceId != originalServiceId) {
                changes[demandeId] = serviceId || null;
            } else {
                delete changes[demandeId];
            }
            
            document.getElementById('modifiedCount').textContent = Object.keys(changes).length;
        }
        
        async function saveChanges() {
            if (Object.keys(changes).length === 0) {
                alert('Aucune modification à sauvegarder');
                return;
            }
            
            const updates = Object.entries(changes).map(([id, service_id]) => ({
                id: parseInt(id),
                service_id: service_id ? parseInt(service_id) : null
            }));
            
            try {
                const response = await fetch(API_BASE + 'demandes.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'bulk_update',
                        updates: updates
                    })
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Modifications enregistrées avec succès');
                    changes = {};
                    loadDemandes();
                } else {
                    alert(data.message || 'Erreur lors de la sauvegarde');
                }
            } catch (error) {
                console.error('Error saving changes:', error);
                alert('Erreur lors de la sauvegarde');
            }
        }
        
        function cancelChanges() {
            if (Object.keys(changes).length > 0) {
                if (!confirm('Annuler toutes les modifications non sauvegardées ?')) {
                    return;
                }
            }
            changes = {};
            loadDemandes();
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
