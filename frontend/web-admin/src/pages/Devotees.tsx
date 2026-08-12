import { type FormEvent, useState } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { api, apiErrorMessage } from '../lib/api'
import type { Devotee, Paginated } from '../types'
import { Badge, Button, Card, Input, PageHeader, Table } from '../components/ui'

export function Devotees() {
  const queryClient = useQueryClient()
  const [search, setSearch] = useState('')
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ first_name: '', last_name: '', email: '', mobile: '', gender: 'male' })
  const [error, setError] = useState<string | null>(null)

  const { data, isLoading } = useQuery({
    queryKey: ['devotees', search],
    queryFn: async () =>
      (await api.get<Paginated<Devotee>>('/devotees', { params: { search: search || undefined } })).data,
  })

  const createDevotee = useMutation({
    mutationFn: async () => api.post('/devotees', { ...form, temple_id: 1 }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['devotees'] })
      setShowForm(false)
      setForm({ first_name: '', last_name: '', email: '', mobile: '', gender: 'male' })
    },
    onError: (err) => setError(apiErrorMessage(err)),
  })

  function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setError(null)
    createDevotee.mutate()
  }

  return (
    <div>
      <PageHeader
        title="Devotees"
        action={<Button onClick={() => setShowForm((v) => !v)}>{showForm ? 'Cancel' : 'Add Devotee'}</Button>}
      />

      {showForm && (
        <Card className="mb-6">
          <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Input
              placeholder="First name"
              value={form.first_name}
              onChange={(e) => setForm({ ...form, first_name: e.target.value })}
              required
            />
            <Input
              placeholder="Last name"
              value={form.last_name}
              onChange={(e) => setForm({ ...form, last_name: e.target.value })}
              required
            />
            <Input
              type="email"
              placeholder="Email"
              value={form.email}
              onChange={(e) => setForm({ ...form, email: e.target.value })}
              required
            />
            <Input
              placeholder="Mobile"
              value={form.mobile}
              onChange={(e) => setForm({ ...form, mobile: e.target.value })}
              required
            />
            {error && <p className="col-span-full text-sm text-red-600">{error}</p>}
            <div className="col-span-full">
              <Button type="submit" disabled={createDevotee.isPending}>
                {createDevotee.isPending ? 'Saving…' : 'Save Devotee'}
              </Button>
            </div>
          </form>
        </Card>
      )}

      <div className="mb-4 max-w-sm">
        <Input placeholder="Search by name, email, mobile…" value={search} onChange={(e) => setSearch(e.target.value)} />
      </div>

      {isLoading ? (
        <p className="text-slate-500">Loading…</p>
      ) : (
        <Table>
          <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th className="px-4 py-3">Name</th>
              <th className="px-4 py-3">Email</th>
              <th className="px-4 py-3">Mobile</th>
              <th className="px-4 py-3">Member</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data?.data.map((devotee) => (
              <tr key={devotee.id}>
                <td className="px-4 py-3 font-medium text-slate-900">
                  {devotee.first_name} {devotee.last_name}
                </td>
                <td className="px-4 py-3 text-slate-600">{devotee.email}</td>
                <td className="px-4 py-3 text-slate-600">{devotee.mobile}</td>
                <td className="px-4 py-3">
                  <Badge tone={devotee.is_member ? 'green' : 'slate'}>{devotee.is_member ? 'Member' : 'Guest'}</Badge>
                </td>
              </tr>
            ))}
            {data?.data.length === 0 && (
              <tr>
                <td colSpan={4} className="px-4 py-6 text-center text-slate-400">
                  No devotees found.
                </td>
              </tr>
            )}
          </tbody>
        </Table>
      )}
    </div>
  )
}
