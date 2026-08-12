-- ===========================================
-- CROWD & QUEUE MANAGEMENT
-- ===========================================

-- Darshan Queue Management
CREATE TABLE darshan_queues (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    queue_id VARCHAR(50) UNIQUE NOT NULL,
    queue_type ENUM('general', 'special', 'vip', 'divyang') DEFAULT 'general',
    start_time DATETIME NOT NULL,
    estimated_wait_time INT DEFAULT 0,
    actual_wait_time INT DEFAULT 0,
    total_devotees INT DEFAULT 0,
    current_position INT DEFAULT 0,
    status ENUM('active', 'paused', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);

-- Queue Entries (Devotee Tracking)
CREATE TABLE queue_entries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    queue_id BIGINT UNSIGNED NOT NULL,
    devotee_id BIGINT UNSIGNED,
    queue_number VARCHAR(50) NOT NULL,
    entry_time DATETIME NOT NULL,
    exit_time DATETIME,
    status ENUM('waiting', 'entered', 'completed', 'exited') DEFAULT 'waiting',
    qr_code VARCHAR(255),
    wristband_id VARCHAR(50),
    darshan_duration INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (queue_id) REFERENCES darshan_queues(id) ON DELETE CASCADE,
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE SET NULL
);

-- AI Crowd Analytics
CREATE TABLE crowd_analytics (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    analytics_date DATE NOT NULL,
    hour INT DEFAULT 0,
    total_devotees INT DEFAULT 0,
    predicted_devotees INT DEFAULT 0,
    peak_capacity_percent DECIMAL(5,2) DEFAULT 0,
    average_darshan_time INT DEFAULT 0,
    average_queue_length INT DEFAULT 0,
    congestion_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    heatmap_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    UNIQUE KEY unique_analytics (temple_id, analytics_date, hour)
);

-- CCTV Camera Integration
CREATE TABLE cctv_cameras (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    camera_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    camera_type ENUM('fixed', 'ptz', 'ai') DEFAULT 'fixed',
    rtsp_url VARCHAR(255),
    ai_enabled BOOLEAN DEFAULT FALSE,
    model_config JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id)
);

-- AI Detection Alerts
CREATE TABLE ai_detection_alerts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    temple_id BIGINT UNSIGNED NOT NULL,
    camera_id BIGINT UNSIGNED NOT NULL,
    alert_type ENUM('crowd_surge', 'queue_length', 'missing_person', 'distress', 'security') NOT NULL,
    severity ENUM('info', 'warning', 'critical') DEFAULT 'info',
    description TEXT,
    detection_data JSON,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (temple_id) REFERENCES temples(id),
    FOREIGN KEY (camera_id) REFERENCES cctv_cameras(id)
);