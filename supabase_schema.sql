-- =============================================================================
-- Hotel Booking API — Supabase (PostgreSQL) Schema + Seed Data
-- =============================================================================
-- Run this entire file in Supabase SQL Editor:
-- Dashboard → SQL Editor → New Query → paste → Run
-- =============================================================================

-- Drop existing tables (safe re-run)
DROP TABLE IF EXISTS bookings CASCADE;
DROP TABLE IF EXISTS rooms    CASCADE;
DROP TABLE IF EXISTS users    CASCADE;

-- =============================================================================
-- USERS
-- =============================================================================
CREATE TABLE users (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(100)        NOT NULL,
    email      VARCHAR(150)        NOT NULL UNIQUE,
    password   VARCHAR(255)        NOT NULL,
    phone      VARCHAR(30),
    role       VARCHAR(20)         NOT NULL DEFAULT 'guest'
                   CHECK (role IN ('admin', 'guest')),
    status     VARCHAR(20)         NOT NULL DEFAULT 'active'
                   CHECK (status IN ('active', 'suspended')),
    created_at TIMESTAMP           NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP           NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_users_email  ON users (email);
CREATE INDEX idx_users_role   ON users (role);
CREATE INDEX idx_users_status ON users (status);

-- =============================================================================
-- ROOMS
-- =============================================================================
CREATE TABLE rooms (
    id              SERIAL PRIMARY KEY,
    room_number     VARCHAR(20)         NOT NULL UNIQUE,
    type            VARCHAR(30)         NOT NULL DEFAULT 'single'
                        CHECK (type IN ('single','double','suite','deluxe','presidential')),
    floor           SMALLINT            NOT NULL DEFAULT 1,
    capacity        SMALLINT            NOT NULL DEFAULT 1,
    price_per_night DECIMAL(10,2)       NOT NULL,
    description     TEXT,
    amenities       TEXT,
    image_url       TEXT,
    status          VARCHAR(20)         NOT NULL DEFAULT 'active'
                        CHECK (status IN ('active','inactive','maintenance')),
    created_at      TIMESTAMP           NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP           NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_rooms_type   ON rooms (type);
CREATE INDEX idx_rooms_status ON rooms (status);
CREATE INDEX idx_rooms_price  ON rooms (price_per_night);

-- =============================================================================
-- BOOKINGS
-- =============================================================================
CREATE TABLE bookings (
    id                SERIAL PRIMARY KEY,
    booking_reference VARCHAR(20)    NOT NULL UNIQUE,
    user_id           INTEGER        NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    room_id           INTEGER        NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
    check_in          DATE           NOT NULL,
    check_out         DATE           NOT NULL,
    nights            SMALLINT       NOT NULL DEFAULT 1,
    guests            SMALLINT       NOT NULL DEFAULT 1,
    total_price       DECIMAL(10,2)  NOT NULL,
    status            VARCHAR(20)    NOT NULL DEFAULT 'pending'
                          CHECK (status IN ('pending','confirmed','cancelled','completed')),
    special_requests  TEXT,
    notes             TEXT,
    created_at        TIMESTAMP      NOT NULL DEFAULT NOW(),
    updated_at        TIMESTAMP      NOT NULL DEFAULT NOW(),
    CONSTRAINT chk_dates CHECK (check_out > check_in)
);

CREATE INDEX idx_bookings_user_id   ON bookings (user_id);
CREATE INDEX idx_bookings_room_id   ON bookings (room_id);
CREATE INDEX idx_bookings_status    ON bookings (status);
CREATE INDEX idx_bookings_check_in  ON bookings (check_in);
CREATE INDEX idx_bookings_reference ON bookings (booking_reference);

-- =============================================================================
-- AUTO-UPDATE updated_at via triggers
-- =============================================================================
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER LANGUAGE plpgsql AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;

CREATE TRIGGER trg_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_rooms_updated_at
    BEFORE UPDATE ON rooms
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_bookings_updated_at
    BEFORE UPDATE ON bookings
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- =============================================================================
-- SEED DATA — Users
-- Password for admin:  Admin@1234
-- Password for guests: Guest@1234
-- Hashes generated with password_hash($pass, PASSWORD_BCRYPT)
-- =============================================================================
INSERT INTO users (name, email, password, phone, role, status) VALUES
('Super Admin',    'admin@hotelbooking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-000-0001', 'admin', 'active'),
('Alice Johnson',  'guest1@example.com',    '$2y$10$TKh8H1.PfYif.cg/C75WOuRohbRp0ZjUJBGRNOrSNa1tPHBkHyRhC', '+1-555-000-0002', 'guest', 'active'),
('Bob Martinez',   'guest2@example.com',    '$2y$10$TKh8H1.PfYif.cg/C75WOuRohbRp0ZjUJBGRNOrSNa1tPHBkHyRhC', '+1-555-000-0003', 'guest', 'active'),
('Carol Williams', 'guest3@example.com',    '$2y$10$TKh8H1.PfYif.cg/C75WOuRohbRp0ZjUJBGRNOrSNa1tPHBkHyRhC', NULL,              'guest', 'active');

-- =============================================================================
-- SEED DATA — Rooms
-- =============================================================================
INSERT INTO rooms (room_number, type, floor, capacity, price_per_night, description, amenities, status) VALUES
('101', 'single',       1, 1,  89.00,  'Cozy single room with city view.',                   'WiFi, TV, Air Conditioning, En-suite Bathroom',                          'active'),
('102', 'single',       1, 1,  89.00,  'Bright single room with garden view.',               'WiFi, TV, Air Conditioning, En-suite Bathroom',                          'active'),
('201', 'double',       2, 2, 149.00,  'Spacious double room with queen bed.',               'WiFi, TV, Air Conditioning, Mini-bar, En-suite Bathroom',                'active'),
('202', 'double',       2, 2, 149.00,  'Corner double room with panoramic views.',           'WiFi, TV, Air Conditioning, Mini-bar, En-suite Bathroom',                'active'),
('301', 'deluxe',       3, 2, 229.00,  'Deluxe room with king bed and lounge area.',         'WiFi, TV, Air Conditioning, Mini-bar, Safe, En-suite Bathroom',          'active'),
('401', 'suite',        4, 3, 349.00,  'Executive suite with separate living room.',         'WiFi, TV, Air Conditioning, Mini-bar, Safe, Jacuzzi, En-suite Bathroom', 'active'),
('402', 'suite',        4, 4, 399.00,  'Family suite with two bedrooms.',                    'WiFi, TV, Air Conditioning, Mini-bar, Kitchen, En-suite Bathroom',       'active'),
('501', 'presidential', 5, 6, 799.00,  'Presidential suite with panoramic views and butler.','WiFi, TV, Air Conditioning, Mini-bar, Jacuzzi, Kitchen, Butler Service', 'active');

-- =============================================================================
-- Done! Verify with:
-- SELECT * FROM users;
-- SELECT * FROM rooms;
-- =============================================================================
