import { Head, useForm } from '@inertiajs/react';
import MemberLayout from '../../Components/MemberLayout';

type Props = {
    member: {
        membership_number: string | null; first_name: string; middle_name: string | null; last_name: string;
        phone: string | null; alternate_phone: string | null; email: string | null; date_of_birth: string | null;
        national_id: string | null; address: string | null; city: string | null; county: string | null; status: string;
    };
    organization: { id: number; name: string };
};

export default function Profile({ member, organization }: Props) {
    const form = useForm({
        phone: member.phone ?? '',
        alternate_phone: member.alternate_phone ?? '',
        date_of_birth: member.date_of_birth ?? '',
        national_id: member.national_id ?? '',
        address: member.address ?? '',
        city: member.city ?? '',
        county: member.county ?? '',
    });

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        form.put('/member/profile', { preserveScroll: true });
    };

    return (
        <MemberLayout organization={organization}>
            <Head title="My Profile" />
            <div>
                <p className="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700">My profile</p>
                <h1 className="mt-1 text-3xl font-bold tracking-tight">Membership details</h1>
                <p className="mt-2 text-slate-500">Review your registered information and update the fields available for member self-service.</p>
            </div>

            <div className="mt-8 grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold">Account identity</h2>
                    <dl className="mt-5 space-y-4 text-sm">
                        <div><dt className="text-slate-500">Membership number</dt><dd className="mt-1 font-semibold">{member.membership_number ?? 'Not assigned'}</dd></div>
                        <div><dt className="text-slate-500">Name</dt><dd className="mt-1 font-semibold">{[member.first_name, member.middle_name, member.last_name].filter(Boolean).join(' ')}</dd></div>
                        <div><dt className="text-slate-500">Email</dt><dd className="mt-1 font-semibold">{member.email ?? 'Not recorded'}</dd></div>
                        <div><dt className="text-slate-500">Membership status</dt><dd className="mt-1 font-semibold capitalize text-emerald-700">{member.status}</dd></div>
                    </dl>
                    <p className="mt-6 rounded-xl bg-slate-50 p-4 text-xs leading-5 text-slate-500">Identity fields such as your name and account email are not directly editable here. This protects the link between your authenticated account and your historical membership record.</p>
                </section>

                <form onSubmit={submit} className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold">Contact & personal details</h2>
                    <div className="mt-5 grid gap-5 sm:grid-cols-2">
                        <Field label="Phone" value={form.data.phone} onChange={(value) => form.setData('phone', value)} error={form.errors.phone} required />
                        <Field label="Alternate phone" value={form.data.alternate_phone} onChange={(value) => form.setData('alternate_phone', value)} error={form.errors.alternate_phone} />
                        <Field label="Date of birth" type="date" value={form.data.date_of_birth} onChange={(value) => form.setData('date_of_birth', value)} error={form.errors.date_of_birth} />
                        <Field label="National ID" value={form.data.national_id} onChange={(value) => form.setData('national_id', value)} error={form.errors.national_id} />
                        <Field label="City" value={form.data.city} onChange={(value) => form.setData('city', value)} error={form.errors.city} />
                        <Field label="County" value={form.data.county} onChange={(value) => form.setData('county', value)} error={form.errors.county} />
                        <label className="sm:col-span-2"><span className="text-sm font-semibold text-slate-700">Address</span><textarea value={form.data.address} onChange={(event) => form.setData('address', event.target.value)} rows={3} className="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100" />{form.errors.address && <span className="mt-1 block text-xs text-red-600">{form.errors.address}</span>}</label>
                    </div>
                    <div className="mt-6 flex items-center justify-between gap-4">
                        {form.recentlySuccessful && <p className="text-sm font-semibold text-emerald-700">Saved successfully.</p>}
                        <button disabled={form.processing} className="ml-auto rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-800 disabled:opacity-50">{form.processing ? 'Saving…' : 'Save changes'}</button>
                    </div>
                </form>
            </div>
        </MemberLayout>
    );
}

function Field({ label, value, onChange, error, type = 'text', required = false }: { label: string; value: string; onChange: (value: string) => void; error?: string; type?: string; required?: boolean }) {
    return <label><span className="text-sm font-semibold text-slate-700">{label}{required ? ' *' : ''}</span><input type={type} value={value} required={required} onChange={(event) => onChange(event.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100" />{error && <span className="mt-1 block text-xs text-red-600">{error}</span>}</label>;
}
