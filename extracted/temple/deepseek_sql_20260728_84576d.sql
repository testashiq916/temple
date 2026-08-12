-- ===========================================
-- SYSTEM LEVEL TABLES
-- ===========================================

CREATE TABLE companies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    zip_code VARCHAR(20),
    timezone VARCHAR(50) DEFAULT 'Asia/Kolkata',
    currency VARCHAR(10) DEFAULT 'INR',
    date_format VARCHAR(20) DEFAULT 'Y-m-d',
    logo_path VARCHAR(255),
    subscription_id BIGINT UNSIGNED,
    subscription_status ENUM('trial', 'active', 'suspended', 'cancelled', 'expired') DEFAULT 'trial',
    subscription_start_date DATE,
    subscription_end_date DATE,
    user_limit INT DEFAULT 50,
    devotee_limit INT DEFAULT 10000,
    storage_limit BIGINT DEFAULT 5368709120,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===========================================
-- TEMPLE MANAGEMENT
-- ===========================================

-- Temples
CREATE TABLE temples (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    sanskrit_name VARCHAR(255),
    description TEXT,
    history TEXT,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    zip_code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    phone VARCHAR(20),
    email VARCHAR(255),
    website VARCHAR(255),
    established_year INT,
    deity_name VARCHAR(255),
    deity_description TEXT,
    temple_type ENUM('jyotirlinga', 'shaktipeeth', 'divyadesam', 'pancha_linga', 'vaishno', 'other') DEFAULT 'other',
    capacity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    profile_image VARCHAR(255),
    banner_image VARCHAR(255),
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Temple Images
CREATE TABLE temple_images (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    temple_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_name VARCHAR(255),
    image_type ENUM('gallery', 'deity', 'event', 'facility') DEFAULT 'gallery',
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (temple_id) REFERENCES temples(id) ON DELETE CASCADE
);

-- Deities
CREATE TABLE deities (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    deity_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    sanskrit_name VARCHAR(255),
    description TEXT,
    avatar VARCHAR(100),
    consort_name VARCHAR(255),
    vehicle VARCHAR(255),
    color VARCHAR(50),
    mantra TEXT,
    significance TEXT,
    image_path VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);

-- Festivals
CREATE TABLE festivals (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    festival_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    sanskrit_name VARCHAR(255),
    description TEXT,
    significance TEXT,
    festival_type ENUM('annual', 'monthly', 'weekly', 'special') DEFAULT 'annual',
    start_date DATE,
    end_date DATE,
    hijri_date VARCHAR(50),
    is_recurring BOOLEAN DEFAULT TRUE,
    recurrence_pattern JSON,
    image_path VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);