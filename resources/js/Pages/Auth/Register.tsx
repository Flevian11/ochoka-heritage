import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
    const form = useForm({ name: '', email: '', phone: '', password: '', password_confirmation: '' });
    const submit = (event: FormEvent) => { event.preventDefault(); form.post('/register'); };

    return (
        <>
            <Head title="Create account" />
            <main className="min-h-screen bg-slate-50 px-6 py-12 flex items-center justify-center">
                <div className="w-full max-w-md">
                    <div className="mb-8 text-center">
                        <Link href="/" className="text-2xl font-extrabold text-emerald-700">Ochoka Heritage</Link>
                        <h1 className="mt-8 text-3xl font-bold text-slate-900">Create account</h1>
                        <p className="mt-2 text-slate-500">Use an email address, phone number, or both.</p>
                    </div>
                    <form onSubmit={submit} className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Full name</label>
                            <input type="text" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="name" required />
                            {form.errors.name && <p className="mt-1 text-sm text-red-600">{form.errors.name}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Email <span className="font-normal text-slate-400">(optional)</span></label>
                            <input type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="email" />
                            {form.errors.email && <p className="mt-1 text-sm text-red-600">{form.errors.email}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Phone number <span className="font-normal text-slate-400">(optional)</span></label>
                            <input type="tel" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} placeholder="+254 7xx xxx xxx" className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="tel" />
                            <p className="mt-1 text-xs text-slate-500">Provide at least one contact method. Kenyan 07xx numbers are accepted.</p>
                            {form.errors.phone && <p className="mt-1 text-sm text-red-600">{form.errors.phone}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Password</label>
                            <input type="password" value={form.data.password} onChange={(e) => form.setData('password', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="new-password" required />
                            <p className="mt-1 text-xs text-slate-500">Use at least 12 characters.</p>
                            {form.errors.password && <p className="mt-1 text-sm text-red-600">{form.errors.password}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">Confirm password</label>
                            <input type="password" value={form.data.password_confirmation} onChange={(e) => form.setData('password_confirmation', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="new-password" required />
                            {form.errors.password_confirmation && <p className="mt-1 text-sm text-red-600">{form.errors.password_confirmation}</p>}
                        </div>
                        <button type="submit" disabled={form.processing} className="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60">
                            {form.processing ? 'Creating account…' : 'Create account'}
                        </button>
                        <p className="text-center text-sm text-slate-500">Already have an account? <Link href="/login" className="font-semibold text-emerald-700">Sign in</Link></p>
                    </form>
                </div>
            </main>
        </>
    );
}
