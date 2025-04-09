## 📘 How to Setup Locally

### 📌 Prerequisites

- Install PHP version 8.2 for Laravel 9
- Install MYSQL 8.0 or higher
- Create Database

### 📦 Steps

**1. Clone the repo:**

```sh
  git clone https://github.com/tech-ninjacoder/ams-core.git
  cd ams-core
```

**2. Install / Update all dependency**

```sh
# FOR FIRST TIME SETUP
  composer install
  php artisan migrate --seed # to run migration script and load the seeders data

# FOR EXISTING SETUP
  composer update
  php artisan migrate # to run any new migration script
```

**2. Run laravel app**

```sh
php artisan serve
```
