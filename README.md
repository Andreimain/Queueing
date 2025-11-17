# Queueing System (Laravel)

A simple and efficient **multi-office queueing system** built with **Laravel**, designed for walk‑in visitor registration and office-based queue management.

This system assigns ticket numbers per office, resets queues daily, and allows staff to manage visitors smoothly.

---

## 📌 Features

### 🧑‍💼 Visitor Module

* Select an office upon registration
* Receives an auto-generated ticket number
* Ticket numbers follow office-specific prefixes (e.g., `BO-001`, `LB-001`)
* Queue numbers reset daily

### 🏢 Staff Module

* View the current queue for their assigned office
* See the currently serving visitor
* Serve the next visitor
* Skip a visitor
* Mark a visitor as done

### 🛠 Admin Module

* Monitor all office queues in one dashboard
* View queue activity

---

## 🔧 Tech Stack

* **Laravel**
* **PHP** (>= 8.1)
* **Tailwind CSS**
* **MySQL or SQLite**

---

## 🔄 Queue Workflow

1. Visitor chooses an office
2. System generates a ticket number based on the office prefix
3. Ticket joins the office's queue
4. Staff manages:

   * Serve next visitor
   * Skip a visitor
   * Mark as done
5. Daily, queue numbers automatically reset while keeping history

---

## 🧠 Core Logic Overview

### Ticket Generation

* Finds the latest ticket for the selected office for **today**
* Increments the number
* Prepends the office prefix

### Queue Management

* Serving the next visitor changes their status to `serving`
* Skipping moves them to `skipped`
* Completed visitors become `done`
* Only one ticket can have `serving` status per office

---

## 🚀 Installation

Follow these steps to set up the application:

### 1. Clone the Repository

```bash
git clone <repo-url>
cd queueing-system
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the default environment file:

**Windows (PowerShell):**

```bash
copy .env.example .env
```

**Linux/Mac:**

```bash
cp .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Configure Database

Open your `.env` file and set your database values:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=queue_system_db
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### 7. Run Database Migrations

```bash
php artisan migrate
```

This will create the required tables such as:

* offices
* tickets
* users (if authentication is enabled)

### 8. Seed Database (Optional)

```bash
php artisan db:seed
```

This will create the admin user: `queue_admin@example.com` and password `123456`.

### 9. Build Frontend Assets

**For production:**

```bash
npm run build
```

**For development (with Vite hot reload):**

```bash
npm run dev
```

---

**Access the Application**

1. Preview of Office Queues: http://localhost:8000
2. Enter Queueing: http://localhost:8000/register-queue
3. Login: http://localhost:8000/login
