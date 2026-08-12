-- ===========================================
-- RITUAL & SEVA MANAGEMENT
-- ===========================================

-- Seva/Services Categories
CREATE TABLE seva_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    sanskrit_name VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);

-- Seva/Services
CREATE TABLE seva_services (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    seva_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    sanskrit_name VARCHAR(255),
    description TEXT,
    procedure TEXT,
    duration_minutes INT DEFAULT 30,
    price DECIMAL(10,2),
    gst_rate DECIMAL(5,2) DEFAULT 18,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    max_devotees INT DEFAULT 1,
    min_advance_days INT DEFAULT 0,
    max_advance_days INT DEFAULT 365,
    is_active BOOLEAN DEFAULT TRUE,
    requires_approval BOOLEAN DEFAULT TRUE,
    image_path VARCHAR(255),
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (category_id) REFERENCES seva_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Seva Slots
CREATE TABLE seva_slots (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    seva_id BIGINT UNSIGNED NOT NULL,
    slot_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    capacity INT DEFAULT 1,
    booked_count INT DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seva_id) REFERENCES seva_services(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (seva_id, slot_date, start_time)
);

-- Seva Bookings
CREATE TABLE seva_bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    booking_id VARCHAR(50) UNIQUE NOT NULL,
    devotee_id BIGINT UNSIGNED NOT NULL,
    seva_id BIGINT UNSIGNED NOT NULL,
    slot_id BIGINT UNSIGNED NOT NULL,
    booking_date DATE NOT NULL,
    slot_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    number_of_devotees INT DEFAULT 1,
    devotee_names JSON,
    special_requests TEXT,
    total_amount DECIMAL(10,2),
    discount_amount DECIMAL(10,2) DEFAULT 0,
    tax_amount DECIMAL(10,2),
    net_amount DECIMAL(10,2),
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded', 'failed') DEFAULT 'pending',
    receipt_id BIGINT UNSIGNED,
    voucher_id BIGINT UNSIGNED,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (devotee_id) REFERENCES devotees(id),
    FOREIGN KEY (seva_id) REFERENCES seva_services(id),
    FOREIGN KEY (slot_id) REFERENCES seva_slots(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Prasad/Laddu Booking
CREATE TABLE prasad_bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    booking_id VARCHAR(50) UNIQUE NOT NULL,
    devotee_id BIGINT UNSIGNED NOT NULL,
    prasad_type VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    booking_date DATE NOT NULL,
    collection_date DATE,
    collection_time TIME,
    status ENUM('pending', 'confirmed', 'collected', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (devotee_id) REFERENCES devotees(id)
);