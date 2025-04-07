# 🚀 How to Deploy Laravel to GitHub & FTP

## 📌 Prerequisites

- PHP version 8.2 for Laravel 9
- MYSQL 8.0 or higher
- FTP access to your server

## ✅ 1. Sync files to the domain directory, deployment are automated by github actions in every PR merge

## ✅ 2. Update .env file , look for APP_URL , change it to your domain name

## ✅ 3. SSH to your server after all files are sync, run the following commands

- For first time setup

```sh
  composer install
  php artisan migrate --seed # to run migration script and load the seeders data
```

- For existing setup

```sh
  composer update
  php artisan migrate # to run any new migration script
```

## ✅ 4.Go to your website and verify changes

- To login with the default user, go to UserTableSeeder
