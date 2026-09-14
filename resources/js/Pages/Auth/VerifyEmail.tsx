import { Head, useForm } from '@inertiajs/react';

type Props = { email: string | null };

export default function VerifyEmail({ email }: Props) {
    const form = useForm({});
    const resend = () => form.post('/email/verification-notification');

    return (
        <>
            <Head title="Verify email" />
            <main className="min-h-screen bg-slate-50 px-6 py-12 flex items-center justify-center">
                <div className="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h1 className="text-2xl font-bold text-slate-900">Verify your email</h1>
                    <p className="mt-3 text-slate-500">Check your inbox for the verification link before accessing the portal.</p>
                    {email && <p className="mt-2 text-sm font-semibold text-slate-700">{email}</p>}
                    <button onClick={resend} disabled={form.processing} className="mt-6 rounded-xl bg-emerald-700 px-5 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60">
                        {form.processing ? 'Sending…' : 'Resend verification email'}
                    </button>
                    <form action="/logout" method="post" className="mt-4">
                        <input type="hidden" name="_token" value={(document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? ''} />
                        <button type="submit" className="text-sm font-semibold text-slate-500 hover:text-slate-700">Sign out</button>
                    </form>
                </div>
            </main>
        </>
    );
}
