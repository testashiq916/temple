-- ===========================================
-- STAFF & VOLUNTEERS MANAGEMENT
-- ===========================================

-- Staff Positions
CREATE TABLE staff_positions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    sanskrit_name VARCHAR(255),
    description TEXT,
    salary_range_min DECIMAL(10,2),
    salary_range_max DECIMAL(10,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);

-- Staff
CREATE TABLE staff (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    staff_id VARCHAR(50) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED,
    position_id BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    sanskrit_name VARCHAR(255),
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE,
    email VARCHAR(255),
    mobile VARCHAR(20) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    qualification VARCHAR(255),
    experience_years INT DEFAULT 0,
    profile_image VARCHAR(255),
    joining_date DATE,
    employee_type ENUM('permanent', 'contract', 'temporary', 'volunteer') DEFAULT 'permanent',
    basic_salary DECIMAL(10,2),
    allowances JSON,
    status ENUM('active', 'inactive', 'on_leave', 'resigned', 'terminated') DEFAULT 'active',
    bank_name VARCHAR(255),
    bank_account_number VARCHAR(50),
    bank_ifsc VARCHAR(20),
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (position_id) REFERENCES staff_positions(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Volunteers
CREATE TABLE volunteers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    volunteer_id VARCHAR(50) UNIQUE NOT NULL,
    devotee_id BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    mobile VARCHAR(20) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    skills JSON,
    availability JSON,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    join_date DATE,
    total_hours INT DEFAULT 0,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (devotee_id) REFERENCES devotees(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);