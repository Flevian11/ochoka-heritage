import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

type Props = { channel?: 'email' | 'sms' | null };

export default function Otp({ channel }: Props) {
    const form = useForm({ code: '' });
    const resend = useForm({});

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/login/otp/verify');
    };

    return (
        <>
            <Head title="Enter verification code" />
            <main className="min-h-screen bg-slate-50 px-6 py-12 flex items-center justify-center">
                <div className="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                    <div className="text-center">
                        <Link href="/" className="text-2xl font-extrabold text-emerald-700">Ochoka Heritage</Link>
                        <h1 className="mt-8 text-2xl font-bold text-slate-900">Enter your code</h1>
                        <p className="mt-2 text-slate-500">Enter the 6-digit code sent to your {channel === 'sms' ? 'phone' : 'email address'}.</p>
                        <p className="mt-2 text-xs text-slate-400">If you did not receive a code, go back and request a new one.</p>
                    </div>
                    <form onSubmit={submit} className="mt-8 space-y-5">
                        <div>
                            <label className="block text-sm font-semibold text-slate-700">One-time code</label>
                            <input type="text" inputMode="numeric" maxLength={6} value={form.data.code} onChange={(e) => form.setData('code', e.target.value.replace(/\D/g, '').slice(0, 6))} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-2xl tracking-[0.4em] outline-none focus:border-emerald-500" autoComplete="one-time-code" required />
                            {form.errors.code && <p className="mt-1 text-sm text-red-600">{form.errors.code}</p>}
                            {form.errors.identifier && <p className="mt-1 text-sm text-red-600">{form.errors.identifier}</p>}
                        </div>
                        <button type="submit" disabled={form.processing} className="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60">
                            {form.processing ? 'Verifying…' : 'Verify and sign in'}
                        </button>
                    </form>
                    <button onClick={() => resend.post('/login/otp/resend')} disabled={resend.processing} className="mt-4 w-full text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                        {resend.processing ? 'Sending…' : 'Send a new code'}
                    </button>
                    <Link href="/login" className="mt-4 block text-center text-sm text-slate-500 hover:text-slate-700">Back to sign in</Link>
                </div>
            </main>
        </>
    );
}
