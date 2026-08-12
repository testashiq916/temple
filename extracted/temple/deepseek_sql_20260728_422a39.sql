-- Scenario: Devotee donates digital gold worth SAR 10,000
-- Voucher Type: DONATION
-- Voucher No: DON-2024-001

-- Entry 1: Debit Digital Gold Reserve (Asset)
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, donation_id) VALUES
(1, 1, '107', '301', 10000.00, 'dr', 'donation', 'DON-2024-001', '2024-01-17', 'Digital gold donation received', 1);

-- Entry 2: Credit Donation Revenue
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, donation_id) VALUES
(1, 2, '301', '107', 10000.00, 'cr', 'donation', 'DON-2024-001', '2024-01-17', 'Digital gold donation revenue', 1);