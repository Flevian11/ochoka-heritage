import { FormEvent, useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login() {
    const [mode, setMode] = useState<'password' | 'otp'>('password');
    const [channel, setChannel] = useState<'email' | 'sms'>('email');

    const passwordForm = useForm({ email: '', password: '', remember: false });
    const otpForm = useForm({ identifier: '', channel });

    const submitPassword = (event: FormEvent) => {
        event.preventDefault();
        passwordForm.post('/login');
    };

    const submitOtp = (event: FormEvent) => {
        event.preventDefault();
        otpForm.clearErrors();
        otpForm.post('/login/otp');
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

                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="mb-6 grid grid-cols-2 rounded-xl bg-slate-100 p-1 text-sm font-semibold">
                            <button type="button" onClick={() => setMode('password')} className={`rounded-lg px-3 py-2 ${mode === 'password' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500'}`}>Password</button>
                            <button type="button" onClick={() => setMode('otp')} className={`rounded-lg px-3 py-2 ${mode === 'otp' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500'}`}>One-time code</button>
                        </div>

                        {mode === 'password' ? (
                            <form onSubmit={submitPassword} className="space-y-5">
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700">Email</label>
                                    <input type="email" value={passwordForm.data.email} onChange={(e) => passwordForm.setData('email', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="email" required />
                                    {passwordForm.errors.email && <p className="mt-1 text-sm text-red-600">{passwordForm.errors.email}</p>}
                                </div>
                                <div>
                                    <div className="flex items-center justify-between">
                                        <label className="block text-sm font-semibold text-slate-700">Password</label>
                                        <Link href="/forgot-password" className="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Forgot password?</Link>
                                    </div>
                                    <input type="password" value={passwordForm.data.password} onChange={(e) => passwordForm.setData('password', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500" autoComplete="current-password" required />
                                    {passwordForm.errors.password && <p className="mt-1 text-sm text-red-600">{passwordForm.errors.password}</p>}
                                </div>
                                <label className="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" checked={passwordForm.data.remember} onChange={(e) => passwordForm.setData('remember', e.target.checked)} />
                                    Remember me
                                </label>
                                <button type="submit" disabled={passwordForm.processing} className="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60">
                                    {passwordForm.processing ? 'Signing in…' : 'Sign in'}
                                </button>
                            </form>
                        ) : (
                            <form onSubmit={submitOtp} className="space-y-5">
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700">Send code by</label>
                                    <div className="mt-2 grid grid-cols-2 gap-2">
                                        <button type="button" onClick={() => { setChannel('email'); otpForm.setData('channel', 'email'); otpForm.clearErrors(); }} className={`rounded-xl border px-3 py-3 text-sm font-semibold ${channel === 'email' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-300 text-slate-600'}`}>Email</button>
                                        <button type="button" onClick={() => { setChannel('sms'); otpForm.setData('channel', 'sms'); otpForm.clearErrors(); }} className={`rounded-xl border px-3 py-3 text-sm font-semibold ${channel === 'sms' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-300 text-slate-600'}`}>Phone / SMS</button>
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700">{channel === 'email' ? 'Email address' : 'Phone number'}</label>
                                    <input
                                        type={channel === 'email' ? 'email' : 'tel'}
                                        value={otpForm.data.identifier}
                                        onChange={(e) => { otpForm.clearErrors('identifier'); otpForm.setData('identifier', e.target.value); }}
                                        placeholder={channel === 'email' ? 'you@example.com' : '+254 7xx xxx xxx'}
                                        className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-500"
                                        autoComplete={channel === 'email' ? 'email' : 'tel'}
                                        required
                                    />
                                    {otpForm.errors.identifier && <p className="mt-1 text-sm text-red-600">{otpForm.errors.identifier}</p>}
                                </div>
                                <p className="text-xs leading-5 text-slate-500">We will send a 6-digit code that expires after {10} minutes.</p>
                                <button type="submit" disabled={otpForm.processing} className="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60">
                                    {otpForm.processing ? 'Sending code…' : 'Send one-time code'}
                                </button>
                            </form>
                        )}

                        <p className="mt-6 text-center text-sm text-slate-500">
                            Need an account? <Link href="/register" className="font-semibold text-emerald-700">Create one</Link>
                        </p>
                    </div>
                </div>
            </main>
        </>
    );
}
