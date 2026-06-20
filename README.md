# 🚀 Project Setup (Laravel Sail)

## 📦 Requirements
Make sure you have installed:
- Docker Desktop (must be running)
- Docker Compose (included with Docker Desktop)

👉 Only Docker Desktop needs to be opened manually. Everything else is done via terminal.

---

## 🐳 Run the project


---

### 1. Start Docker Desktop
Open **Docker Desktop** and make sure it is running.

---

### 2. Get the latest version from repository (main branch)

Clone the project from the repository and make sure you are using the latest version from the `main` branch:

```bash
git clone 
cd timesheet-php
git checkout main
git pull origin main
```
---

### 3. Setup environment file

```bash
cp .env.example .env
```

---

### 4. Install dependencies (inside Docker)

```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v $(pwd):/var/www/html \
  -w /var/www/html \
  laravelsail/php83-composer:latest \
  composer install
```

---

### 5. Start the project

```bash
./vendor/bin/sail up -d
```

This will:

Build images if needed
Start all required services (API, database, etc.)

---

### 6. Generate application key

```bash
./vendor/bin/sail artisan key:generate
```

---

### 6. Run database migrations

```bash
./vendor/bin/sail artisan migrate
```

---

### 7. Check running containers

```bash
./vendor/bin/sail ps
```

---

### 8. Access service

Backend API: http://localhost
Database: runs inside Docker network (no manual setup needed)

---

### 9. Background scheduler (auto check-out)

The `scheduler` service in `compose.yaml` runs Laravel's scheduler via `php artisan schedule:work`.
It executes the `attendance:auto-checkout` command every minute to check out users who have been
checked in for more than 8 hours.

When using Sail locally, start it with the rest of the stack:

```bash
./vendor/bin/sail up -d
```

Verify the scheduler container is running:

```bash
./vendor/bin/sail ps
```

For production, ensure a cron entry or supervisor process runs `php artisan schedule:work`
(or `* * * * * php artisan schedule:run`).

---

### 10.🛑 Stop the project

```bash
./vendor/bin/sail down
```

---

## 🔄 Rebuild containers (when backend or compose.yaml changes)

Run:

```bash
./vendor/bin/sail up --build -d
```

---