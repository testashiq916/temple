-- ===========================================
-- DEVOTEE MANAGEMENT
-- ===========================================

-- Devotee Types
CREATE TABLE devotee_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    sanskrit_name VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);

-- Devotees
CREATE TABLE devotees (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    devotee_id VARCHAR(50) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED,
    devotee_type_id BIGINT UNSIGNED,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    sanskrit_name VARCHAR(255),
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE,
    place_of_birth VARCHAR(100),
    gotra VARCHAR(100),
    rashi VARCHAR(50),
    nakshatra VARCHAR(50),
    nationality VARCHAR(100),
    email VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    alternate_mobile VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    zip_code VARCHAR(20),
    occupation VARCHAR(255),
    employer VARCHAR(255),
    profile_image VARCHAR(255),
    family_members INT DEFAULT 1,
    is_member BOOLEAN DEFAULT FALSE,
    membership_type VARCHAR(50),
    membership_start_date DATE,
    membership_end_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (devotee_type_id) REFERENCES devotee_types(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Devotee Family Members
CREATE TABLE devotee_family (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    devotee_id BIGINT UNSIGNED NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    sanskrit_name VARCHAR(255),
    relationship VARCHAR(50) NOT NULL,
    date_of_birth DATE,
    gotra VARCHAR(100),
    rashi VARCHAR(50),
    nakshatra VARCHAR(50),
    gender ENUM('male', 'female', 'other'),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE CASCADE
);

-- Devotee Documents
CREATE TABLE devotee_documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    devotee_id BIGINT UNSIGNED NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    document_path VARCHAR(255) NOT NULL,
    document_number VARCHAR(100),
    issue_date DATE,
    expiry_date DATE,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by BIGINT UNSIGNED,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id)
);

-- Devotee Preferences
CREATE TABLE devotee_preferences (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    devotee_id BIGINT UNSIGNED NOT NULL,
    preference_type VARCHAR(50) NOT NULL,
    preference_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE CASCADE
);