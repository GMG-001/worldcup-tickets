# World Cup Tickets API

A Laravel REST API for managing World Cup match tickets.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Seed Credentials

All seeded users share the password: **`password`**

### Admin

| Name       | Email                  | Role  |
|------------|------------------------|-------|
| Admin User | admin@worldcup.test    | admin |

### Fans

| Name       | Email                  | Role |
|------------|------------------------|------|
| Fan User 1 | fan1@worldcup.test     | fan  |
| Fan User 2 | fan2@worldcup.test     | fan  |
| Fan User 3 | fan3@worldcup.test     | fan  |
| Fan User 4 | fan4@worldcup.test     | fan  |
| Fan User 5 | fan5@worldcup.test     | fan  |

## Authentication

The API uses Laravel Sanctum (token-based).

```
POST /api/register
POST /api/login
POST /api/logout
GET  /api/me
```

Include the token in subsequent requests:
```
Authorization: Bearer <token>
```