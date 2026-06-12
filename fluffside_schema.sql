-- ============================================================
-- FLUFFSIDE DATABASE SCHEMA
-- Run this entire file in phpMyAdmin > fluffside_db > SQL tab
-- ============================================================

USE fluffside_db;

-- ── 1. USERS (already exists, just adding missing columns) ────
ALTER TABLE tbl_users
    ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL AFTER dob,
    ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(255) DEFAULT NULL AFTER address,
    ADD COLUMN IF NOT EXISTS role ENUM('User','Admin') NOT NULL DEFAULT 'User' AFTER profile_photo,
    ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER role,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_active;

-- ── 2. PETS ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tbl_pets (
    pet_id       VARCHAR(60)  PRIMARY KEY,           -- slug e.g. 'scout'
    pet_name     VARCHAR(100) NOT NULL,
    breed        VARCHAR(100) DEFAULT NULL,
    animal_type  ENUM('DOG','CAT','RABBIT','HAMSTER','BIRD','OTHER') NOT NULL,
    gender       ENUM('MALE','FEMALE') NOT NULL,
    age_desc     VARCHAR(60)  DEFAULT NULL,           -- e.g. "12 weeks old"
    age_group    ENUM('Young','Adult','Senior') DEFAULT 'Adult',
    image_path   VARCHAR(255) DEFAULT NULL,
    description  TEXT         DEFAULT NULL,
    is_available TINYINT(1)   NOT NULL DEFAULT 1,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- Pet traits (likes, dislikes, personality tags)
CREATE TABLE IF NOT EXISTS tbl_pet_traits (
    trait_id   INT AUTO_INCREMENT PRIMARY KEY,
    pet_id     VARCHAR(60) NOT NULL,
    trait_type ENUM('trait','like','dislike') NOT NULL,
    trait_value VARCHAR(100) NOT NULL,
    FOREIGN KEY (pet_id) REFERENCES tbl_pets(pet_id) ON DELETE CASCADE
);

-- Pet gallery images
CREATE TABLE IF NOT EXISTS tbl_pet_gallery (
    gallery_id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id     VARCHAR(60)  NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT          DEFAULT 0,
    FOREIGN KEY (pet_id) REFERENCES tbl_pets(pet_id) ON DELETE CASCADE
);

-- ── 3. PRODUCTS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tbl_products (
    product_id   INT AUTO_INCREMENT PRIMARY KEY,
    image_path   VARCHAR(255) DEFAULT NULL,
    title        VARCHAR(200) NOT NULL,
    subtitle     VARCHAR(255) DEFAULT NULL,
    description  TEXT         DEFAULT NULL,
    full_description TEXT     DEFAULT NULL,
    price        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    category     VARCHAR(60)  DEFAULT NULL,
    pet_type     VARCHAR(60)  DEFAULT NULL,
    brand        VARCHAR(100) DEFAULT NULL,
    life_stage   VARCHAR(60)  DEFAULT NULL,
    weight_size  VARCHAR(60)  DEFAULT NULL,
    food_form    VARCHAR(60)  DEFAULT NULL,
    storage_type VARCHAR(60)  DEFAULT NULL,
    origin       VARCHAR(100) DEFAULT NULL,
    rating       DECIMAL(3,2) DEFAULT 5.00,
    review_count INT          DEFAULT 0,
    is_active    TINYINT(1)   NOT NULL DEFAULT 1,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- Product gallery
CREATE TABLE IF NOT EXISTS tbl_product_gallery (
    gallery_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT         NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT         DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES tbl_products(product_id) ON DELETE CASCADE
);

-- ── 4. ORDERS ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tbl_orders (
    order_id        INT AUTO_INCREMENT PRIMARY KEY,
    order_number    VARCHAR(20) NOT NULL UNIQUE,      -- e.g. FS-20260609-0001
    user_id         INT         NOT NULL,
    full_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(100) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    address         TEXT         NOT NULL,
    city            VARCHAR(100) NOT NULL,
    zip_code        VARCHAR(10)  NOT NULL,
    payment_method  ENUM('Cash on Delivery','GCash','Credit / Debit Card') NOT NULL DEFAULT 'Cash on Delivery',
    subtotal        DECIMAL(10,2) NOT NULL,
    donation_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount    DECIMAL(10,2) NOT NULL,
    status          ENUM('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    notes           TEXT          DEFAULT NULL,
    ordered_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE RESTRICT
);

-- Order line items
CREATE TABLE IF NOT EXISTS tbl_order_items (
    item_id     INT AUTO_INCREMENT PRIMARY KEY,
    order_id    INT          NOT NULL,
    product_id  INT          NOT NULL,
    product_title VARCHAR(200) NOT NULL,   -- snapshot at time of purchase
    unit_price  DECIMAL(10,2) NOT NULL,    -- snapshot
    quantity    INT          NOT NULL,
    subtotal    DECIMAL(10,2) NOT NULL,    -- unit_price * quantity
    FOREIGN KEY (order_id)   REFERENCES tbl_orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES tbl_products(product_id) ON DELETE RESTRICT
);

-- ── 5. APPLICATIONS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tbl_applications (
    app_id          VARCHAR(20)  PRIMARY KEY,          -- e.g. APP-001
    user_id         INT          NOT NULL,
    pet_id          VARCHAR(60)  NOT NULL,
    app_type        ENUM('Adoption','Foster') NOT NULL,
    status          ENUM('active','completed','rejected') NOT NULL DEFAULT 'active',
    current_step    TINYINT      NOT NULL DEFAULT 1,   -- 1-6
    last_update     TEXT         DEFAULT NULL,
    rejected        TINYINT(1)   NOT NULL DEFAULT 0,
    submitted_at    DATE         NOT NULL,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id)  REFERENCES tbl_pets(pet_id)  ON DELETE CASCADE
);

-- ── 6. MESSAGES ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tbl_messages (
    message_id  INT AUTO_INCREMENT PRIMARY KEY,
    app_id      VARCHAR(20) NOT NULL,
    user_id     INT         NOT NULL,
    sender      ENUM('admin','user') NOT NULL,
    message     TEXT        NOT NULL,
    sent_at     TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id)  REFERENCES tbl_applications(app_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id)       ON DELETE CASCADE
);

-- ── Indexes for common queries ─────────────────────────────────
CREATE INDEX IF NOT EXISTS idx_orders_user      ON tbl_orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status    ON tbl_orders(status);
CREATE INDEX IF NOT EXISTS idx_order_items_ord  ON tbl_order_items(order_id);
CREATE INDEX IF NOT EXISTS idx_apps_user        ON tbl_applications(user_id);
CREATE INDEX IF NOT EXISTS idx_msgs_app         ON tbl_messages(app_id);
CREATE INDEX IF NOT EXISTS idx_pet_traits_pet   ON tbl_pet_traits(pet_id);
