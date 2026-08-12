-- Scenario: Devotee donates SAR 1,000 via E-Hundi (Digital Donation Box)
-- Voucher Type: RECEIPT
-- Voucher No: RCP-2024-001

-- Entry 1: Debit Bank (Money Received)
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, devotee_id) VALUES
(1, 1, '102', '301', 1000.00, 'dr', 'receipt', 'RCP-2024-001', '2024-01-15', 'E-Hundi donation received', 1);

-- Entry 2: Credit Donation Revenue
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, devotee_id) VALUES
(1, 2, '301', '102', 1000.00, 'cr', 'receipt', 'RCP-2024-001', '2024-01-15', 'Donation revenue', 1);