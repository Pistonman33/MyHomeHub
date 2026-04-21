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
make laravel-shell
make db
```

Services:

```
docker-compose.yml
├── traefik       --> reverse proxy + automatic SSL (Let's Encrypt)
├── nginx         --> web server for Laravel and static site
├── laravel       --> PHP-FPM container for the app
├── scheduler     --> container for Laravel scheduled tasks (`php artisan schedule:work`)
├── mysql         --> database container
└── grpc-ctt      --> grpc written on GO that returns player results by license
└── grpc-client   --> grpc client witten on GO that call grpc ctt services
└── grpcui        --> grpc Ui to test grpc
└── mailhog       --> Mailpit is packed full of features for developers wanting to test SMTP and emails.
└── node.         --> Node js use with laravel for vite server, npm libraries needed and tailwind css.
```

### Traefik

Reverse proxy service that manages routes and certificates.
Need to add following records on /etc/hosts for web domain

```
127.0.0.1       traefik.thiebault.test
127.0.0.1       myhome.thiebault.test
127.0.0.1       mailhog.thiebault.test
127.0.0.1       thiebault.test
127.0.0.1       blog.thiebault.test
127.0.0.1       media.thiebault.test
127.0.0.1       go.thiebault.test
127.0.0.1       doc.thiebault.test
```

Traefik [dashboard](https://traefik.thiebault.test/dashboard/#/)

### Nginx

In this project, Nginx acts as the web server in front of the Laravel application and also manage the static website html.

Laravel itself does not serve HTTP requests directly. It requires a web server to:

- Serve static files (CSS, JS, images)
- Forward PHP requests to PHP-FPM
- Define the correct web root (/public)

Nginx webserver manages the static html website.

### Laravel

In dev environment the log are sent to the docker logs with this configuration

```
LOG_CHANNEL=stderr
LOG_LEVEL=debug
```

or this config to store error message on the laravel log file of the day in the storage folder.

```
LOG_CHANNEL=daily
LOG_LEVEL=debug
```

All environment variable is on the root of the project and not of laravel.

#### Laravel Scheduler in Docker (`schedule:work`)

In this project, the Laravel task scheduler has own **Docker container** using `schedule:work`.  
This means there is **no need to set up a separate Linux cron job**.
This turn every minute.
To check the list of schedule:

```bash
php artisan schedule:list
```

And logs file are store in /storage/logs/scheduler.log

##### How it works

- The scheduler is defined in `App\Console\Kernel.php` using `Schedule` and your existing commands/callbacks.
- There is a Docker container (scheduler) that launches the scheduler in the background using:

```bash
php artisan schedule:work
```

MyHomeHub [backend](https://myhome.thiebault.test/admin)

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

- MailHog [Mail](https://mailhog.thiebault.test)

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

MyHomeHub [blog](https://blog.thiebault.test)

##### Import posts from Wordpress

I have created one command in laravel that import all posts from Wordpress to laravel.
Be carrefull all datas will be truncated before.
This tool also download all pictures from blog wordpress to the `public/storage/images/posts`

```
php artisan import:wordpress

```

#### Finance

#### Movies / Series

MyHomeHub [media](https://media.thiebault.test)

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

#### Ping pong

It's a feature that import all ping pong results from me from ctt api and present result on a nice dashboard

##### Tables impacted

- `myhome_ctt_matches`
- `myhome_ctt_players`
- `myhome_ctt_seasons`

I have created one command in laravel that import all results of season and license id in parameter.
It's a sync tool thta adds only new values.

```
php artisan ctt:sync <license_id> <season_year>
```

I have also created a dashboard on the frontend laravel project in the folowing url:

CTT [dashboard](https://myhome.thiebault.test/ctt)

#### Backup

It's a feature that used the artisan bakcup run to create backup, restore it and download zip file.
there is also an automatic backup done before import transactions from bank text file for example.

# Laravel + Docker / GitHub Actions CI/CD Deployment

This document summarizes the production deployment process for a Laravel project using Docker, Traefik, and GitHub Actions with GitHub Container Registry (GHCR).

---

# Production info

## 1. Production Docker Compose

File: `docker-compose.prod.yml`

- **Traefik**: reverse proxy + automatic SSL (Let's Encrypt)
- **Nginx**: web server for Laravel and static site
- **Laravel**: PHP-FPM container for the app
- **Scheduler**: container for Laravel scheduled tasks (`php artisan schedule:work`)
- **MySQL**: database container

### Key Points

- The production `.env` **must NOT be versioned**.
  → It should exist **directly on the VPS**.
- Exposed ports: `80` for ACME challenge (HTTP → HTTPS), `443` for HTTPS.
- Persistent volumes for Laravel and MySQL ensure data is not lost.
- Laravel command in prod: `php-fpm` for main app, `php artisan schedule:work` for scheduler.

---

## 2. Laravel Dockerfile (Production)

- Based on `php:8.3-fpm-alpine`
- Installs PHP extensions required for Laravel
- Copies Laravel app into `/var/www`
- Installs dependencies with `composer install --no-dev --optimize-autoloader`
- Sets proper permissions for `www-data`
- Entrypoint `/entrypoint.sh` handles `storage:link` creation

---

## 3. GitHub Actions CI/CD Workflow

Workflow file: `deploy.yml`

### Steps

1. **Build Docker Image**
   - GitHub Actions builds the Laravel image using the production Dockerfile:

```bash
docker build -t ghcr.io/<OWNER>/myhomehub:latest -f laravel/Dockerfile.prod .

```

Also we can test it locally the build image from prod with the following command:

```bash
docker build -t myhomehub-prod -f docker/laravel/Dockerfile.prod .
```

    - Push to GHCR:

Authenticates to GitHub Container Registry using ${{ secrets.GITHUB_TOKEN }}

Pushes the built image:

```bash
docker push ghcr.io/<OWNER>/myhomehub:latest
```

2. **Tests**

- We build a laravel container with the Dockerfile.ci
- We used the laravel prod build with other permission and composer install with dev depencies for use artisan test command
- Also use another mysql temporaly server for testing.
- Execute all test on laravel defined:

```bash
php artisan config:clear
php artisan migrate
php artisan test
```

3. **Deploy on VPS**

- This step need first satisfy build and test step before!

- SSH into VPS and pull the latest image from GHCR:

```bash
cd /var/www/myhomehub
docker compose pull
docker compose up -d
docker system prune -f
```

- Laravel containers run with the production .env already on the VPS

- Scheduler container runs php artisan schedule:work

- Nginx reads the Laravel container via fastcgi_pass

- GitHub Secrets Used

```
SERVER_IP → VPS IP
SERVER_USER → SSH user
SSH_PRIVATE_KEY → SSH private key
GITHUB_TOKEN → automatically provided by GitHub for GHCR login
```

⚠️ Never commit DB_PASSWORD, APP_KEY, SSH keys, or full .env in a public repo.

- Environment Variables
  Production .env is on the VPS only

CI/CD only uses GitHub secrets for SSH, GHCR login, or dynamic variables

In development, use a local .env with Docker Compose

- Traefik + SSL

Ports 80 and 443 exposed
Let’s Encrypt handles HTTPS automatically
HTTP → HTTPS redirection recommended
Configuration via labels in docker-compose.prod.yml

- Best Practices

Public repo → safe for code, Dockerfiles, Nginx, Traefik configs, but never secrets

Private repo → safer if you store internal scripts or CI logic

Always test locally/dev before deploying to production

Store sensitive secrets outside the repo, either in GitHub Secrets or directly on the VPS

- Vercel-style GHCR Workflow Advantages

No .env in repo → secrets stay safe on VPS

Reproducible builds → image built once in CI, deployed anywhere

Clean separation between build (CI) and runtime (prod)

- Optional Diagram
  +----------------+ +-----------------+ +----------------+
  | | 80/443| | 9000 | |
  | Traefik +------->+ Nginx +------->+ Laravel |
  | (SSL, proxy) | | (PHP FPM proxy) | | PHP-FPM |
  +----------------+ +-----------------+ +----------------+
  | |
  | |
  v v
  Static files /www/html MySQL container

### Go — Grpc client / server developped on go for icroservices.

You can see the result of calling grpc microservices with a client developped on go directly inside the container `myhomehub_grpc-client`.

If error not display in the containers `myhomehub_grpc-client` and `myhomehub_grpc-ctt` launch following command in terminal:

```bash
cd docker/dev && docker compose --progress=plain build grpc-ctt --no-cache 2>&1 | tail -30
```

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
