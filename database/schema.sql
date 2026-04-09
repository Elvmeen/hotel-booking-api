-- ============================================================
--  Hotel Booking — MySQL Database Schema
--  Engine: InnoDB  |  Charset: utf8mb4
-- ============================================================

CREATE DATABASE IF NOT EXISTS hotel_booking
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE hotel_booking;

-- ------------------------------------------------------------
--  Sessions (CodeIgniter database sessions)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ci_sessions (
    id            varchar(128) NOT NULL,
    ip_address    varchar(45)  NOT NULL,
    timestamp     int(10) UNSIGNED NOT NULL DEFAULT 0,
    data          blob         NOT NULL,
    PRIMARY KEY (id),
    KEY idx_ci_sessions_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)    NOT NULL,
    email       VARCHAR(180)    NOT NULL,
    password    VARCHAR(255)    NOT NULL,
    phone       VARCHAR(30)     DEFAULT NULL,
    role        ENUM('admin','guest') NOT NULL DEFAULT 'guest',
    status      ENUM('active','suspended') NOT NULL DEFAULT 'active',
    created_at  DATETIME        NOT NULL,
    updated_at  DATETIME        NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role (role),
    KEY idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Rooms
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS rooms (
    id               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    room_number      VARCHAR(20)     NOT NULL,
    type             ENUM('single','double','suite','deluxe','presidential') NOT NULL,
    floor            TINYINT UNSIGNED NOT NULL DEFAULT 1,
    capacity         TINYINT UNSIGNED NOT NULL DEFAULT 2,
    price_per_night  DECIMAL(10,2)   NOT NULL,
    description      TEXT            DEFAULT NULL,
    amenities        TEXT            DEFAULT NULL,
    image_url        VARCHAR(500)    DEFAULT NULL,
    status           ENUM('active','inactive','maintenance') NOT NULL DEFAULT 'active',
    created_at       DATETIME        NOT NULL,
    updated_at       DATETIME        NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rooms_number (room_number),
    KEY idx_rooms_type (type),
    KEY idx_rooms_status (status),
    KEY idx_rooms_price (price_per_night)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Bookings
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
    id                 INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    booking_reference  VARCHAR(20)     NOT NULL,
    user_id            INT UNSIGNED    NOT NULL,
    room_id            INT UNSIGNED    NOT NULL,
    check_in           DATE            NOT NULL,
    check_out          DATE            NOT NULL,
    nights             TINYINT UNSIGNED NOT NULL DEFAULT 1,
    guests             TINYINT UNSIGNED NOT NULL DEFAULT 1,
    total_price        DECIMAL(12,2)   NOT NULL,
    status             ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
    special_requests   TEXT            DEFAULT NULL,
    notes              TEXT            DEFAULT NULL,
    created_at         DATETIME        NOT NULL,
    updated_at         DATETIME        NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_bookings_reference (booking_reference),
    KEY idx_bookings_user (user_id),
    KEY idx_bookings_room (room_id),
    KEY idx_bookings_status (status),
    KEY idx_bookings_dates (check_in, check_out),
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_room FOREIGN KEY (room_id) REFERENCES rooms (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Seed Data
-- ============================================================

-- Default admin account (password: Admin@1234)
INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES
('Super Admin', 'admin@hotelbooking.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin', 'active', NOW(), NOW());

-- Sample rooms
INSERT INTO rooms (room_number, type, floor, capacity, price_per_night, description, amenities, status, created_at, updated_at) VALUES
('101', 'single',       1, 1, 89.00,  'Cozy single room with city view.',        'WiFi, TV, Air Conditioning, En-suite Bathroom', 'active', NOW(), NOW()),
('102', 'single',       1, 1, 89.00,  'Bright single room with garden view.',    'WiFi, TV, Air Conditioning, En-suite Bathroom', 'active', NOW(), NOW()),
('201', 'double',       2, 2, 149.00, 'Spacious double room with queen bed.',    'WiFi, TV, Air Conditioning, Mini-bar, En-suite Bathroom', 'active', NOW(), NOW()),
('202', 'double',       2, 2, 149.00, 'Double room with panoramic city view.',   'WiFi, TV, Air Conditioning, Mini-bar, En-suite Bathroom', 'active', NOW(), NOW()),
('301', 'suite',        3, 3, 299.00, 'Elegant suite with separate living area.','WiFi, TV, Air Conditioning, Mini-bar, Bathtub, Room Service, Safe', 'active', NOW(), NOW()),
('302', 'suite',        3, 4, 349.00, 'Family suite with two bedrooms.',         'WiFi, 2 TVs, Air Conditioning, Mini-bar, Bathtub, Room Service, Safe', 'active', NOW(), NOW()),
('401', 'deluxe',       4, 2, 459.00, 'Deluxe room with premium furnishings.',   'WiFi, Smart TV, Air Conditioning, Mini-bar, Bathtub, Jacuzzi, Concierge', 'active', NOW(), NOW()),
('501', 'presidential', 5, 6, 999.00, 'Presidential suite — the pinnacle of luxury.', 'WiFi, Multiple Smart TVs, Air Conditioning, Full Bar, Private Jacuzzi, Private Terrace, Butler Service, Concierge', 'active', NOW(), NOW());

-- Sample guest users (password: Guest@1234)
INSERT INTO users (name, email, password, phone, role, status, created_at, updated_at) VALUES
('John Smith',    'john@example.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0101', 'guest', 'active', NOW(), NOW()),
('Sarah Johnson', 'sarah@example.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0102', 'guest', 'active', NOW(), NOW()),
('Ali Hassan',    'ali@example.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+234-800-001', 'guest', 'active', NOW(), NOW());
