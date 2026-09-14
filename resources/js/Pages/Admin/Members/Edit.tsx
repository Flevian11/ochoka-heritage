import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import MemberForm, { MemberFormData } from './Form';

export default function Edit({ member }: { member: MemberFormData & { id: number } }) {
    const form = useForm<MemberFormData>({
        membership_number: member.membership_number,
        first_name: member.first_name,
        middle_name: member.middle_name || '',
        last_name: member.last_name,
        phone: member.phone || '',
        alternate_phone: member.alternate_phone || '',
        email: member.email || '',
        date_of_birth: member.date_of_birth || '',
        national_id: member.national_id || '',
        address: member.address || '',
        city: member.city || '',
        county: member.county || '',
        joined_at: member.joined_at || '',
        status: member.status,
        notes: member.notes || '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.put(`/admin/members/${member.id}`);
    };

    return (
        <>
            <Head title="Edit member" />
            <main className="min-h-screen bg-slate-50">
                <div className="mx-auto max-w-4xl px-6 py-8">
                    <Link href="/admin/members" className="text-sm font-semibold text-emerald-700">← Members</Link>
                    <h1 className="mt-2 text-3xl font-bold text-slate-900">Edit member</h1>
                    <p className="mt-1 mb-6 text-slate-500">{member.membership_number}</p>
                    <MemberForm {...form} setData={(key, value) => form.setData(key, value)} onSubmit={submit} submitLabel="Save changes">
                        <p className="mt-5 text-xs text-slate-500">Changes to member records are recorded in the audit log.</p>
                    </MemberForm>
                </div>
            </main>
        </>
    );
}