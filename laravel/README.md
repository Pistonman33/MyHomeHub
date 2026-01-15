# MyHome — Personal Dashboard (Movies, Series & Financial Management)

**MyHome** is a private Laravel-based web application designed to centralize and manage various aspects of my personal life, including:

- 🎬 **Movies & TV Series collection**
- 💰 **Personal financial tracking**
- 🗂️ **Personal data and utilities**
- 🏠 **Home dashboard and quick-access tools**

This project is not intended to be public-facing but serves as a secure and organized digital hub for managing my daily information.

---

## 🌐 Overview

MyHome is a custom-built personal platform developed with **Laravel**.  
It provides a clean and simple interface to store, track, and browse information that matters to me on a daily basis.

The application includes:

### 🎥 Movies & TV Series Management
- Add and categorize movies & series  
- Track watched / to-watch items  
- Store metadata (year, genre, rating, etc.)  
- Quick search and filtering  
- Possible integration with APIs (TMDB, IMDB, etc.)

### 💵 Personal Financial Management
- Track expenses, income, and monthly budgets  
- Categorize financial movements  
- Visual reports and summaries  
- Export/import tools (CSV, Excel, etc.)  
- Overview dashboard showing financial health

### 🏠 Home Dashboard
- Centralized view of all modules  
- At-a-glance overview (latest movies, financial stats, reminders…)  
- Custom widgets for quick actions  

---

## 🛠️ Technologies Used

- **Laravel** (PHP Framework)
- **MySQL/MariaDB** for data storage
- **Blade** templating engine
- **Bootstrap/Tailwind** (depending on project version)
- **Custom REST endpoints** for local automation scripts
- **SSH & CLI tools** for server management
- Optional: API integrations (TMDB, bank exports, etc.)

---

## 📁 Repository Structure

Typical structure includes:

- app/ # Laravel application logic
- config/ # Application configuration
- database/ # Migrations, seeds
- public/ # Public assets
- resources/ # Views and frontend resources
- routes/ # Web/API routes
- storage/ # Logs, cache, personal files


This repository contains the full source code of the private app, minus sensitive credentials.

---

## 🚀 Installation (Local)

To install the project locally:

1. Clone the repository:
   ```bash
   git clone git@github.com:Pistonman33/myhome.git


Install dependencies:
```bash
composer install
```

Copy the environment file:

```bash
cp .env.example .env
```

Generate Laravel key:
```bash
php artisan key:generate
```

Configure database settings in .env.

Run migrations:
```bash
php artisan migrate --seed
```

Start the local server:
```bash
php artisan serve
```
## 🚀 Update website
Use the `thiebaul_pull` script to apply changes website. (bash script git pull on OVH)

composer update:
```bash
cd www/myhome/
COMPOSER_MEMORY_LIMIT=-1 ../../composer.phar update 
```
For the movie, we used also the public storage image link, so we need to do that on ovh hosting:
```bash
# aller dans /public
cd public

# supprimer l’ancien symlink
rm storage

# créer un symlink relatif
ln -s ../storage/app/public storage
```
On OVh, right issue with the command `php artisan storage:link`

🔐 Security & Privacy

This project contains strictly personal data.
Nothing in this repository should be exposed publicly.

All API keys, passwords, and financial data are handled through .env files and never stored in the repository.
