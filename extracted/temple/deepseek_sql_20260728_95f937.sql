-- Scenario: Devotee books Laddu Prasad worth SAR 200
-- Voucher Type: PAYMENT
-- Voucher No: PAY-2024-001

-- Entry 1: Debit Bank (Money Received)
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks) VALUES
(1, 1, '102', '303', 200.00, 'dr', 'payment', 'PAY-2024-001', '2024-01-19', 'Prasad booking payment');

-- Entry 2: Credit Prasad Revenue
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks) VALUES
(1, 2, '303', '102', 200.00, 'cr', 'payment', 'PAY-2024-001', '2024-01-19', 'Prasad revenue');