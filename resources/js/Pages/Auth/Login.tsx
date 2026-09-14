import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login() {
    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/login');
    };

    return (
        <>
            <Head title="Sign in" />
            <main className="min-h-screen bg-slate-50 px-6 py-12 flex items-center justify-center">
                <div className="w-full max-w-md">
                    <div className="mb-8 text-center">
                        <Link href="/" className="text-2xl font-extrabold text-emerald-700">Ochoka Heritage</Link>
                        <h1 className="mt-8 text-3xl font-bold text-slate-900">Sign in</h1>
                        <p className="mt-2 text-slate-500">Access the association management portal.</p>
                    </div>

                    <form onSubmit={submit} className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Email</label>
                            <input
                                type="email"
                                value={form.data.email}
                                onChange={(e) => form.setData('email', e.target.value)}
                                className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500"
                                autoComplete="email"
                            />
                            {form.errors.email && <p className="mt-1 text-sm text-red-600">{form.errors.email}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Password</label>
                            <input
                                type="password"
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500"
                                autoComplete="current-password"
                            />
                            {form.errors.password && <p className="mt-1 text-sm text-red-600">{form.errors.password}</p>}
                        </div>

                        <label className="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" checked={form.data.remember} onChange={(e) => form.setData('remember', e.target.checked)} />
                            Remember me
                        </label>

                        <button
                            type="submit"
                            disabled={form.processing}
                            className="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60"
                        >
                            {form.processing ? 'Signing in…' : 'Sign in'}
                        </button>
                    </form>
                </div>
            </main>
        </>
    );
}