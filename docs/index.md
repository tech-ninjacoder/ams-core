## 🚀 How to Deploy Laravel to GitHub & FTP

### 📌 Prerequisites

- PHP version 8.2 for Laravel 9
- MYSQL 8.0 or higher
- FTP access to your server
- Create Database
- Create Github Repo
- Add Github Repo Secret

### 📦 Steps

**✅ 1. Commit all changes in github, deployment are automated by github actions in every PR merge**

**✅ 2. Update .env file , look for APP_URL , change it to your domain name**

**✅ 3. SSH to your server after all files are sync, to install/update dependencies**

```sh
# FOR FIRST TIME SETUP
  composer install
  php artisan migrate --seed # to run migration script and load the seeders data

# FOR EXISTING SETUP
  composer update
  php artisan migrate # to run any new migration script
```

**✅ 4.Go to your website and verify changes**

- To login with the default user, go to UserTableSeeder

---

### 💡 How to access Image from storage/app/banners - optional

- Make sure your images are uploaded to: storage/app/public/

- Link storage to public/storage to create a symbolic link:

- Run this Artisan command:

```sh
  php artisan storage:link
```
