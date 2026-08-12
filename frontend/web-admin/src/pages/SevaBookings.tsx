import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { api, apiErrorMessage } from '../lib/api'
import type { Paginated, SevaBooking } from '../types'
import { Badge, Button, PageHeader, Table } from '../components/ui'
import { useState } from 'react'

function statusTone(status: string): 'slate' | 'green' | 'amber' | 'red' {
  if (status === 'completed' || status === 'confirmed') return 'green'
  if (status === 'pending') return 'amber'
  if (status === 'cancelled') return 'red'
  return 'slate'
}

export function SevaBookings() {
  const queryClient = useQueryClient()
  const [error, setError] = useState<string | null>(null)

  const { data, isLoading } = useQuery({
    queryKey: ['seva-bookings'],
    queryFn: async () => (await api.get<Paginated<SevaBooking>>('/seva-bookings')).data,
  })

  const confirmPayment = useMutation({
    mutationFn: async (id: number) => api.post(`/seva-bookings/${id}/confirm-payment`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['seva-bookings'] }),
    onError: (err) => setError(apiErrorMessage(err)),
  })

  const complete = useMutation({
    mutationFn: async (id: number) => api.post(`/seva-bookings/${id}/complete`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['seva-bookings'] }),
    onError: (err) => setError(apiErrorMessage(err)),
  })

  const cancel = useMutation({
    mutationFn: async (id: number) => api.post(`/seva-bookings/${id}/cancel`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['seva-bookings'] }),
    onError: (err) => setError(apiErrorMessage(err)),
  })

  return (
    <div>
      <PageHeader title="Seva Bookings" />
      {error && <p className="mb-4 text-sm text-red-600">{error}</p>}

      {isLoading ? (
        <p className="text-slate-500">Loading…</p>
      ) : (
        <Table>
          <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th className="px-4 py-3">Booking</th>
              <th className="px-4 py-3">Devotee</th>
              <th className="px-4 py-3">Seva</th>
              <th className="px-4 py-3">Net Amount</th>
              <th className="px-4 py-3">Status</th>
              <th className="px-4 py-3">Payment</th>
              <th className="px-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data?.data.map((booking) => (
              <tr key={booking.id}>
                <td className="px-4 py-3 font-mono text-xs text-slate-500">{booking.booking_id}</td>
                <td className="px-4 py-3 text-slate-900">
                  {booking.devotee ? `${booking.devotee.first_name} ${booking.devotee.last_name}` : '—'}
                </td>
                <td className="px-4 py-3 text-slate-600">{booking.seva?.name ?? '—'}</td>
                <td className="px-4 py-3 text-slate-600">₹{booking.net_amount}</td>
                <td className="px-4 py-3">
                  <Badge tone={statusTone(booking.status)}>{booking.status}</Badge>
                </td>
                <td className="px-4 py-3">
                  <Badge tone={booking.payment_status === 'paid' ? 'green' : 'amber'}>{booking.payment_status}</Badge>
                </td>
                <td className="space-x-2 px-4 py-3">
                  {booking.payment_status !== 'paid' && (
                    <Button variant="secondary" onClick={() => confirmPayment.mutate(booking.id)}>
                      Confirm Payment
                    </Button>
                  )}
                  {booking.status === 'confirmed' && booking.payment_status === 'paid' && (
                    <Button variant="secondary" onClick={() => complete.mutate(booking.id)}>
                      Complete
                    </Button>
                  )}
                  {!['completed', 'cancelled'].includes(booking.status) && (
                    <Button variant="secondary" onClick={() => cancel.mutate(booking.id)}>
                      Cancel
                    </Button>
                  )}
                </td>
              </tr>
            ))}
            {data?.data.length === 0 && (
              <tr>
                <td colSpan={7} className="px-4 py-6 text-center text-slate-400">
                  No seva bookings yet.
                </td>
              </tr>
            )}
          </tbody>
        </Table>
      )}
    </div>
  )
}
