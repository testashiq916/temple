import { useQuery } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { Dashboard as DashboardData } from '../types'
import { PageHeader, StatCard, Card } from '../components/ui'

export function Dashboard() {
  const { data, isLoading } = useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => (await api.get<DashboardData>('/reports/dashboard')).data,
  })

  return (
    <div>
      <PageHeader title="Dashboard" />
      {isLoading || !data ? (
        <p className="text-slate-500">Loading…</p>
      ) : (
        <>
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StatCard label="Donations today" value={`₹${data.donations_today}`} />
            <StatCard label="Donations this month" value={`₹${data.donations_this_month}`} />
            <StatCard label="Seva bookings today" value={data.seva_bookings_today} />
            <StatCard label="Seva revenue this month" value={`₹${data.seva_revenue_this_month}`} />
            <StatCard label="Pending seva bookings" value={data.pending_seva_bookings} />
            <StatCard label="Ledger entries today" value={data.ledger_entries_today} />
          </div>
          <Card className="mt-6">
            <p className="text-sm text-slate-600">
              This dashboard reflects live data from the Laravel API — donations, seva bookings, and the
              double-entry ledger all post through the same backend used by the devotee-facing apps.
            </p>
          </Card>
        </>
      )}
    </div>
  )
}
