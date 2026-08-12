import { type FormEvent, useState } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { api, apiErrorMessage } from '../lib/api'
import type { Donation, Donor, Paginated } from '../types'
import { Badge, Button, Card, Input, PageHeader, Select, Table } from '../components/ui'

export function Donations() {
  const queryClient = useQueryClient()
  const [showForm, setShowForm] = useState(false)
  const [donorId, setDonorId] = useState('')
  const [amount, setAmount] = useState('')
  const [paymentMethod, setPaymentMethod] = useState('upi')
  const [purpose, setPurpose] = useState('')
  const [error, setError] = useState<string | null>(null)

  const { data, isLoading } = useQuery({
    queryKey: ['donations'],
    queryFn: async () => (await api.get<Paginated<Donation>>('/donations')).data,
  })

  const { data: donors } = useQuery({
    queryKey: ['donors'],
    queryFn: async () => (await api.get<Paginated<Donor>>('/donors')).data,
  })

  const createDonation = useMutation({
    mutationFn: async () =>
      api.post('/donations', {
        temple_id: 1,
        donor_id: Number(donorId),
        amount: Number(amount),
        payment_method: paymentMethod,
        purpose,
      }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['donations'] })
      queryClient.invalidateQueries({ queryKey: ['dashboard'] })
      setShowForm(false)
      setAmount('')
      setPurpose('')
    },
    onError: (err) => setError(apiErrorMessage(err)),
  })

  function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setError(null)
    createDonation.mutate()
  }

  return (
    <div>
      <PageHeader
        title="Donations"
        action={<Button onClick={() => setShowForm((v) => !v)}>{showForm ? 'Cancel' : 'Record Donation'}</Button>}
      />

      {showForm && (
        <Card className="mb-6">
          <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Select value={donorId} onChange={(e) => setDonorId(e.target.value)} required>
              <option value="">Select donor…</option>
              {donors?.data.map((donor) => (
                <option key={donor.id} value={donor.id}>
                  {donor.full_name}
                </option>
              ))}
            </Select>
            <Input
              type="number"
              min="0.01"
              step="0.01"
              placeholder="Amount"
              value={amount}
              onChange={(e) => setAmount(e.target.value)}
              required
            />
            <Select value={paymentMethod} onChange={(e) => setPaymentMethod(e.target.value)}>
              <option value="upi">UPI</option>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="netbanking">Net Banking</option>
              <option value="cheque">Cheque</option>
            </Select>
            <Input placeholder="Purpose (optional)" value={purpose} onChange={(e) => setPurpose(e.target.value)} />
            {error && <p className="col-span-full text-sm text-red-600">{error}</p>}
            <div className="col-span-full">
              <Button type="submit" disabled={createDonation.isPending}>
                {createDonation.isPending ? 'Recording…' : 'Record Donation'}
              </Button>
            </div>
          </form>
        </Card>
      )}

      {isLoading ? (
        <p className="text-slate-500">Loading…</p>
      ) : (
        <Table>
          <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th className="px-4 py-3">Receipt</th>
              <th className="px-4 py-3">Donor</th>
              <th className="px-4 py-3">Amount</th>
              <th className="px-4 py-3">Method</th>
              <th className="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data?.data.map((donation) => (
              <tr key={donation.id}>
                <td className="px-4 py-3 font-mono text-xs text-slate-500">{donation.donation_id}</td>
                <td className="px-4 py-3 text-slate-900">{donation.donor?.full_name ?? '—'}</td>
                <td className="px-4 py-3 text-slate-600">₹{donation.amount}</td>
                <td className="px-4 py-3 text-slate-600 uppercase">{donation.payment_method}</td>
                <td className="px-4 py-3">
                  <Badge tone={donation.status === 'verified' ? 'green' : 'amber'}>{donation.status}</Badge>
                </td>
              </tr>
            ))}
            {data?.data.length === 0 && (
              <tr>
                <td colSpan={5} className="px-4 py-6 text-center text-slate-400">
                  No donations recorded yet.
                </td>
              </tr>
            )}
          </tbody>
        </Table>
      )}
    </div>
  )
}
