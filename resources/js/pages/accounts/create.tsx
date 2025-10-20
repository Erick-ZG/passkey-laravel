import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';

interface Currency { code: string; label: string; }

export default function AccountCreate({ currencies = [] as Currency[] }) {
  const { data, setData, post, processing, errors } = useForm({
    name: '',
    currency: currencies.length ? currencies[0].code : 'USD',
  });

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Cuentas', href: '/accounts' },
    { title: 'Crear cuenta', href: '/accounts/create' },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Crear cuenta" />
      <div className="p-4 max-w-lg">
        <form
          onSubmit={(e) => {
            e.preventDefault();
            post(route('accounts.store'));
          }}
          className="space-y-4"
        >
          <div>
            <label className="block text-sm font-medium mb-1">Nombre de la cuenta</label>
            <input
              value={data.name}
              onChange={(e) => setData('name', e.target.value)}
              className="w-full rounded border px-3 py-2 bg-white dark:bg-neutral-800 dark:text-white"
              placeholder="Ej: Cuenta principal, Ahorros, Viaje"
            />
            {errors.name && <p className="text-sm text-red-600 mt-1">{errors.name as string}</p>}
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Moneda</label>
            <select
              value={data.currency}
              onChange={(e) => setData('currency', e.target.value)}
              className="w-full rounded border px-3 py-2 bg-white dark:bg-neutral-800 dark:text-white"
            >
              {currencies.map((c: Currency) => (
                <option key={c.code} value={c.code}>
                  {c.label}
                </option>
              ))}
            </select>
            {errors.currency && <p className="text-sm text-red-600 mt-1">{errors.currency as string}</p>}
          </div>

          <div className="flex gap-2">
            <button
              type="submit"
              disabled={processing}
              className="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
            >
              Crear cuenta
            </button>

            <Link
              href={route('accounts.index')}
              className="px-4 py-2 rounded border bg-transparent text-sm flex items-center justify-center"
            >
              Cancelar
            </Link>
          </div>
        </form>
      </div>
    </AppLayout>
  );
}
