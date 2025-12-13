
CREATE DATABASE IF NOT EXISTS gestion_demandes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_demandes;


CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_complet VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    role ENUM('Administrateur', 'Validateur', 'Demandeur') NOT NULL DEFAULT 'Demandeur',
    statut ENUM('Actif', 'Inactif') DEFAULT 'Actif',
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    departement_id INT NULL,
    equipe_id INT NULL,
    date_entree DATE NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL,
    remember_token VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS departements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS equipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    departement_id INT,
    chef_equipe_id INT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (departement_id) REFERENCES departements(id) ON DELETE SET NULL
);


ALTER TABLE users ADD FOREIGN KEY (departement_id) REFERENCES departements(id) ON DELETE SET NULL;
ALTER TABLE users ADD FOREIGN KEY (equipe_id) REFERENCES equipes(id) ON DELETE SET NULL;
ALTER TABLE equipes ADD FOREIGN KEY (chef_equipe_id) REFERENCES users(id) ON DELETE SET NULL;


CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    couleur VARCHAR(20) DEFAULT '#17a2b8',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS types_besoins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    icone VARCHAR(50) DEFAULT 'tag',
    couleur VARCHAR(20) DEFAULT '#ffc107',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS demandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demandeur_id INT NOT NULL,
    type_id INT NOT NULL,
    description TEXT NOT NULL,
    details_justification TEXT,
    statut ENUM('En attente', 'En cours de validation', 'Validée', 'Rejetée', 'En cours', 'Traitée') DEFAULT 'En attente',
    priorite ENUM('Faible', 'Normale', 'Moyenne', 'Haute', 'Urgente') DEFAULT 'Normale',
    service_id INT NULL,
    validateur_id INT NULL,
    raison_rejet TEXT NULL,
    budget_estime VARCHAR(50) NULL,
    fichier_joint VARCHAR(255) NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    date_traitement TIMESTAMP NULL,
    FOREIGN KEY (demandeur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES types_besoins(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (validateur_id) REFERENCES users(id) ON DELETE SET NULL
);


CREATE TABLE IF NOT EXISTS pieces_jointes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille INT,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS brouillons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demandeur_id INT NOT NULL,
    type_id INT NULL,
    description TEXT,
    priorite VARCHAR(50),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (demandeur_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    demande_id INT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    lu BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    ancien_statut VARCHAR(50),
    nouveau_statut VARCHAR(50),
    details TEXT,
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);


INSERT INTO departements (nom, description) VALUES 
('IT', 'Service Informatique'),
('RH', 'Ressources Humaines'),
('Finance', 'Service Financier'),
('Marketing', 'Service Marketing'),
('Logistique', 'Service Logistique');


INSERT INTO equipes (nom, departement_id) VALUES 
('Développement', 1),
('Support IT', 1),
('Infrastructure', 1),
('Recrutement', 2),
('Comptabilité', 3),
('Communication', 4);


INSERT INTO services (nom, description, couleur) VALUES
('Support IT', 'Service de support informatique', '#17a2b8'),
('Agent IT', 'Service des agents informatiques', '#28a745'),
('Service RH', 'Service des ressources humaines', '#dc3545'),
('Service Finance', 'Service financier', '#ffc107'),
('Service Logistique', 'Service de logistique', '#6f42c1');

INSERT INTO types_besoins (nom, description, icone, couleur) VALUES
('Matériel', 'Demandes de matériel informatique ou de bureau', 'laptop', '#ffc107'),
('Logiciel', 'Demandes de licences logicielles ou applications', 'code', '#dc3545'),
('Service', 'Demandes de services ou prestations', 'cogs', '#0d6efd'),
('Formation', 'Demandes de formations professionnelles', 'graduation-cap', '#28a745'),
('Support Technique', 'Demandes d''assistance technique', 'tools', '#6f42c1'),
('Autre', 'Autres types de demandes', 'tag', '#6c757d');

INSERT INTO users (nom_complet, email, mot_de_passe, telephone, role, statut, departement_id, equipe_id, date_entree) VALUES

('Admin Principal', 'admin@example.com', 'password123', '+33 6 00 00 00 00', 'Administrateur', 'Actif', 1, 1, '2020-01-01'),


('Ahmed Ben Ali', 'ahmed@example.com', 'password123', '+33 6 12 34 56 78', 'Validateur', 'Actif', 1, 1, '2020-01-15'),
('Fatima Zahra', 'fatima@example.com', 'password123', '+33 6 11 22 33 44', 'Validateur', 'Actif', 2, 4, '2021-03-10'),


('Mohamed Alami', 'mohamed@example.com', 'password123', '+33 6 55 66 77 88', 'Demandeur', 'Actif', 1, 1, '2022-06-01'),
('Sara Bennani', 'sara@example.com', 'password123', '+33 6 99 88 77 66', 'Demandeur', 'Actif', 1, 2, '2021-09-15'),
('Youssef Idrissi', 'youssef@example.com', 'password123', '+33 6 44 55 66 77', 'Demandeur', 'Actif', 3, 5, '2023-01-20'),
('Alice Martin', 'alice@example.com', 'password123', '+33 6 33 44 55 66', 'Demandeur', 'Actif', 1, 1, '2022-02-14');


UPDATE equipes SET chef_equipe_id = 2 WHERE id = 1; 
UPDATE equipes SET chef_equipe_id = 3 WHERE id = 4; 


INSERT INTO demandes (demandeur_id, type_id, description, details_justification, statut, priorite, service_id, date_creation) VALUES
(4, 1, 'Ordinateur portable', 'Besoin d''un nouvel ordinateur pour le travail à distance', 'En attente', 'Normale', NULL, '2024-12-01 10:00:00'),
(4, 1, 'Souris ergonomique', 'Ma souris actuelle cause des douleurs au poignet', 'Validée', 'Normale', 1, '2024-11-20 09:00:00'),
(5, 2, 'Licence Adobe', 'Besoin de la suite Adobe pour les projets de design', 'Rejetée', 'Haute', 3, '2024-11-25 11:00:00'),
(6, 3, 'Formation Excel avancé', 'Formation pour améliorer les compétences en analyse de données', 'En cours', 'Moyenne', NULL, '2024-11-28 16:00:00'),
(7, 1, 'Clavier mécanique', 'Remplacement du clavier défectueux', 'En attente', 'Faible', NULL, '2024-12-02 08:00:00');


INSERT INTO notifications (user_id, demande_id, message, type, lu) VALUES
(4, 2, 'Votre demande de souris ergonomique a été validée', 'success', FALSE),
(5, 3, 'Votre demande de licence Adobe a été rejetée. Raison: Budget insuffisant', 'error', FALSE),
(2, 1, 'Nouvelle demande de Mohamed Alami en attente de validation', 'info', FALSE),
(2, 5, 'Nouvelle demande de Alice Martin en attente de validation', 'info', FALSE);


INSERT INTO historique (demande_id, user_id, action, ancien_statut, nouveau_statut, details) VALUES
(1, 4, 'Création de la demande', NULL, 'En attente', 'Demande créée par le demandeur'),
(2, 4, 'Création de la demande', NULL, 'En attente', 'Demande créée par le demandeur'),
(2, 2, 'Validation de la demande', 'En attente', 'Validée', 'Demande validée par Ahmed Ben Ali'),
(3, 5, 'Création de la demande', NULL, 'En attente', 'Demande créée par le demandeur'),
(3, 2, 'Rejet de la demande', 'En attente', 'Rejetée', 'Budget insuffisant');
