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
└── node
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

### Node

The Node service is used exclusively for frontend asset management during development and build time.
It runs Vite and Tailwind CSS to compile and bundle CSS and JavaScript assets for the Laravel application.
This service is running only locally and not required in production, as only the compiled assets are deployed.
Using a dedicated Node container keeps the PHP application lightweight and ensures a clean separation between backend logic and frontend tooling.

use the following command to enter into the node container and after that execute npm command needed locally.

```
make npm-shell
```

Locally on dev environment we don't need to run `make npm-dev` because the node container running.

Vite [dashboard](http://localhost:5173)

Behavior:

- CSS and JS are served from memory by Vite (http://localhost:5173).
- Laravel includes these files via @vite(...).
- Nothing is written to public/build.
- node_modules is required locally but should not be versioned.

For production, we run `make npm-build` to compile files in laravel
Behavior:

- Compiled CSS and JS are written to public/build.
- Laravel serves the files from public/build.
- node_modules remains outside public/ and should not be versioned.
- Only public/build is deployed to production.

---

## 🟢 Who does what

### 🟩 Laravel — Main Backend

Laravel is the **master system** that:

- manages the **business domain**
- exposes the **main APIs** - Movies / TV showd save file from NAS
- serves the backend UI

Responsibilities:

- Authentication & users
- Financial information about my family - stats by categories and see all transactions - import transactions from txt file (ING transactions export) - update transactions by categories - Little logic that save transactions in the good category during import.
- Movies / TV shows (API + admin panel) - Show Movies and TV shows library - Assign movie/tv show info from TMDB for the good movie title

- Blog (posts / categories / tags)
- External API calls (TMDB, legal age, etc.)
- Business logic validation

Laravel uses Eloquent, migrations, seeders, and policies. Its main database stores all the business tables.

#### Blog Posts

For this feature, i have installed the livewire service from laravel to create component that manages js frontend for the posts backend management.
I have created a livewire component to list all posts and filter by a search input and sort by column name.
I have used also livewire to create / update post with livewire form.

##### Tables impacted

- `myhome_post_term`
- `myhome_posts`
- `myhome_terms`

##### Frontend

I have created a new template with tailwindcss and vite for the blog posts frontend website.
So need to have a container node with npm to install tailwind and generate css.
There is also a vite server installed on the node container that refresh page every changes that we do on the laravel template for blog.
This server is available only on dev environment and need to follow the production process to deploy it.

##### Import posts from Wordpress

I have created one command in laravel that import all posts from Wordpress to laravel.
Be carrefull all datas will be truncated before.
This tool also download all pictures from blog wordpress to the `public/storage/images/posts`

```
php artisan import:wordpress
```

#### Finance

#### Movies / Series

#### Friends

It's a feature that manages all my friends and family people that i store the birthdate to never forget to wish an happy birthday !

##### Tables impacted

- `myhome_friend_groups`
- `myhome_friends`

I have created one command in laravel that import all posts from old Laravel tables to yhe new laravel.
Be carrefull all datas will be truncated before.

```
php artisan import:friends
```

I have also used a livewire component to list all friends and filter by a search input.
It's also possible to sort by column name.
I have used also livewire to create / update friends.

#### Backup

It's a feature that used the artisan bakcup run to create backup, restore it and download zip file.
there is also an automatic backup done before import transactions from bank text file for example.

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
