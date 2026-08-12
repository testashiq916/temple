-- Scenario: Seva service cancelled, refund issued
-- Amount: SAR 500
-- Voucher Type: CREDIT_NOTE
-- Voucher No: CN-2024-001

-- Entry 1: Debit Seva Revenue (Reduce Income)
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, seva_booking_id) VALUES
(1, 1, '302', '205', 500.00, 'dr', 'credit_note', 'CN-2024-001', '2024-01-20', 'Seva cancellation refund', 1);

-- Entry 2: Credit Advance Liability (or Bank if refunded)
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, seva_booking_id) VALUES
(1, 2, '205', '302', 500.00, 'cr', 'credit_note', 'CN-2024-001', '2024-01-20', 'Refund liability created', 1);