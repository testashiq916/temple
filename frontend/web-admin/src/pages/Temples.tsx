import { type FormEvent, useState } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { api, apiErrorMessage } from '../lib/api'
import type { Paginated, Temple } from '../types'
import { Badge, Button, Card, Input, PageHeader, Table } from '../components/ui'

export function Temples() {
  const queryClient = useQueryClient()
  const [showForm, setShowForm] = useState(false)
  const [name, setName] = useState('')
  const [city, setCity] = useState('')
  const [deityName, setDeityName] = useState('')
  const [error, setError] = useState<string | null>(null)

  const { data, isLoading } = useQuery({
    queryKey: ['temples'],
    queryFn: async () => (await api.get<Paginated<Temple>>('/temples')).data,
  })

  const createTemple = useMutation({
    mutationFn: async () => api.post('/temples', { name, city, deity_name: deityName }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['temples'] })
      setShowForm(false)
      setName('')
      setCity('')
      setDeityName('')
    },
    onError: (err) => setError(apiErrorMessage(err)),
  })

  function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setError(null)
    createTemple.mutate()
  }

  return (
    <div>
      <PageHeader
        title="Temples"
        action={<Button onClick={() => setShowForm((v) => !v)}>{showForm ? 'Cancel' : 'Add Temple'}</Button>}
      />

      {showForm && (
        <Card className="mb-6">
          <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Input placeholder="Temple name" value={name} onChange={(e) => setName(e.target.value)} required />
            <Input placeholder="City" value={city} onChange={(e) => setCity(e.target.value)} />
            <Input placeholder="Presiding deity" value={deityName} onChange={(e) => setDeityName(e.target.value)} />
            {error && <p className="col-span-full text-sm text-red-600">{error}</p>}
            <div className="col-span-full">
              <Button type="submit" disabled={createTemple.isPending}>
                {createTemple.isPending ? 'Saving…' : 'Save Temple'}
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
              <th className="px-4 py-3">Name</th>
              <th className="px-4 py-3">Deity</th>
              <th className="px-4 py-3">City</th>
              <th className="px-4 py-3">Type</th>
              <th className="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data?.data.map((temple) => (
              <tr key={temple.id}>
                <td className="px-4 py-3 font-medium text-slate-900">{temple.name}</td>
                <td className="px-4 py-3 text-slate-600">{temple.deity_name ?? '—'}</td>
                <td className="px-4 py-3 text-slate-600">{temple.city ?? '—'}</td>
                <td className="px-4 py-3 text-slate-600 capitalize">{temple.temple_type}</td>
                <td className="px-4 py-3">
                  <Badge tone={temple.is_active ? 'green' : 'slate'}>{temple.is_active ? 'Active' : 'Inactive'}</Badge>
                </td>
              </tr>
            ))}
            {data?.data.length === 0 && (
              <tr>
                <td colSpan={5} className="px-4 py-6 text-center text-slate-400">
                  No temples yet.
                </td>
              </tr>
            )}
          </tbody>
        </Table>
      )}
    </div>
  )
}
