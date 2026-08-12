import { useQuery } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { TrialBalance } from '../types'
import { Card, PageHeader, Table } from '../components/ui'

export function Accounting() {
  const { data, isLoading } = useQuery({
    queryKey: ['trial-balance'],
    queryFn: async () => (await api.get<TrialBalance>('/reports/trial-balance')).data,
  })

  return (
    <div>
      <PageHeader title="Accounting — Trial Balance" />

      {isLoading || !data ? (
        <p className="text-slate-500">Loading…</p>
      ) : (
        <>
          <div className="mb-6 grid grid-cols-2 gap-4">
            <Card>
              <p className="text-sm text-slate-500">Total Debit</p>
              <p className="mt-1 text-2xl font-semibold text-slate-900">₹{data.total_debit}</p>
            </Card>
            <Card>
              <p className="text-sm text-slate-500">Total Credit</p>
              <p className="mt-1 text-2xl font-semibold text-slate-900">₹{data.total_credit}</p>
            </Card>
          </div>

          <Table>
            <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th className="px-4 py-3">Code</th>
                <th className="px-4 py-3">Account</th>
                <th className="px-4 py-3">Type</th>
                <th className="px-4 py-3 text-right">Balance</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {data.accounts.map((account) => (
                <tr key={account.accode}>
                  <td className="px-4 py-3 font-mono text-xs text-slate-500">{account.accode}</td>
                  <td className="px-4 py-3 text-slate-900">{account.name}</td>
                  <td className="px-4 py-3 capitalize text-slate-600">{account.actype}</td>
                  <td className="px-4 py-3 text-right text-slate-900">₹{account.balance}</td>
                </tr>
              ))}
            </tbody>
          </Table>
        </>
      )}
    </div>
  )
}
