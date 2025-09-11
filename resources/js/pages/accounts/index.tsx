import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

interface Account {
  id: number;
  number: string;
  name: string;
  currency: string;
  balance: string;
  status: string;
  created_at: string;
}

export default function AccountsIndex({ accounts = [] as Account[] }) {
  const breadcrumbs: BreadcrumbItem[] = [{ title: 'Cuentas', href: '/accounts' }];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Cuentas" />
      <div className="p-4">
        <div className="flex justify-between items-center mb-4">
          <h1 className="text-xl font-semibold">Mis cuentas</h1>

          {/* USAR Link de Inertia y route(...) — NO usar {{ ... }} en JSX */}
          <Link
            href={route('accounts.create')}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow"
          >
            Crear cuenta
          </Link>
        </div>

        <div className="grid md:grid-cols-2 gap-4">
          {accounts.map(acc => (
            <div key={acc.id} className="rounded border p-4 bg-white dark:bg-neutral-900">
              <div className="flex justify-between">
                <div>
                  <div className="text-sm text-neutral-500">{acc.number}</div>
                  <div className="font-medium">{acc.name}</div>
                </div>
                <div className="text-right">
                  <div className="text-xs uppercase text-neutral-500">{acc.currency}</div>
                  <div className="text-lg font-semibold">${Number(acc.balance).toFixed(2)}</div>
                </div>
              </div>
              <div className="mt-3 flex gap-2">
                {/* Link a la vista de la cuenta */}
                <Link href={route('accounts.show', acc.id)} className="px-3 py-1.5 border rounded bg-gray-800 hover:bg-gray-900">
                  Ver
                </Link>
              </div>
            </div>
          ))}
        </div>
      </div>
    </AppLayout>
  );
}
