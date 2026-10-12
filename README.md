# Plant Nursery Management System

A beginner-friendly plant nursery inventory and purchasing application built with Laravel, MySQL, Blade templates, and Bootstrap 5.

## Features

- Live dashboard for plants, total stock, low stock, categories, suppliers, and purchase records.
- Plant management with search, category filters, stock status, and pagination.
- Category management with safe cascading deletion; categories with plants that have purchase history are protected.
- Supplier management with name search and protection against deleting suppliers with purchase history.
- Purchase records with a supplier, date, status, multiple plant items, and automatically calculated line subtotals and totals.
- Purchase filters by supplier and status.
- Received purchases add their quantities to inventory. Editing or deleting a received purchase safely reverses or reapplies its stock changes in a database transaction.
- Responsive Blade pages, Bootstrap 5, Bootstrap Icons, validation messages, flash alerts, and delete confirmations.
- Repeatable category, plant, supplier, and purchase seed data.

## Database design

The project has exactly five nursery-domain tables:

1. `categories` (`id`, `name`, timestamps) has many `plants`.
2. `suppliers` (`id`, `name`, nullable `contact`, timestamps) has many `purchases`.
3. `plants` (`id`, `name`, `category_id`, `price`, `stock`, timestamps) belongs to a category and appears in many `purchase_items`.
4. `purchases` (`id`, `supplier_id`, `purchase_date`, `total_amount`, `status`, timestamps) belongs to a supplier and has many `purchase_items`.
5. `purchase_items` belongs to a purchase and a plant; it stores the purchased quantity, unit price, and subtotal.

Laravel's framework migrations also create their default `users`, `cache`, `jobs`, session, and related framework tables. These are not nursery-domain tables; the application adds no other project-specific tables.

Purchase statuses are `Pending`, `Received`, and `Cancelled`; the default is `Pending`. Only received purchases update plant stock. Categories and suppliers have cascading foreign keys; purchase items are removed with their purchase.

## Requirements

- PHP 8.3+
- Composer
- MySQL 8 or compatible MariaDB
- Laragon or another local PHP and MySQL environment

## Install and run with Laragon

Start MySQL in Laragon, then open PowerShell:

```powershell
cd C:\laragon\www\plant-nursery
composer install
```

Create the database if it does not exist. Laragon's default MySQL `root` account has an empty password:

```powershell
& 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe' -h 127.0.0.1 -P 3306 -u root -e "CREATE DATABASE IF NOT EXISTS plant_nursery_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Copy `.env.example` to `.env` if necessary. Configure the database settings, and enter a password if your MySQL installation requires one:

```dotenv
APP_NAME="Plant Nursery Management System"
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plant_nursery_db
DB_USERNAME=root
DB_PASSWORD=
```

Generate an application key only if `APP_KEY` is empty, then run migrations and sample data:

```powershell
php artisan key:generate
php artisan migrate
php artisan db:seed
```

To verify a completely fresh schema instead, back up any important database data first, then run:

```powershell
php artisan migrate:fresh --seed
```

`migrate:fresh` drops every table in the configured database before rebuilding it, so use it only when resetting that database is intended.

Start the application:

```powershell
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000). If port 8000 is already in use, run `php artisan serve --port=8001` and open [http://127.0.0.1:8001](http://127.0.0.1:8001).

Run the automated tests:

```powershell
php artisan test
```

The feature tests use a separate in-memory SQLite database. The local demonstration application uses the MySQL settings in `.env`.

## Final project checklist

- [x] Exactly five related nursery-domain tables and their migrations.
- [x] CRUD operations, relationships, input validation, and foreign-key protections.
- [x] Factories and repeatable seeders.
- [x] Responsive Bootstrap 5 and Blade interface.
- [x] Laravel feature tests pass.
- [x] Latest project source and setup guide are pushed to GitHub.
- [ ] Submit the GitHub repository link through your course CASTLY account.
- [x] Ready for the final project demonstration after completing CASTLY submission.

## GitHub and CASTLY submission

GitHub repository: <https://github.com/vrsnzz19-glitch/IPT-MINI-CAPSTONE>

The GitHub repository contains the project source and setup instructions. To finish the course submission, sign in to your institution's CASTLY portal, open the relevant project submission, paste the GitHub repository URL above, and complete its submission confirmation. CASTLY submission must be completed using your own student account.

Do not upload `.env`, database passwords, or other secrets. The `.env.example` file provides safe local configuration defaults.

## Presentation checklist

1. Add, edit, search, filter, and delete a plant.
2. Verify out-of-stock, low-stock, and in-stock statuses and dashboard totals.
3. Add, edit, and delete an empty category; verify a category with plants cannot be deleted.
4. Add, edit, search, and delete a supplier; verify a supplier with purchases cannot be deleted.
5. Create a pending purchase and verify plant stock does not change.
6. Mark it received and verify its plant quantities are added to inventory.
7. Edit or delete a received purchase and verify inventory changes safely.
8. Filter purchases by supplier and status.
