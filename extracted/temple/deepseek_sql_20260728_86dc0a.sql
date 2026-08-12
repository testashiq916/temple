-- ===========================================
-- PROPERTY & LAND MANAGEMENT
-- ===========================================

-- Temple Properties
CREATE TABLE temple_properties (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    property_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    property_type ENUM('land', 'building', 'commercial', 'residential', 'agricultural', 'mixed') NOT NULL,
    description TEXT,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    zip_code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    total_area DECIMAL(15,2),
    area_unit VARCHAR(20) DEFAULT 'sq_meter',
    purchase_date DATE,
    purchase_price DECIMAL(15,2),
    current_value DECIMAL(15,2),
    valuation_date DATE,
    title_deed_number VARCHAR(100),
    survey_number VARCHAR(100),
    document_path VARCHAR(255),
    status ENUM('active', 'inactive', 'under_maintenance', 'disposed') DEFAULT 'active',
    ownership_type ENUM('temple', 'trust', 'lease') DEFAULT 'temple',
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Land Records
CREATE TABLE land_records (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    property_id BIGINT UNSIGNED NOT NULL,
    record_id VARCHAR(50) UNIQUE NOT NULL,
    survey_number VARCHAR(100),
    khata_number VARCHAR(100),
    plot_number VARCHAR(100),
    village VARCHAR(100),
    tehsil VARCHAR(100),
    district VARCHAR(100),
    state VARCHAR(100),
    area_hectares DECIMAL(15,2),
    soil_type VARCHAR(100),
    irrigation_type VARCHAR(100),
    crop_details TEXT,
    record_date DATE,
    document_path VARCHAR(255),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (property_id) REFERENCES temple_properties(id)
);

-- Property Tenants
CREATE TABLE property_tenants (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    property_id BIGINT UNSIGNED NOT NULL,
    tenant_id VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    mobile VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    business_type VARCHAR(100),
    business_license_number VARCHAR(100),
    status ENUM('active', 'inactive', 'evicted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (property_id) REFERENCES temple_properties(id)
);