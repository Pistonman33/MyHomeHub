# 📘 MyHomeHub — Portfolio Laravel + Python + Docker

**MyHomeHub centralise tous mes projets personnels et techniques : films, séries, finances, blog et anniversaires. C’est un tableau de bord complet, modulable et conçu pour démontrer mes compétences en Laravel, Python et Docker.**

Ce document présente la structure, les choix techniques, les responsabilités des composants et le plan de déploiement de ce projet portfolio public.

---

## 🎯 Objectif du projet

Ce projet est conçu pour :
- **Montrer mes compétences en développement web (Laravel / PHP)**
- **Démontrer ma maîtrise de Python pour des services automatisés**
- **Présenter une architecture évolutive, maintenable et professionnelle**
- **Inclure une mise en production sur VPS Cloud avec Docker**

---

## 🧭 Vue d’ensemble de l’architecture

```
                ┌──────────────┐
                │   Navigateur │
                └──────┬───────┘
                       │
                       ▼
                ┌──────────────┐
                │ Laravel API │
                │ + Backoffice │
                └──────┬───────┘
                       │ HTTP REST
                       ▼
                ┌──────────────┐
                │   Python API │
                │ (services)   │
                └──────┬───────┘
                       │
                       ▼
                   Database
```

---

## 🟢 Qui fait quoi

### 🟩 Laravel — Backend principal

Laravel est le **système maître** qui :
- gère le **domaine métier**
- expose les **APIs principales**
- sert l’UI du backend
- orchestre les appels à Python

Responsabilités :
- Authentification & utilisateurs
- Films / séries (API + backoffice)
- Blog (articles / catégories / tags)
- Finances
- Appels APIs externes (TMDB, âge légal, etc.)
- Validation métier

Laravel utilise Eloquent, migrations, seeders et policies. Sa base de données principale stocke toutes les tables métier.

### Tables Laravel
- `myhome_users`
- `myhome_movies`
- `myhome_series`
- `myhome_info_movie`
- `myhome_info_series`
- `myhome_supports`
- `myhome_categories`
- `myhome_comptes`
- `myhome_records`
- `myhome_taux`
- `myhome_blog_posts` (à créer)
- `myhome_blog_categories` (à créer)
- `myhome_blog_tags` (à créer)
- `myhome_password_resets`
- `myhome_personal_access_tokens`
- `myhome_rappels` (si utilisé pour orchestrer API Python)
- `myhome_groups`

#### Détail tables Blog Laravel à créer
**`myhome_blog_posts`**:
- id (PK)
- title (string)
- slug (string, unique)
- content (text)
- author_id (FK users)
- category_id (FK categories)
- published_at (datetime)
- timestamps

**`myhome_blog_categories`**:
- id (PK)
- name (string)
- slug (string, unique)
- description (text, nullable)
- timestamps

**`myhome_blog_tags`**:
- id (PK)
- name (string)
- slug (string, unique)
- timestamps

**Pivot table `myhome_blog_post_tag`**:
- post_id (FK posts)
- tag_id (FK tags)

---

### 🟨 Python — Ingestion & services transverses

Python est utilisé pour :
- scanner le **NAS**
- faire des traitements asynchrones ou batch
- enrichir des données
- exposer des services spécialisés (ex. anniversaire)

Python :
- n’est **pas** source de vérité
- **ne touche jamais directement la base de données Laravel**
- interagit avec Laravel uniquement via API

Python gère une DB légère (ex. SQLite) pour :
- l’état des scans
- des données techniques temporaires

### Tables Python
- `myhome_amis`
- `myhome_groupe_amis`
- `myhome_rappels`

---

## 📦 Migrations et seeders

### 🟢 Laravel

Laravel **gère ses propres migrations et seeders** pour :
- toutes les tables métier
- les données de test CRUD et logiques

Exemple :
```php
Movie::factory()->count(20)->create();
```

### 🟡 Python

Python a ses propres scripts de mise en place et de peuplement (par exemple `migrate.py`, `seed_amis.py`).

Laravel n’exécute **aucune migration Python directement**.

Seeder Laravel qui appelle l’API Python :
```php
foreach ($fakeAmis as $ami) {
    Http::post('http://python-api/api/amis', $ami);
}
```
- Cela permet de rester fidèle à la séparation des responsabilités.

---

## 🚀 Déploiement — Docker

Le projet s’exécute via Docker Compose :

```
docker-compose.yml
├── reverse-proxy
├── laravel-app
├── python-api
├── db
└── redis
```

Le reverse proxy (Traefik ou Nginx) gère les routes vers Laravel et Python.

---

## 🛠️ Mise en production — VPS OVH

Pour la production, un **serveur VPS Cloud OVH** est recommandé.

### 💡 OVH VPS-1

- 4 vCores  
- 8 Go RAM  
- 75 Go SSD  
- Backup journalier  
- Trafic illimité  
- Bande passante ~400 Mbps  
👉 Convient pour ton projet avec Docker et plusieurs services. ([ovhcloud.com](https://www.ovhcloud.com/fr/vps/))

### 🔎 Alternatives

- **VPS-2** (~6‑7 €/mois) : 6 vCores, 12 Go RAM, NVMe 100 Go, 1 Gbps → recommandé si tu veux plus de ressources
- **VPS-3** (~12‑14 €/mois) : 8 vCores, 24 Go RAM, 200 Go NVMe → excellent pour plusieurs services lourds

### 🔑 Pourquoi ce choix de VPS

- Accès root complet
- Docker + Compose contrôlable
- Backups inclus
- Capable d’héberger :
  - Laravel + PHP
  - Python + API
  - DB SQL
  - Reverse Proxy
- Coût maîtrisé

---

## 📌 Conseils pratiques de production

### Sécurité
- Configurer un firewall
- Activer HTTPS avec Certbot
- Surveiller les backups

### Performances
- Allouer plus de RAM si tu prévois une charge importante
- Surveiller logs & métriques

---

## 📚 Structure du repo

```
portfolio-project/
├── README.md
├── docker-compose.yml
├── laravel/
├── python-api/
└── docs/
```

---

## 🧭 Roadmap

1. Set up VPS OVH
2. Déployer Docker
3. Développer API films en Laravel
4. Développer scan NAS en Python
5. Backoffice Laravel
6. Éventuelle API publique mobile

---

## 📎 Résumé

**MyHomeHub** est une **architecture professionnelle et claire**, capable de :
- être montrée en entretien
- évoluer vers mobile
- être maintenue et déployée facilement
- utiliser Docker et VPS Cloud

