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
├── traefik                   --> reverse proxy + automatic SSL (Let's Encrypt)
├── nginx                     --> web server for Laravel and static site
├── laravel                   --> PHP-FPM container for the app
├── scheduler                 --> container for Laravel scheduled tasks (`php artisan schedule:work`)
└── grpc-ctt                  --> grpc written on GO that returns player results by license
└── grpc-video                --> grpc written on GO that streams video from the server
└── grpc-client               --> grpc client written on GO that call grpc ctt services
└── grpc-client-ctt-python    --> grpc client written on python that call grpc ctt services with protobuff files
└── grpc-client-video-python  --> grpc client written on python that call grpc video to download backend video by chunks
└── grpc-video-gateway        --> grpc client written on GO that call grpc video to download backen video by chunks and stream it hlc.
├── mysql                     --> database container
└── grpcui                    --> grpc Ui to test grpc
└── mailhog                   --> Mailpit is packed full of features for developers wanting to test SMTP and emails.
└── node.                     --> Node js use with laravel for vite server, npm libraries needed and tailwind css.
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
127.0.0.1       doc.thiebault.test
127.0.0.1       stream.thiebault.test
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

### 🟨 Go

#### Grpc client / server developped on go for microservices.

You can see the result of calling grpc microservices with a client developped on go directly inside the container `myhomehub_grpc-client`.

If error not display in the containers `myhomehub_grpc-client` and `myhomehub_grpc-ctt` launch following command in terminal:

```bash
cd docker/dev && docker compose --progress=plain build grpc-ctt --no-cache 2>&1 | tail -30
```

If a container crashed and need to be inside to chek file:

```bash
cd docker/dev && docker compose run --rm --entrypoint sh grpc-client-python
```

#### Grpc client / server developped on go for microservices with streaming files.

You can see a streamin example with go stream grpc video on the following [url](https://thiebault.test/test_stream_grpc.html).

### 🟨 Python

#### Grpc client developped on python for microservices interact and protobuff on go.

You can see the result of calling grpc microservices written in go (server) with a client developped on python and same proto from server directly inside the container `myhomehub_grpc-client-ctt-python`.

You can see the results of calling grpc streaaming video with chunks with a client developped on python and same proto from server directly inside the container `myhomehub_grpc-client-video-python`.

# Production Infrastructure

## Overview

Production infrastructure is fully Dockerized and deployed on an OVH VPS.

The stack is composed of:

- **Traefik** → Reverse proxy + HTTPS termination
- **Nginx** → Multi-site web server
- **Laravel PHP-FPM** → PHP runtime only
- **Scheduler** → Laravel cron/scheduler worker
- **MySQL** → Database
- **GitHub Actions** → CI/CD pipeline
- **GHCR** → Docker image registry

---

# Global Production Architecture

```text
                            INTERNET
                                |
                                v
                    +----------------------+
                    |       Traefik        |
                    |----------------------|
                    | - HTTPS termination  |
                    | - Let's Encrypt SSL  |
                    | - Domain routing     |
                    +----------------------+
                                |
                                v
                    +----------------------+
                    |        Nginx         |
                    |----------------------|
                    | Multi-site webserver |
                    |                      |
                    | thiebault.be         |
                    | -> Static website    |
                    |                      |
                    | myhome.thiebault.be  |
                    | -> Laravel public/   |
                    +----------------------+
                             |       |
             Static files <--+       +--> PHP requests
                                     |
                                     v
                          +------------------+
                          | Laravel PHP-FPM |
                          |------------------|
                          | Executes PHP only|
                          | No static files  |
                          +------------------+
                                     |
                                     v
                          +------------------+
                          |      MySQL       |
                          +------------------+
```

---

# Understanding Each Component

---

## 1. Traefik

Traefik is the public entrypoint of the infrastructure.

Responsibilities:

- HTTPS termination
- Automatic Let's Encrypt certificates
- Domain routing
- HTTP → HTTPS redirection
- Docker service discovery

Traefik is **NOT** a web server.

It does NOT:

- execute PHP
- serve Laravel assets
- serve static files directly

Traefik only forwards requests to the correct container.

---

## 2. Nginx (Multi-Site Web Server)

Nginx is the actual web server.

Responsibilities:

- Serve static files
- Serve Laravel public assets
- Forward PHP requests to PHP-FPM
- Handle multi-site configuration

The same Nginx container handles:

### Static website

```text
thiebault.be
-> /var/www/static
```

### Laravel application

```text
myhome.thiebault.be
-> /var/www/public
```

Nginx uses multiple `server {}` blocks (virtual hosts).

Example:

```nginx
server {
    server_name thiebault.be;
    root /var/www/static;
}

server {
    server_name myhome.thiebault.be;
    root /var/www/public;
}
```

---

# IMPORTANT: Nginx vs PHP-FPM

One of the most important concepts of the architecture:

## Nginx serves files

Examples:

```text
/build/assets/app.css
/images/logo.png
/favicon.ico
```

Nginx serves these files directly from disk.

---

## PHP-FPM executes PHP only

PHP-FPM does NOT:

- serve CSS
- serve JS
- serve images
- expose HTTP

PHP-FPM only executes Laravel PHP code.

Example flow:

```text
Browser
-> Nginx
-> PHP-FPM
-> Laravel executes
-> HTML response
```

---

# Laravel Nginx Configuration

Final production configuration:

```nginx
server {
    listen 80;
    server_name myhome.thiebault.be blog.thiebault.be media.thiebault.be;

    root /var/www/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;

        fastcgi_pass laravel:9000;

        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_X_FORWARDED_PROTO $http_x_forwarded_proto;
    }
}
```

---

# Docker Compose Production

File:

```text
docker-compose.prod.yml
```

Services:

| Service   | Role                    |
| --------- | ----------------------- |
| Traefik   | Reverse proxy + SSL     |
| Nginx     | Web server              |
| Laravel   | PHP-FPM runtime         |
| Scheduler | Laravel scheduled tasks |
| MySQL     | Database                |

---

# Volumes

Persistent volumes ensure data survives container recreation.

Important volumes:

```yaml
volumes:
  mysql_data:
  traefik_certs:
  laravel_public:
```

---

# Shared Laravel Public Volume

Critical concept:

```yaml
laravel_public:/var/www/public
```

This volume is shared between:

- Laravel container
- Nginx container

Why?

Because:

- Laravel generates Vite assets
- Nginx must serve them

Example:

```text
Laravel builds:
/var/www/public/build/assets/app.css

Nginx serves:
/build/assets/app.css
```

---

# Laravel Production Container

Laravel runs with:

```bash
php-fpm
```

This container:

- executes PHP
- runs Laravel
- exposes port 9000 internally

It does NOT expose HTTP publicly.

---

# Scheduler Container

Separate container for Laravel scheduled tasks:

```bash
php artisan schedule:work
```

This isolates:

- web traffic
- cron jobs

Better stability and scalability.

---

# GitHub Actions CI/CD

Workflow:

```text
.github/workflows/deploy.yml
```

Pipeline:

```text
1. Build Docker images (nginx webserver multi site / laravel php executaion)
2. Run tests
3. Push image to GHCR
4. Deploy on VPS
```

---

# HTTPS / SSL

Handled automatically by Traefik + Let's Encrypt.

Ports exposed:

```text
80  -> ACME challenge / redirect
443 -> HTTPS
```

---

# Backup system

There is a root cron on the VPS that extract from `/var/lib/docker/volumes/myhomehub_laravel_storage/_data` all backups from laravel and store it on `/opt/backups` by rsync cmd.
Need to extract data from the laravle storage volumes managed by docker.

```
0 2 * * * /opt/scripts/export-backups.sh >> /var/log/export-backups.log 2>&1
```

And after that a script on my Nas connect to the vps server and sync backups file on the NAS to get a backup outside from the vps server.
