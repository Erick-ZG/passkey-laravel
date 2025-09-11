import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

export default function DepositCreate({ account }: any) {
  const { data, setData, post, processing, errors } = useForm({ amount: '', reference: '' });

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Cuentas', href: '/accounts' },
    { title: account.name, href: route('accounts.show', account.id) },
    { title: 'Depósito', href: route('deposits.create', account.id) },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Depósito" />
      <div className="p-4 max-w-lg">
        <form onSubmit={e => { e.preventDefault(); post(route('deposits.store', account.id)); }} className="space-y-4">
          <div>
            <label className="block text-sm mb-1">Monto</label>
            <input type="number" step="0.01" min="1" value={data.amount}
              onChange={e => setData('amount', e.target.value)}
              className="w-full border rounded px-3 py-2" />
            {errors.amount && <p className="text-red-600 text-sm mt-1">{errors.amount}</p>}
          </div>
          <div>
            <label className="block text-sm mb-1">Referencia (opcional)</label>
            <input type="text" value={data.reference}
              onChange={e => setData('reference', e.target.value)}
              className="w-full border rounded px-3 py-2" />
          </div>
          <div className="flex gap-2">
            <button disabled={processing} className="px-4 py-2 bg-green-600 hover:bg-green-700 cursor-pointer text-white rounded">Confirmar</button>
            <Link href={route('accounts.show', account.id)} className="px-4 py-2 bg-gray-600 hover:bg-gray-700 border rounded">Cancelar</Link>
          </div>
        </form>
      </div>
    </AppLayout>
  );
}
