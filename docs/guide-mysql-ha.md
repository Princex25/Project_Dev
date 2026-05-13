# Guide MySQL Haute Disponibilite

Ce guide presente des options pour rendre la base MySQL plus resiliente et eviter un point de panne unique.

## Definition et objectif

**Haute disponibilite MySQL** signifie que l'application peut continuer a fonctionner meme si un noeud MySQL tombe.
Objectifs dans ce projet:

- reduire les interruptions de service.
- proteger les donnees via replication et sauvegardes.
- offrir une URL de connexion stable au code applicatif.

## Techniques utilisees

- **Replication**: un noeud principal ecrit, des replicas lisent.
- **Failover**: bascule automatique vers un noeud sain.
- **Proxy/Router**: point d'entree unique pour l'application.
- **Backups**: snapshots ou sauvegardes regulieres.

## Options recommandees

### Option A — MySQL manage (simple)

Utiliser un fournisseur gere (ex: PlanetScale, Aiven, RDS, Cloud SQL):

- HA et backups integres.
- Endpoint stable pour `DB_HOST`.
- Maintenance simplifiee.

### Option B — Replication + proxy (auto-heberge)

Architecture type:

- 1 primary + 1 ou plusieurs replicas.
- ProxySQL ou HAProxy en front pour un endpoint unique.
- Supervision et bascule automatique (MHA / Orchestrator).

Role dans le projet: l'application continue a utiliser `DB_HOST` unique (le proxy), sans changement de code majeur.

## Variables d'environnement impactees

L'application utilise les variables suivantes:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

En mode HA, `DB_HOST` doit pointer vers:

- l'endpoint du service manage, ou
- le proxy (HAProxy/ProxySQL) dans un cluster auto-heberge.

## Sauvegardes et restauration

Bonnes pratiques:

- sauvegarde quotidienne (min) de la base.
- tests de restauration reguliers.
- stockage des backups hors du cluster principal.

## Limites

- La haute disponibilite n'evite pas toutes les erreurs humaines (suppression accidentelle, etc.).
- Une vraie HA demande une supervision et des tests de bascule.
- Pour un usage local, Docker Compose reste suffisant.
