-- Scenario: Devotee books Seva (Pooja) service worth SAR 500
-- Voucher Type: SEVA
-- Voucher No: SEV-2024-001

-- Entry 1: Debit Bank (Money Received)
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, seva_booking_id) VALUES
(1, 1, '102', '205', 500.00, 'dr', 'seva', 'SEV-2024-001', '2024-01-16', 'Seva booking payment', 1);

-- Entry 2: Credit Advance Seva Bookings (Liability)
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, seva_booking_id) VALUES
(1, 2, '205', '102', 500.00, 'cr', 'seva', 'SEV-2024-001', '2024-01-16', 'Advance seva liability', 1);

-- After completion of seva:
-- Entry 3: Debit Advance Seva Bookings
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, seva_booking_id) VALUES
(1, 3, '205', '302', 500.00, 'dr', 'seva', 'SEV-2024-001', '2024-01-18', 'Seva completed', 1);

-- Entry 4: Credit Seva Revenue
INSERT INTO daybook (company_id, sno, accode, opaccode, amount, drcr, voucher_type, voucher_no, voucher_date, remarks, seva_booking_id) VALUES
(1, 4, '302', '205', 500.00, 'cr', 'seva', 'SEV-2024-001', '2024-01-18', 'Seva revenue', 1);