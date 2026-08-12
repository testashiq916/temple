-- ===========================================
-- DONATION & E-HUNDI MANAGEMENT
-- ===========================================

-- Donation Categories
CREATE TABLE donation_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    sanskrit_name VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);

-- Donors
CREATE TABLE donors (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    donor_id VARCHAR(50) UNIQUE NOT NULL,
    devotee_id BIGINT UNSIGNED,
    full_name VARCHAR(255) NOT NULL,
    sanskrit_name VARCHAR(255),
    email VARCHAR(255),
    mobile VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    donor_type ENUM('individual', 'corporate', 'trust', 'nri') DEFAULT 'individual',
    pan_card VARCHAR(50),
    tax_exempt BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE SET NULL
);

-- Donations
CREATE TABLE donations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    donor_id BIGINT UNSIGNED NOT NULL,
    devotee_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    donation_id VARCHAR(50) UNIQUE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    donation_date DATE NOT NULL,
    donation_type ENUM('cash', 'digital', 'gold', 'silver', 'kind') DEFAULT 'cash',
    payment_method ENUM('cash', 'upi', 'card', 'netbanking', 'cheque', 'digital_gold', 'hundi') DEFAULT 'cash',
    transaction_id VARCHAR(255),
    is_anonymous BOOLEAN DEFAULT FALSE,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurrence_pattern JSON,
    purpose VARCHAR(255),
    notes TEXT,
    status ENUM