import { Head, Link, useForm } from '@inertiajs/react';

type Props = {
    email: string | null;
    phone: string | null;
    email_verified: boolean;
    phone_verified: boolean;
};

export default function VerifyEmail({ email, phone, email_verified, phone_verified }: Props) {
    const form = useForm({});
    const resend = () => form.post('/email/verification-notification');

    return (
        <>
            <Head title="Verify your contact" />
            <main className="min-h-screen bg-slate-50 px-6 py-12 flex items-center justify-center">
                <div className="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h1 className="text-2xl font-bold text-slate-900">Verify your contact</h1>
                    <p className="mt-3 text-slate-500">Verify at least one contact method before accessing the portal.</p>

                    {email && (
                        <div className="mt-6 rounded-xl bg-slate-50 p-4 text-left">
                            <p className="text-sm font-semibold text-slate-700">Email</p>
                            <p className="mt-1 text-sm text-slate-600">{email}</p>
                            <p className={`mt-2 text-xs font-semibold ${email_verified ? 'text-emerald-700' : 'text-amber-700'}`}>
                                {email_verified ? 'Verified' : 'Verification required'}
                            </p>
                        </div>
                    )}

                    {phone && (
                        <div className="mt-3 rounded-xl bg-slate-50 p-4 text-left">
                            <p className="text-sm font-semibold text-slate-700">Phone</p>
                            <p className="mt-1 text-sm text-slate-600">{phone}</p>
                            <p className={`mt-2 text-xs font-semibold ${phone_verified ? 'text-emerald-700' : 'text-amber-700'}`}>
                                {phone_verified ? 'Verified' : 'Verification required'}
                            </p>
                        </div>
                    )}

                    {email && !email_verified && (
                        <button onClick={resend} disabled={form.processing} className="mt-6 w-full rounded-xl bg-emerald-700 px-5 py-3 font-bold text-white hover:bg-emerald-800 disabled:opacity-60">
                            {form.processing ? 'Sending…' : 'Resend verification email'}
                        </button>
                    )}

                    {phone && !phone_verified && (
                        <Link href="/login" className="mt-3 block text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                            Use a one-time code instead
                        </Link>
                    )}

                    <form action="/logout" method="post" className="mt-5">
                        <input type="hidden" name="_token" value={(document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? ''} />
                        <button type="submit" className="text-sm font-semibold text-slate-500 hover:text-slate-700">Sign out</button>
                    </form>
                </div>
            </main>
        </>
    );
}
