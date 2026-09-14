import { FormEvent, ReactNode } from 'react';
import { useForm } from '@inertiajs/react';

export type MemberFormData = {
    membership_number: string;
    first_name: string;
    middle_name: string;
    last_name: string;
    phone: string;
    alternate_phone: string;
    email: string;
    date_of_birth: string;
    national_id: string;
    address: string;
    city: string;
    county: string;
    joined_at: string;
    status: string;
    notes: string;
};

export default function MemberForm({
    data,
    errors,
    processing,
    onSubmit,
    setData,
    submitLabel,
    children,
}: {
    data: MemberFormData;
    errors: Record<string, string>;
    processing: boolean;
    onSubmit: (event: FormEvent) => void;
    setData: (key: keyof MemberFormData, value: string) => void;
    submitLabel: string;
    children?: ReactNode;
}) {
    const field = (key: keyof MemberFormData, label: string, type = 'text', required = false) => (
        <div>
            <label className="block text-sm font-semibold text-slate-700">{label}{required ? ' *' : ''}</label>
            <input type={type} value={data[key]} onChange={(e) => setData(key, e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" />
            {errors[key] && <p className="mt-1 text-sm text-red-600">{errors[key]}</p>}
        </div>
    );

    return (
        <form onSubmit={onSubmit} className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div className="grid gap-5 md:grid-cols-2">
                {field('membership_number', 'Membership number', 'text', true)}
                {field('first_name', 'First name', 'text', true)}
                {field('middle_name', 'Middle name')}
                {field('last_name', 'Last name', 'text', true)}
                {field('phone', 'Phone')}
                {field('alternate_phone', 'Alternate phone')}
                {field('email', 'Email', 'email')}
                {field('date_of_birth', 'Date of birth', 'date')}
                {field('national_id', 'National ID')}
                {field('joined_at', 'Joined date', 'date')}
                {field('city', 'City')}
                {field('county', 'County')}
                {field('address', 'Address')}
                <div>
                    <label className="block text-sm font-semibold text-slate-700">Status *</label>
                    <select value={data.status} onChange={(e) => setData('status', e.target.value)} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3">
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div className="md:col-span-2">
                    <label className="block text-sm font-semibold text-slate-700">Notes</label>
                    <textarea value={data.notes} onChange={(e) => setData('notes', e.target.value)} rows={4} className="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" />
                </div>
            </div>
            {children}
            <button disabled={processing} className="mt-6 rounded-xl bg-emerald-700 px-5 py-3 font-bold text-white disabled:opacity-60">{processing ? 'Saving…' : submitLabel}</button>
        </form>
    );
}