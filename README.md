# 📘 MyHomeHub — Laravel + Python + Docker Portfolio

**MyHomeHub centralizes all my personal and technical projects: movies, series, finances, blog, and birthdays. It is a complete, modular dashboard designed to showcase my skills in Laravel, Python, and Docker.**

This document presents the structure, technical choices, component responsibilities, and deployment plan of this public portfolio project.

---

## 🎯 Project Goal

This project is designed to:
- **Showcase my web development skills (Laravel / PHP)**
- **Demonstrate my proficiency in Python for automated services**
- **Present a scalable, maintainable, and professional architecture**
- **Include production deployment on a Cloud VPS using Docker**

---

## 🧭 Architecture Overview

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

## 🧭 Environment Variables

All environment variables are stored on the `.env` root file for 

```
laravel
MakeFile
```

---
## 🚀 Déploiement — Docker

Execute the project following the environment.
Here the list of commands.

```
make up
make down
make build
make restart
make logs
make ps
```

Services:

```
docker-compose.yml
├── traefik
├── nginx
├── laravel
├── python
├── mysql
└── mailhog
```

### Traefik

Reverse proxy service that manages routes and certificates.
Need to add following records on /etc/hosts for web domain

```
127.0.0.1       traefik.myhome.hub.test
127.0.0.1       myhome.hub.test
127.0.0.1       mailhog.myhome.hub.test
```

Traefik [dashboard](https://traefik.myhome.hub.test/dashboard/#/)


### Nginx

In this project, Nginx acts as the web server in front of the Laravel application.

Laravel itself does not serve HTTP requests directly. It requires a web server to:
- Serve static files (CSS, JS, images)
- Forward PHP requests to PHP-FPM
- Define the correct web root (/public)

### Laravel

In dev environment the log are sent to the docker logs

```
LOG_CHANNEL=stderr
LOG_LEVEL=debug
```

All environment variable is on the root of the project and not of laravel.

MyHomeHub [dashboard](https://myhome.hub.test/)


### Mysql

Laravel project needs mysql database.
There are also command line to manage db:

```
make db
make db-dump FILE=prod-2026-01-15.sql
make db-restore FILE=prod-2026-01-15.sql
```

There is also an `docker/mysql/init` folder that can contains a init backup file launching when we rebuild the mysql container.
All database data are store in `docker/mysql/data`.
And we can do backup and restore with makefile in the folder `docker/mysql/dumps`.

### Mailhog

MailHog is a simple email testing tool used in local development. It captures outgoing emails sent by your application so you can view them in a web interface instead of sending them to real recipients.

Why we use it in this project:

- Allows developers to safely test email functionality (like registration, password reset, notifications) without spamming real inboxes.
- Provides a web interface to easily inspect, search, and debug emails.
- Useful for local development and testing, but not needed in production.

Access (local dev):
- MailHog [Mail](https://mailhog.myhome.hub.test) 

---


## 🟢 Who does what

### 🟩 Laravel — Main Backend

Laravel is the **master system** that:
- manages the **business domain**
- exposes the **main APIs**
       - Movies / TV showd save file from NAS
- serves the backend UI


Responsibilities:
- Authentication & users
- Financial information about my family
       - stats by categories and see all transactions
       - import transactions from txt file (ING transactions export)
       - update transactions by categories
              - Little logic that save transactions in the good category during import.
- Movies / TV shows (API + admin panel)
       - Show Movies and TV shows library 
       - Assign movie/tv show info from TMDB for the good movie title

- Blog (posts / categories / tags)
- External API calls (TMDB, legal age, etc.)
- Business logic validation


Laravel uses Eloquent, migrations, seeders, and policies. Its main database stores all the business tables.

#### Blog Posts

For this feature i have installed the livewire service from laravel to create component that manages js frontend for the posts backen management.


##### Import posts from Wordpress

Here is the query to export all posts from Wordpress to new Laravel blog system.

```
SELECT 
    p.ID AS wordpress_id,
    p.post_title,
    p.post_name AS slug,
    p.post_content,
    p.post_status,
    p.post_date,
    
    -- Catégories séparées par virgule
    GROUP_CONCAT(DISTINCT c.name ORDER BY c.name ASC SEPARATOR ', ') AS categories,
    
    -- Tags séparés par virgule
    GROUP_CONCAT(DISTINCT t.name ORDER BY t.name ASC SEPARATOR ', ') AS tags

FROM rayufat_posts p

-- Catégories
LEFT JOIN rayufat_term_relationships tr_c 
    ON p.ID = tr_c.object_id
LEFT JOIN rayufat_term_taxonomy tt_c 
    ON tr_c.term_taxonomy_id = tt_c.term_taxonomy_id 
    AND tt_c.taxonomy = 'category'
LEFT JOIN rayufat_terms c 
    ON tt_c.term_id = c.term_id

-- Tags
LEFT JOIN rayufat_term_relationships tr_t 
    ON p.ID = tr_t.object_id
LEFT JOIN rayufat_term_taxonomy tt_t 
    ON tr_t.term_taxonomy_id = tt_t.term_taxonomy_id 
    AND tt_t.taxonomy = 'post_tag'
LEFT JOIN rayufat_terms t 
    ON tt_t.term_id = t.term_id

WHERE p.post_type = 'post'
  AND p.post_status IN ('publish', 'draft')

GROUP BY p.ID, p.post_title, p.post_name, p.post_content, p.post_status, p.post_date
ORDER BY p.post_date DESC;
```


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

#TODO Makefile pour mysql php artisan ????