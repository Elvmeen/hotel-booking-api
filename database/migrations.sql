-- ============================================================
--  Hotel Booking — Optional Migrations
--  Run these after importing schema.sql if you need to alter
--  the database structure for future updates.
-- ============================================================

-- Example: Add a reviews table (v1.1)
/*
CREATE TABLE IF NOT EXISTS reviews (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    booking_id  INT UNSIGNED    NOT NULL,
    user_id     INT UNSIGNED    NOT NULL,
    room_id     INT UNSIGNED    NOT NULL,
    rating      TINYINT UNSIGNED NOT NULL DEFAULT 5,
    comment     TEXT            DEFAULT NULL,
    created_at  DATETIME        NOT NULL,
    PRIMARY KEY (id),
    KEY idx_reviews_room (room_id),
    KEY idx_reviews_user (user_id),
    CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_user    FOREIGN KEY (user_id)    REFERENCES users (id)    ON DELETE CASCADE,
    CONSTRAINT fk_reviews_room    FOREIGN KEY (room_id)    REFERENCES rooms (id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/
