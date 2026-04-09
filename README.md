# Hotel Booking API

A full-featured hotel booking REST API built with **CodeIgniter 3** and **MySQL**. Includes JWT authentication, role-based access control (admin / guest), and a server-rendered admin dashboard.

---

## Features

- **REST API** for rooms, bookings, and users
- **JWT Authentication** (stateless, HS256)
- **Role-based Authorization** — admin and guest roles
- **Admin Dashboard** — session-secured web interface
- **MySQL** database with proper indices and foreign keys
- **CORS** support for frontend integration
- Clean, well-structured CodeIgniter 3 application layout

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | 7.4 or higher |
| MySQL | 5.7 or higher |
| Apache | with `mod_rewrite` |
| Composer | 2.x |

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/Elvmeen/hotel-booking-api.git
cd hotel-booking-api
```

### 2. Install dependencies (downloads CodeIgniter 3)

```bash
composer install
```

> Composer will download CodeIgniter 3 and symlink the `system/` directory automatically.

### 3. Configure the database

Edit `application/config/database.php` and update the credentials:

```php
$db['default'] = [
    'hostname' => 'localhost',
    'username' => 'your_db_user',
    'password' => 'your_db_password',
    'database' => 'hotel_booking',
    // ...
];
```

Alternatively, set environment variables:

```bash
export DB_HOST=localhost
export DB_USER=root
export DB_PASS=secret
export DB_NAME=hotel_booking
```

### 4. Import the database schema

```bash
mysql -u root -p < database/schema.sql
```

This creates the `hotel_booking` database, all tables, and seed data including:

- **Admin account** — `admin@hotelbooking.com` / `Admin@1234`
- **Sample guest accounts** — `john@example.com`, `sarah@example.com`, `ali@example.com` (all with password `Guest@1234`)
- **8 sample rooms** — single, double, suite, deluxe, presidential

### 5. Configure CodeIgniter

In `application/config/config.php`:

```php
$config['base_url'] = 'http://localhost/hotel-booking-api/';
$config['encryption_key'] = 'your-random-32-character-key';
$config['jwt_secret_key'] = 'your-secure-jwt-secret-key';
```

### 6. Web server

**Apache:** Enable `mod_rewrite`. The `.htaccess` file handles URL rewriting.

**Nginx:** Add a `try_files` rewrite rule:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

---

## API Reference

All API responses follow a consistent JSON envelope:

```json
{
  "success": true,
  "message": "Success",
  "data": { ... }
}
```

### Authentication

| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `POST` | `/api/auth/register` | Register a new guest account | Public |
| `POST` | `/api/auth/login` | Login and receive a JWT token | Public |
| `POST` | `/api/auth/logout` | Logout (client discards token) | — |
| `GET` | `/api/auth/me` | Get authenticated user info | JWT |

#### Register

```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "phone": "+1-555-9999"
}
```

#### Login

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@hotelbooking.com",
  "password": "Admin@1234"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
    "user": { "id": 1, "name": "Super Admin", "role": "admin" }
  }
}
```

Pass the token in subsequent requests:

```http
Authorization: Bearer <token>
```

---

### Rooms

| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `GET` | `/api/rooms` | List all rooms | Public |
| `GET` | `/api/rooms/{id}` | Get a room | Public |
| `GET` | `/api/rooms/available` | Available rooms for dates | Public |
| `POST` | `/api/rooms` | Create a room | Admin |
| `PUT` | `/api/rooms/{id}` | Update a room | Admin |
| `DELETE` | `/api/rooms/{id}` | Delete a room | Admin |

**Query parameters for `GET /api/rooms`:**

| Param | Type | Description |
|---|---|---|
| `page` | int | Page number (default 1) |
| `per_page` | int | Items per page (default 15, max 100) |
| `type` | string | `single`, `double`, `suite`, `deluxe`, `presidential` |
| `status` | string | `active`, `inactive`, `maintenance` |
| `min_price` | float | Minimum price per night |
| `max_price` | float | Maximum price per night |
| `capacity` | int | Minimum capacity |
| `search` | string | Search room number or description |

