## 🚀 Upgrading Laravel 8 to V9

### 💡 Reason

    - PHP 7.4 is already out of life and can't anymore use for development

### 📌 Prerequisites

    - PHP version 8.0 - 8.2
    - MYSQL 8.0 or higher
    - FTP access to your server
    - Created Database, check the .env file for the name

### 🔧 Challenges & Resolutions

**1. FILTER_SANITIZE_STRING is deprecated in PHP8.1, when filter_var(null, FILTER_SANITIZE_STRING) executes it generates error.**

- 🧠: Skip string sanitization for null values and use FILTER_SANITIZE_SPECIAL_CHARS instead of FILTER_SANITIZE_STRING

**2.\-**

- 🧠: \-

---

### 🚀 Upgrading Laravel 9 to V10 - FUTURE PLAN

---

### 🚀 Upgrading Laravel 10 to V1 - FUTURE PLAN
