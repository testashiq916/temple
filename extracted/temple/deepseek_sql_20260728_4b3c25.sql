-- ===========================================
-- INVENTORY & DEAD STOCK MANAGEMENT
-- ===========================================

-- Inventory Categories
CREATE TABLE inventory_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);

-- Inventory Items
CREATE TABLE inventory_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    item_code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    unit VARCHAR(20),
    quantity INT DEFAULT 0,
    min_quantity INT DEFAULT 0,
    max_quantity INT DEFAULT 0,
    unit_price DECIMAL(10,2),
    total_value DECIMAL(15,2),
    location VARCHAR(255),
    expiry_date DATE,
    status ENUM('available', 'low_stock', 'expired', 'disposed') DEFAULT 'available',
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (category_id) REFERENCES inventory_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Stock Movements
CREATE TABLE stock_movements (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    item_id BIGINT UNSIGNED NOT NULL,
    movement_id VARCHAR(50) UNIQUE NOT NULL,
    movement_type ENUM('purchase', 'issue', 'return', 'transfer', 'dispose') NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2),
    total_amount DECIMAL(15,2),
    movement_date DATE NOT NULL,
    from_location VARCHAR(255),
    to_location VARCHAR(255),
    reference_no VARCHAR(50),
    remarks TEXT,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (item_id) REFERENCES inventory_items(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);