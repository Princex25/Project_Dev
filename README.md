Technologies : PHP – MySQL – Bootstrap 5
It'a me that commit

1.  Contexte du projet
    Au sein d’une organisation, les employés expriment régulièrement des besoins
    (matériel, logiciel, service, formation...).
    Actuellement, la gestion se fait de manière manuelle (papier, e-mail), ce qui entraîne :
    • Des retards de validation
    • Des pertes de demandes
    • Une absence de traçabilité
    • Difficulté de suivi et de reporting
    Le projet vise à numériser l’ensemble du processus afin de garantir un traitement fluide
    et une meilleure visibilité des demandes.
2.  Objectifs du projet
    Objectif principal
    Mettre en place une application web permettant de gérer les demandes d’expression de
    besoins, depuis leur création jusqu’à leur validation.
    Objectifs spécifiques
    • Permettre aux employés de soumettre facilement une demande.
    • Offrir un espace de suivi des demandes à chaque utilisateur.
    • Automatiser les notifications (email ou internes).
3.  Périmètre fonctionnel
    3.1 Utilisateurs
    • Demandeur (Employé)
    • Validateur (Chef hiérarchique)
    • Administrateur (RH / Service achat / DSI...)
    3.2 Fonctionnalités détaillées
    A. Espace Demandeur 1. Authentification (login / mot de passe). 2. Création d’une demande :
    o Type de besoin (matériel, service, logiciel, autre)
    o Description détaillée
    o Urgence (faible / moyenne / urgente)
    o Pièces jointes (optionnel)
        3. Consultation de l’historique des demandes
        4. Suivi des statuts :
            o En attente
            o En cours de validation
            o Validée
            o Rejetée
            o Traitée

    5. Possibilité de modifier une demande tant qu’elle n’est pas validée.

B. Espace Validateur (Chef hiérarchique) 1. Consultation des demandes de son équipe. 2. Validation ou rejet avec commentaire. 3. Transmission automatique au niveau supérieur si nécessaire. 4. Filtrage par statut / demandeur / date.
C. Espace Administrateur 1. Gestion des utilisateurs (CRUD). 2. Gestion des types de besoins. 3. Consultation de toutes les demandes. 4. Affectation des demandes à un service (ex : achat, DSI...). 5. Mise à jour des statuts (traitée, en cours...).

4. Architecture technique
   Front-end
   • Bootstrap 5
   • HTML5 / CSS3
   • JQuery (optionnel)
   • Formulaires responsives
   • Composants Bootstrap (modals, cards, alerts, tables...)
   Back-end
   • PHP 7+
   • Programmation orientée objet (POO)
   Base de données : MySQL
   Tables principales :
1. users (id, nom, email, rôle...)
1. demandes (id, type, description, urgence, statut...)
1. validation (id, demande_id, validateur_id, commentaire, date)
1. types_besoins
1. pieces_jointes
1. Workflow du processus
1. Demandeur crée une demande
1. Système envoie au chef hiérarchique
1. Chef valide / rejette
1. o Si validée → envoyée à l’administrateur
   o Si rejetée → retour demandeur
1. Administrateur traite la demande
1. Demande marquée comme terminée
1. Demandeur reçoit la notification
