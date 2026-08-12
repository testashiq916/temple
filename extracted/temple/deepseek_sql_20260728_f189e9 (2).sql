-- ===========================================
-- ACCOUNTING MODULE (Double-Entry System)
-- ===========================================

-- Account Group BS (Balance Sheet Head)
CREATE TABLE accountgbs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    bshead VARCHAR(50) UNIQUE NOT NULL,
    bshead_name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- Account Group
CREATE TABLE accountg (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    grcode VARCHAR(20) UNIQUE NOT NULL,
    grname VARCHAR(100) NOT NULL,
    bshead_id BIGINT UNSIGNED NOT NULL,
    parent_grcode VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (bshead_id) REFERENCES accountgbs(id),
    FOREIGN KEY (parent_grcode) REFERENCES accountg(grcode)
);

-- Account Master (Chart of Accounts)
CREATE TABLE accountm (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    accode VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    grcode VARCHAR(20) NOT NULL,
    bshead VARCHAR(50) NOT NULL,
    actype ENUM('debit', 'credit') NOT NULL,
    opening_balance DECIMAL(15,2) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    is_default BOOLEAN DEFAULT FALSE,
    address TEXT,
    contact_person VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    tax_number VARCHAR(50),
    gst_type ENUM('registered', 'unregistered', 'composition') DEFAULT 'unregistered',
    gstin VARCHAR(50),
    bank_name VARCHAR(255),
    bank_branch VARCHAR(255),
    bank_account_number VARCHAR(50),
    bank_ifsc VARCHAR(20),
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (grcode) REFERENCES accountg(grcode),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Default Chart of Accounts for Temple ERP
INSERT INTO accountgbs (company_id, bshead, bshead_name) VALUES
(1, 'ASSETS', 'Assets'),
(1, 'LIABILITIES', 'Liabilities'),
(1, 'INCOME', 'Income'),
(1, 'EXPENSES', 'Expenses');

INSERT INTO accountg (company_id, grcode, grname, bshead_id, parent_grcode) VALUES
(1, 'CA', 'Current Assets', 1, NULL),
(1, 'FA', 'Fixed Assets', 1, NULL),
(1, 'CL', 'Current Liabilities', 2, NULL),
(1, 'LL', 'Long Term Liabilities', 2, NULL),
(1, 'OP', 'Operating Income', 3, NULL),
(1, 'OI', 'Other Income', 3, NULL),
(1, 'OE', 'Operating Expenses', 4, NULL),
(1, 'NE', 'Non-Operating Expenses', 4, NULL);

-- Sample Accounts
INSERT INTO accountm (company_id, accode, name, grcode, bshead, actype, opening_balance) VALUES
-- Asset Accounts
(1, '101', 'Cash in Hand', 'CA', 'ASSETS', 'debit', 0),
(1, '102', 'Bank Account - Main', 'CA', 'ASSETS', 'debit', 0),
(1, '103', 'Bank Account - Digital Gold', 'CA', 'ASSETS', 'debit', 0),
(1, '104', 'Accounts Receivable', 'CA', 'ASSETS', 'debit', 0),
(1, '105', 'Security Deposit Receivable', 'CA', 'ASSETS', 'debit', 0),
(1, '106', 'Temple Property', 'FA', 'ASSETS', 'debit', 0),
(1, '107', 'Temple Gold Reserve', 'FA', 'ASSETS', 'debit', 0),
(1, '108', 'Temple Silver Reserve', 'FA', 'ASSETS', 'debit', 0),

-- Liability Accounts
(1, '201', 'Accounts Payable', 'CL', 'LIABILITIES', 'credit', 0),
(1, '202', 'Security Deposit Payable', 'CL', 'LIABILITIES', 'credit', 0),
(1, '203', 'GST Payable - CGST', 'CL', 'LIABILITIES', 'credit', 0),
(1, '204', 'GST Payable - SGST', 'CL', 'LIABILITIES', 'credit', 0),
(1, '205', 'Advance Seva Bookings', 'CL', 'LIABILITIES', 'credit', 0),

-- Income Accounts
(1, '301', 'Donation Revenue', 'OP', 'INCOME', 'credit', 0),
(1, '302', 'Seva Revenue', 'OP', 'INCOME', 'credit', 0),
(1, '303', 'Prasad Sales', 'OP', 'INCOME', 'credit', 0),
(1, '304', 'Rental Income', 'OP', 'INCOME', 'credit', 0),
(1, '305', 'Interest Income', 'OI', 'INCOME', 'credit', 0),
(1, '306', 'Digital Gold Income', 'OI', 'INCOME', 'credit', 0),

-- Expense Accounts
(1, '401', 'Staff Salaries', 'OE', 'EXPENSES', 'debit', 0),
(1, '402', 'Utilities - Electricity', 'OE', 'EXPENSES', 'debit', 0),
(1, '403', 'Utilities - Water', 'OE', 'EXPENSES', 'debit', 0),
(1, '404', 'Temple Maintenance', 'OE', 'EXPENSES', 'debit', 0),
(1, '405', 'Prasad Materials', 'OE', 'EXPENSES', 'debit', 0),
(1, '406', 'Insurance', 'OE', 'EXPENSES', 'debit', 0),
(1, '407', 'Bank Charges', 'NE', 'EXPENSES', 'debit', 0);

-- Daybook (Ledger Entries)
CREATE TABLE daybook (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    slno BIGINT UNSIGNED AUTO_INCREMENT UNIQUE,
    sno BIGINT UNSIGNED NOT NULL,
    accode VARCHAR(20) NOT NULL,
    opaccode VARCHAR(20) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    drcr ENUM('dr', 'cr') NOT NULL,
    voucher_type ENUM('donation', 'seva', 'payment', 'receipt', 'journal', 'contra', 'credit_note', 'debit_note') NOT NULL,
    voucher_no VARCHAR(50) NOT NULL,
    voucher_date DATE NOT NULL,
    remarks TEXT,
    reference_no VARCHAR(50),
    reference_date DATE,
    devotee_id BIGINT UNSIGNED,
    seva_booking_id BIGINT UNSIGNED,
    donation_id BIGINT UNSIGNED,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (accode) REFERENCES accountm(accode),
    FOREIGN KEY (opaccode) REFERENCES accountm(accode),
    FOREIGN KEY (devotee_id) REFERENCES devotees(id) ON DELETE SET NULL,
    FOREIGN KEY (seva_booking_id) REFERENCES seva_bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_voucher (voucher_type, voucher_no),
    INDEX idx_account (accode),
    INDEX idx_opposite (opaccode)
);

-- Voucher Header
CREATE TABLE vouchers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    voucher_no VARCHAR(50) NOT NULL,
    voucher_type ENUM('donation', 'seva', 'payment', 'receipt', 'journal', 'contra', 'credit_note', 'debit_note') NOT NULL,
    voucher_date DATE NOT NULL,
    reference_no VARCHAR(50),
    reference_date DATE,
    narration TEXT,
    total_amount DECIMAL(15,2),
    is_posted BOOLEAN DEFAULT FALSE,
    posted_by BIGINT UNSIGNED,
    posted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (posted_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    UNIQUE KEY unique_voucher (company_id, voucher_type, voucher_no)
);