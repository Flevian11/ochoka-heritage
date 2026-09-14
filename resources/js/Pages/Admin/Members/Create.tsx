import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import MemberForm, { MemberFormData } from './Form';

export default function Create() {
    const form = useForm<MemberFormData>({
        membership_number: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        phone: '',
        alternate_phone: '',
        email: '',
        date_of_birth: '',
        national_id: '',
        address: '',
        city: '',
        county: '',
        joined_at: '',
        status: 'pending',
        notes: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/admin/members');
    };

    return (
        <>
            <Head title="Add member" />
            <main className="min-h-screen bg-slate-50">
                <div className="mx-auto max-w-4xl px-6 py-8">
                    <Link href="/admin/members" className="text-sm font-semibold text-emerald-700">← Members</Link>
                    <h1 className="mt-2 text-3xl font-bold text-slate-900">Add member</h1>
                    <p className="mt-1 mb-6 text-slate-500">Create a member record without forcing an account to exist.</p>
                    <MemberForm {...form} setData={(key, value) => form.setData(key, value)} onSubmit={submit} submitLabel="Create member" />
                </div>
            </main>
        </>
    );
}