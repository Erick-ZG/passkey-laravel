import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

interface Account { id: number; name: string; number: string; currency: string; balance: string; }
interface Tx { id: number; account_id: number; type: string; amount: string; counterparty?: string; created_at: string; }

export default function Dashboard({ accounts = [], total_balance = 0, recent_transactions = [] as Tx[] }: any) {
  const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />

      <div className="p-4 space-y-4">
        {/* Top summary */}
        <div className="grid gap-4 md:grid-cols-3">
          <div className="rounded-lg border p-4 bg-white dark:bg-neutral-900">
            <div className="text-sm text-neutral-500">Saldo total</div>
            <div className="mt-2 text-2xl font-bold">${Number(total_balance).toFixed(2)}</div>
            <div className="mt-3 text-sm text-neutral-500">Suma de todos tus saldos</div>
          </div>

          <div className="rounded-lg border p-4 bg-white dark:bg-neutral-900">
            <div className="text-sm text-neutral-500">Cuentas</div>
            <div className="mt-2 text-2xl font-bold">{accounts.length}</div>
            <div className="mt-3 text-sm text-neutral-500">Cuentas activas</div>
            <div className="mt-3">
                <Link href={route('accounts.index')}
                className="bg-indigo-600 hover:bg-indigo-800 text-white px-4 py-2 rounded-md shadow">
                Ver todas las cuentas
                </Link>
            </div>
          </div>

          <div className="rounded-lg border p-4 bg-white dark:bg-neutral-900">
            <div className="text-sm text-neutral-500">Acciones</div>
            <div className="mt-2 flex flex-col gap-2">
              <Link href={route('accounts.create')} className="px-3 py-2 rounded bg-green-700 hover:bg-green-800 text-white">Crear cuenta</Link>
              <Link href={route('deposits.create', accounts.length ? accounts[0].id : '')} className="px-3 py-2 rounded bg-gray-800 hover:bg-gray-900 border text-sm">Depositar</Link>
            </div>
          </div>
        </div>

        {/* Accounts list */}
        <div className="rounded-lg border p-4 bg-white dark:bg-neutral-900">
          <h2 className="font-semibold mb-3">Tus cuentas</h2>
          <div className="grid gap-3 md:grid-cols-2">
            {accounts.map((a: Account) => (
              <div key={a.id} className="p-3 rounded border bg-white dark:bg-neutral-800">
                <div className="flex justify-between items-center">
                  <div>
                    <div className="text-sm text-neutral-500">{a.number}</div>
                    <div className="font-medium">{a.name}</div>
                  </div>
                  <div className="text-right">
                    <div className="text-xs uppercase text-neutral-500">{a.currency}</div>
                    <div className="text-lg font-semibold">${Number(a.balance).toFixed(2)}</div>
                    <Link href={route('accounts.show', a.id)}
                    className="bg-gray-700 hover:bg-gray-900 text-white px-3 py-1 rounded-md shadow">
                    Ver
                    </Link>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Recent transactions */}
        <div className="rounded-lg border p-4 bg-white dark:bg-neutral-900">
          <h2 className="font-semibold mb-3">Movimientos recientes</h2>
          {recent_transactions.length === 0 ? (
            <div className="text-sm text-neutral-500">No hay movimientos recientes.</div>
          ) : (
            <ul className="space-y-2">
              {recent_transactions.map((tx: Tx) => (
                <li key={tx.id} className="flex justify-between items-center border-b pb-2">
                  <div>
                    <div className="text-sm">{tx.type} {tx.counterparty ? `— ${tx.counterparty}` : ''}</div>
                    <div className="text-xs text-neutral-500">{new Date(tx.created_at).toLocaleString()}</div>
                  </div>
                  <div className="text-right font-medium">${Number(tx.amount).toFixed(2)}</div>
                </li>
              ))}
            </ul>
          )}
        </div>
      </div>
    </AppLayout>
  );
}
