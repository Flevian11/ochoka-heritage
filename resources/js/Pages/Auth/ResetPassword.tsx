import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

type Props = { email: string; token: string };

export default function ResetPassword({ email, token }: Props) {
    const form = useForm({
        token,
        email,
        password: '',
        password_confirmation: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/reset-password');
    };

    return (
        <>
            <Head title="Set new password" />
            <main className="min-h-screen bg-slate-50 px-6 py-12 flex items-center justify-center">
                <div className="w-full max-w-md">
                    <div className="mb-8 text-center">
                        <Link href="/" className="text-2xl font-extrabold text-emerald-700">Ochoka Heritage</Link>
                        <h1 className="mt-8 text-3xl font-bold text-slate-900">Set a new password</h1>
                    </div>
                    <form onSubmit={submit} className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Email</label>
                            <input type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="email" required />
                        </div>
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">New password</label>
                            <input type="password" value={form.data.password} onChange={(e) => form.setData('password', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="new-password" required />
                            {form.errors.password && <p className="mt-1 text-sm text-red-600">{form.errors.password}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Confirm password</label>
                            <input type="password" value={form.data.password_confirmation} onChange={(e) => form.setData('password_confirmation', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="new-password" required />
                        </div>
                        <button type="submit" disabled={form.processing} className="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60">
                            {form.processing ? 'Saving…' : 'Reset password'}
                        </button>
                        <Link href="/login" className="block text-center text-sm font-semibold text-emerald-700">Back to sign in</Link>
                    </form>
                </div>
            </main>
        </>
    );
}
