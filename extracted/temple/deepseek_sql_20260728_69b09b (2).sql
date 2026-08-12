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
    status ENUM('pending', 'received', 'verified', 'refunded') DEFAULT 'pending',
    receipt_id BIGINT UNSIGNED,
    voucher_id BIGINT UNSIGNED,
    verified_by BIGINT UNSIGNED,
    verified_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (donor_id) REFERENCES donors(id),
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES donation_categories(id),
    FOREIGN KEY (receipt_id) REFERENCES receipts(id) ON DELETE SET NULL,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Digital Gold Transactions (E-Gold)
CREATE TABLE digital_gold_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    transaction_id VARCHAR(50) UNIQUE NOT NULL,
    devotee_id BIGINT UNSIGNED,
    donor_id BIGINT UNSIGNED,
    amount DECIMAL(15,2) NOT NULL,
    gold_weight_grams DECIMAL(10,3) NOT NULL,
    gold_purity ENUM('24k', '22k', '18k') DEFAULT '24k',
    transaction_date DATE NOT NULL,
    payment_method VARCHAR(50),
    gateway_response JSON,
    status ENUM('pending', 'success', 'failed', 'redeemed') DEFAULT 'pending',
    redemption_date DATE,
    redeem_to VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE SET NULL,
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE SET NULL
);

-- E-Hundi (Digital Donation Box)
CREATE TABLE e_hundi_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    transaction_id VARCHAR(50) UNIQUE NOT NULL,
    qr_code VARCHAR(255),
    amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('upi', 'card', 'netbanking') DEFAULT 'upi',
    upi_id VARCHAR(100),
    transaction_date DATE NOT NULL,
    gateway_response JSON,
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    donation_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE SET NULL
);

-- Receipts
CREATE TABLE receipts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    receipt_no VARCHAR(50) UNIQUE NOT NULL,
    devotee_id BIGINT UNSIGNED,
    donor_id BIGINT UNSIGNED,
    receipt_date DATE NOT NULL,
    receipt_type ENUM('donation', 'seva', 'prasad', 'membership', 'other') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255),
    description TEXT,
    voucher_id BIGINT UNSIGNED,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE SET NULL,
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE SET NULL,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);