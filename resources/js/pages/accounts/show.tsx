import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

interface Account {
  id: number; number: string; name: string; currency: string; balance: string; status: string;
}
interface Tx {
  id: number; type: string; amount: string; balance_after: string;
  reference?: string; counterparty?: string; status: string; created_at: string;
}
interface Paginated<T> { data: T[]; links: { url: string|null; label: string; active: boolean }[]; }

export default function AccountShow({ account, transactions }: { account: Account; transactions: Paginated<Tx> }) {
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Cuentas', href: '/accounts' },
    { title: account.name, href: route('accounts.show', account.id) },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Cuenta ${account.name}`} />
      <div className="p-4 space-y-4">
        <div className="rounded border p-4 bg-white dark:bg-neutral-900">
          <div className="flex justify-between">
            <div>
              <div className="text-sm text-neutral-500">{account.number}</div>
              <h1 className="text-xl font-semibold">{account.name}</h1>
            </div>
            <div className="text-right">
              <div className="text-xs uppercase text-neutral-500">{account.currency}</div>
              <div className="text-2xl font-bold">${Number(account.balance).toFixed(2)}</div>
            </div>
          </div>
          <div className="mt-3 flex gap-2">
            <Link href={route('deposits.create', account.id)} className="px-3 py-1.5 rounded bg-green-600 text-white">Depositar</Link>
            <Link href={route('transfers.create', account.id)} className="px-3 py-1.5 rounded bg-blue-600 text-white">Transferir</Link>
            <Link href={route('payments.create', account.id)} className="px-3 py-1.5 rounded bg-amber-600 text-white">Pagar</Link>
          </div>
        </div>

        <div className="rounded border bg-white dark:bg-neutral-900 overflow-x-auto">
          <table className="min-w-full text-sm">
            <thead className="bg-neutral-50 dark:bg-neutral-800">
              <tr>
                <th className="text-left p-3">Fecha</th>
                <th className="text-left p-3">Tipo</th>
                <th className="text-left p-3">Contraparte</th>
                <th className="text-right p-3">Monto</th>
                <th className="text-right p-3">Saldo</th>
                <th className="text-left p-3">Ref</th>
              </tr>
            </thead>
            <tbody>
              {transactions.data.map(tx => (
                <tr key={tx.id} className="border-t">
                  <td className="p-3">{new Date(tx.created_at).toLocaleString()}</td>
                  <td className="p-3">{tx.type}</td>
                  <td className="p-3">{tx.counterparty ?? '-'}</td>
                  <td className="p-3 text-right">${Number(tx.amount).toFixed(2)}</td>
                  <td className="p-3 text-right">${Number(tx.balance_after).toFixed(2)}</td>
                  <td className="p-3">{tx.reference ?? '-'}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </AppLayout>
  );
}
