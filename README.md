# 💸 Expense Tracker

A Laravel 12 + Inertia + Vue 3 application for tracking personal expenses by category.

## ✅ Requirements

- PHP 8.2+
- Composer 2+
- Node.js 22+
- pnpm 10+
- SQLite (default) or another supported Laravel database

## 📦 Install pnpm

Use this command on macOS, Windows, or Linux:

```bash
npm install -g pnpm@latest-10
```

## 🛠️ Local Installation

1. Install backend dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
pnpm install
```

3. Create environment file:

```bash
cp .env.example .env
```

4. Generate app key:

```bash
php artisan key:generate
```

5. Run migrations and seeders:

```bash
php artisan migrate --seed
```

## ▶️ Run the Project Locally

Run everything with one command:

```bash
composer run dev
```

Open the app at: `http://127.0.0.1:8000`

## 🧰 Useful Commands

- Run tests:

```bash
php artisan test --compact
```

- Lint frontend:

```bash
pnpm run lint:check
```

- Auto-fix frontend lint issues:

```bash
pnpm run lint
```

- Format frontend files:

```bash
pnpm run format
```

- Build frontend assets:

```bash
pnpm run build
```
