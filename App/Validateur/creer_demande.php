<?php

require_once 'api/creer_demande.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Nouvelle Demande - Validater</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="content" id="mainContent">
            <div class="page-header">
                <a href="index.php" class="back-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Retour au Dashboard
                </a>
            </div>
            
            <div class="card form-card">
                <h2 class="form-title">Créer une Nouvelle Demande</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="demande-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="type_demande">Type de demande *</label>
                            <select name="type_demande" id="type_demande" required>
                                <option value="">Sélectionner un type</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="urgence">Niveau d'urgence *</label>
                            <select name="urgence" id="urgence" required>
                                <option value="Faible">Faible</option>
                                <option value="Moyenne">Moyenne</option>
                                <option value="Urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description courte *</label>
                        <input type="text" name="description" id="description" placeholder="Ex: Souris ergonomique, Formation Excel..." required>
                    </div>
                    
                    <div class="form-group">
                        <label for="justification">Détails et justification *</label>
                        <textarea name="justification" id="justification" rows="5" placeholder="Décrivez votre demande en détail et justifiez son importance..." required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="budget">Budget estimé (optionnel)</label>
                        <input type="text" name="budget" id="budget" placeholder="Ex: 50€, 500€...">
                    </div>
                    
                    <div class="form-notice">
                        <p>* Champs obligatoires. Votre demande sera envoyée à votre chef hiérarchique pour validation.</p>
                    </div>
                    
                    <div class="form-actions">
                        <a href="index.php" class="btn btn-cancel">Annuler</a>
                        <button type="submit" class="btn btn-submit">Soumettre la demande</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
