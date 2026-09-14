import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/register');
    };

    return (
        <>
            <Head title="Create account" />
            <main className="min-h-screen bg-slate-50 px-6 py-12 flex items-center justify-center">
                <div className="w-full max-w-md">
                    <div className="mb-8 text-center">
                        <Link href="/" className="text-2xl font-extrabold text-emerald-700">Ochoka Heritage</Link>
                        <h1 className="mt-8 text-3xl font-bold text-slate-900">Create account</h1>
                        <p className="mt-2 text-slate-500">Create your portal account. Membership is handled separately.</p>
                    </div>
                    <form onSubmit={submit} className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
                        {[
                            ['name', 'Full name', 'text', 'name'],
                            ['email', 'Email', 'email', 'email'],
                        ].map(([key, label, type, autocomplete]) => (
                            <div key={key}>
                                <label className="block text-sm font-semibold text-slate-700">{label}</label>
                                <input
                                    type={type}
                                    value={form.data[key as 'name' | 'email']}
                                    onChange={(e) => form.setData(key as 'name' | 'email', e.target.value)}
                                    className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500"
                                    autoComplete={autocomplete}
                                    required
                                />
                                {form.errors[key as 'name' | 'email'] && <p className="mt-1 text-sm text-red-600">{form.errors[key as 'name' | 'email']}</p>}
                            </div>
                        ))}
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Password</label>
                            <input type="password" value={form.data.password} onChange={(e) => form.setData('password', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="new-password" required />
                            <p className="mt-1 text-xs text-slate-500">Use at least 12 characters.</p>
                            {form.errors.password && <p className="mt-1 text-sm text-red-600">{form.errors.password}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Confirm password</label>
                            <input type="password" value={form.data.password_confirmation} onChange={(e) => form.setData('password_confirmation', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="new-password" required />
                        </div>
                        <button type="submit" disabled={form.processing} className="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60">
                            {form.processing ? 'Creating account…' : 'Create account'}
                        </button>
                        <p className="text-center text-sm text-slate-500">
                            Already have an account? <Link href="/login" className="font-semibold text-emerald-700">Sign in</Link>
                        </p>
                    </form>
                </div>
            </main>
        </>
    );
}