**Query parameters for `GET /api/rooms/available`:**

| Param | Type | Required | Description |
|---|---|---|---|
| `check_in` | date | Yes | Check-in date (`YYYY-MM-DD`) |
| `check_out` | date | Yes | Check-out date (`YYYY-MM-DD`) |
| `type` | string | No | Filter by room type |

**Create/Update room body:**

```json
{
  "room_number": "205",
  "type": "double",
  "floor": 2,
  "capacity": 2,
  "price_per_night": 149.00,
  "description": "Spacious double room with sea view.",
  "amenities": "WiFi, TV, Air Conditioning, Mini-bar",
  "status": "active"
}
```

---

### Bookings

| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `GET` | `/api/bookings` | List bookings | JWT (guests see own) |
| `GET` | `/api/bookings/{id}` | Get a booking | JWT |
| `POST` | `/api/bookings` | Create a booking | JWT |
| `PUT` | `/api/bookings/{id}` | Update status | JWT |
| `DELETE` | `/api/bookings/{id}` | Delete a booking | Admin |

**Create booking body:**

```json
{
  "room_id": 3,
  "check_in": "2025-06-01",
  "check_out": "2025-06-05",
  "guests": 2,
  "special_requests": "Late check-in, high floor preferred"
}
```

**Booking statuses:** `pending` → `confirmed` → `completed` | `cancelled`

Guests can only set their own bookings to `cancelled`. Admins can set any status.

---

### Users (Admin)

| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `GET` | `/api/users` | List all users | Admin |
| `GET` | `/api/users/{id}` | Get user | Admin or self |
| `PUT` | `/api/users/{id}` | Update user | Admin or self |
| `DELETE` | `/api/users/{id}` | Delete user | Admin |

---

## Admin Dashboard

The web admin panel is available at:

```
http://localhost/hotel-booking-api/admin/dashboard
```

Login with the admin credentials from the seed data:
- Email: `admin@hotelbooking.com`
- Password: `Admin@1234`

The dashboard provides:
- Overview with booking stats and revenue figures
- Room management (add, edit, toggle status)
- Booking management (confirm, cancel, complete)
- User management (suspend, activate)

> **Note:** The action buttons in the admin dashboard call the REST API using a JWT token stored in `localStorage`. On first visit, a prompt will appear asking you to paste your JWT token (obtained from `POST /api/auth/login`).

---

## Project Structure

```
hotel-booking-api/
├── application/
│   ├── config/            # CI configuration files
│   ├── controllers/
│   │   ├── api/           # REST API controllers
│   │   │   ├── Auth.php
│   │   │   ├── Rooms.php
│   │   │   ├── Bookings.php
│   │   │   └── Users.php
│   │   ├── Admin.php      # Admin dashboard controller
│   │   └── Welcome.php    # API landing page
│   ├── models/
│   │   ├── User_model.php
│   │   ├── Room_model.php
│   │   └── Booking_model.php
│   ├── libraries/
│   │   └── Base_api.php   # Base REST controller (JWT, CORS, response)
│   ├── helpers/
│   │   └── jwt_helper.php # JWT encode / decode
│   └── views/
│       └── admin/         # Admin dashboard views
├── assets/
│   ├── css/admin.css
│   └── js/admin.js
├── database/
│   ├── schema.sql         # Full schema + seed data
│   └── migrations.sql     # Future migration examples
├── .htaccess
├── composer.json
└── index.php
```

---

## Security Notes

- Change `$config['jwt_secret_key']` and `$config['encryption_key']` before deploying to production.
- Use HTTPS in production; set `$config['rest_force_https'] = TRUE`.
- Rotate the default admin password after the first login.
- Consider adding rate limiting for the `/api/auth/login` endpoint in production.

---

## License

MIT License — free to use, modify, and distribute.
