# 🛠️ Mam Fachowca

**Mam Fachowca** is a full-stack service marketplace platform connecting users with professional service providers. Built with **Vue 3**, **Laravel**, **Tailwind CSS**, and containerized using **Docker**.

---

## 🌐 Live Application

- 🔗 **Frontend**: [mamfachowca.mosioneklukasz.pl](http://mamfachowca.mosioneklukasz.pl)
- 🔗 **Backend API**: [API Docs](http://api.mamfachowca.mosioneklukasz.pl/docs/api)

---

## ⚙️ Tech Stack

| Layer       | Technology                     |
|-------------|--------------------------------|
| Frontend    | Vue 3, Vite, Tailwind CSS      |
| Backend     | Laravel 12 (PHP 8.2.12), REST  |
| Styling     | Tailwind CSS                   |
| Database    | MySQL / Sqlite                 |
| DevOps      | Docker, Laravel Queue Worker   |
| Auth        | Laravel Sanctum (Token-based)  |

---

## 📦 Repositories

| Name     | Link |
|----------|------|
| 🖥️ Frontend  | [mamfachowca_FRONTEND_Vue3](https://github.com/lukaszmosionek/mamfachowca_FRONTEND_Vue3) |
| 🔧 Backend   | [mamfachowca_BACKEND_LaravelAPI](https://github.com/lukaszmosionek/mamfachowca_BACKEND_LaravelAPI) |
| 🐳 Docker    | [mamfachowca_Docker](https://github.com/lukaszmosionek/mamfachowca_Docker.git) |

---

## 🧰 Standard Local Installation

### ✅ Requirements

- **PHP**: `8.2.12`
- **Composer**
- **Node.js**: `v22.12.0`
- **npm**: `v10.9.0`
- **MySQL / MariaDB**

---

### 🔧 Backend Setup (Laravel API)

```bash
# Clone the repository
git clone https://github.com/lukaszmosionek/mamfachowca_BACKEND_LaravelAPI.git 

# Navigate into the directory
cd mamfachowca_BACKEND_LaravelAPI

# Install PHP dependencies
composer install

# Copy and configure environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# (Optional) Seed database
php artisan db:seed

# Laravel Queue Worker (Optional)
php artisan queue:work

# (Optional) Generate enums in json and vue3
php artisan enums:export

#run for mobile( optional )
php artisan serve --host=0.0.0.0 --port=8000
