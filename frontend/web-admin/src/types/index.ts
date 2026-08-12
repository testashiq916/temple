export interface Company {
  id: number
  name: string
  code: string
  currency: string
}

export interface User {
  id: number
  name: string
  email: string
  company_id: number
  role_id: number | null
  company?: Company
}

export interface Paginated<T> {
  data: T[]
  total: number
  current_page: number
  last_page: number
  per_page: number
}

export interface Temple {
  id: number
  temple_id: string
  name: string
  sanskrit_name: string | null
  city: string | null
  state: string | null
  deity_name: string | null
  temple_type: string
  capacity: number
  is_active: boolean
}

export interface Devotee {
  id: number
  devotee_id: string
  first_name: string
  last_name: string
  full_name?: string
  email: string
  mobile: string
  city: string | null
  is_member: boolean
  is_active: boolean
}

export interface SevaService {
  id: number
  seva_id: string
  name: string
  sanskrit_name: string | null
  description: string | null
  price: string
  gst_rate: string
  duration_minutes: number
  is_active: boolean
  category?: { id: number; name: string }
}

export interface SevaSlot {
  id: number
  seva_id: number
  slot_date: string
  start_time: string
  end_time: string
  capacity: number
  booked_count: number
  is_available: boolean
}

export interface SevaBooking {
  id: number
  booking_id: string
  devotee_id: number
  seva_id: number
  slot_id: number
  booking_date: string
  slot_date: string
  total_amount: string
  net_amount: string
  status: string
  payment_status: string
  devotee?: Devotee
  seva?: SevaService
}

export interface Donor {
  id: number
  donor_id: string
  full_name: string
  email: string | null
  mobile: string | null
  donor_type: string
}

export interface Donation {
  id: number
  donation_id: string
  donor_id: number
  amount: string
  donation_date: string
  donation_type: string
  payment_method: string
  status: string
  purpose: string | null
  donor?: Donor
  category?: { id: number; name: string } | null
}

export interface AccountBalance {
  accode: string
  name: string
  actype: 'debit' | 'credit'
  balance: number
}

export interface TrialBalance {
  accounts: AccountBalance[]
  total_debit: number
  total_credit: number
}

export interface Dashboard {
  donations_today: number
  donations_this_month: number
  seva_bookings_today: number
  seva_revenue_this_month: number
  pending_seva_bookings: number
  ledger_entries_today: number
}
