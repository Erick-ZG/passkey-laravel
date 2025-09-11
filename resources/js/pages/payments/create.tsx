import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

export default function PaymentCreate({ account, merchants = [] }: any) {
  const { data, setData, post, processing, errors } = useForm({
    merchant_id: '',
    amount: '',
    description: '',
  });

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Cuentas', href: '/accounts' },
    { title: account.name, href: route('accounts.show', account.id) },
    { title: 'Pago', href: route('payments.create', account.id) },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Pago" />
      <div className="p-4 max-w-lg">
        <form
          onSubmit={(e) => {
            e.preventDefault();
            post(route('payments.store', account.id));
          }}
          className="space-y-4"
        >
          <div>
            <label className="block text-sm mb-1">Comercio</label>

            {/* SELECT accesible y oscuro */}
            <div className="relative">
              <select
                value={data.merchant_id}
                onChange={(e) => setData('merchant_id', e.target.value)}
                className="w-full rounded border px-3 py-2 appearance-none pr-8
                  bg-white text-black
                  dark:bg-neutral-800 dark:text-white
                  dark:border-neutral-700
                "
                aria-label="Seleccionar comercio"
              >
                <option value="">Selecciona un comercio...</option>
                {merchants.map((m: any) => (
                  <option key={m.id} value={m.id}>
                    {m.name} {m.category ? `— ${m.category}` : ''}
                  </option>
                ))}
              </select>

              {/* flecha custom para mejor contraste */}
              <div className="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                <svg className="w-4 h-4 text-neutral-400 dark:text-neutral-300" viewBox="0 0 20 20" fill="currentColor">
                  <path fillRule="evenodd" d="M5.23 7.21a.75.75 0 011.06-.02L10 10.833l3.71-3.644a.75.75 0 111.04 1.08l-4.24 4.163a.75.75 0 01-1.04 0L5.25 8.27a.75.75 0 01-.02-1.06z" clipRule="evenodd" />
                </svg>
              </div>
            </div>

            {errors.merchant_id && <p className="text-sm text-red-600 mt-1">{errors.merchant_id as string}</p>}
          </div>

          <div>
            <label className="block text-sm mb-1">Monto</label>
            <input
              type="number"
              step="0.01"
              min="1"
              value={data.amount}
              onChange={(e) => setData('amount', e.target.value)}
              className="w-full rounded border px-3 py-2 bg-white dark:bg-neutral-800 dark:text-white"
            />
            {errors.amount && <p className="text-sm text-red-600 mt-1">{errors.amount as string}</p>}
          </div>

          <div>
            <label className="block text-sm mb-1">Descripción (opcional)</label>
            <input
              value={data.description}
              onChange={(e) => setData('description', e.target.value)}
              className="w-full rounded border px-3 py-2 bg-white dark:bg-neutral-800 dark:text-white"
            />
          </div>

          <div className="flex gap-2">
            <button type="submit" disabled={processing} className="px-4 py-2 rounded cursor-pointer bg-amber-600 hover:bg-amber-700 text-white">
              Pagar
            </button>

            <Link href={route('accounts.show', account.id)} className="px-4 py-2 border rounded bg-gray-600 hover:bg-gray-700">
              Cancelar
            </Link>
          </div>
        </form>
      </div>
    </AppLayout>
  );
}
