import { FormEvent } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';

type Member = {
    id: number;
    membership_number: string;
    first_name: string;
    middle_name?: string | null;
    last_name: string;
    phone?: string | null;
    email?: string | null;
    status: string;
};

type Props = {
    organization: { id: number; name: string };
    members: { data: Member[]; links: { url: string | null; label: string; active: boolean }[] };
    filters: { search: string; status: string };
};

const statusLabel = (value: string) => value.replace('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase());

export default function Index({ organization, members, filters }: Props) {
    const form = useForm({ search: filters.search, status: filters.status });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        router.get('/admin/members', form.data, { preserveState: true, replace: true });
    };

    return (
        <>
            <Head title="Members" />
            <main className="min-h-screen bg-slate-50">
                <div className="mx-auto max-w-7xl px-6 py-8">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <Link href="/admin" className="text-sm font-semibold text-emerald-700">← Dashboard</Link>
                            <h1 className="mt-2 text-3xl font-bold text-slate-900">Members</h1>
                            <p className="mt-1 text-slate-500">{organization.name}</p>
                        </div>
                        <Link href="/admin/members/create" className="rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white">Add member</Link>
                    </div>

                    <form onSubmit={submit} className="mt-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_220px_auto]">
                        <input
                            value={form.data.search}
                            onChange={(e) => form.setData('search', e.target.value)}
                            placeholder="Search name, membership no., phone or email"
                            className="rounded-xl border border-slate-300 px-4 py-3"
                        />
                        <select value={form.data.status} onChange={(e) => form.setData('status', e.target.value)} className="rounded-xl border border-slate-300 px-4 py-3">
                            <option value="">All statuses</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button className="rounded-xl bg-slate-900 px-5 py-3 font-bold text-white">Filter</button>
                    </form>

                    <div className="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[760px] text-left">
                                <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th className="px-5 py-4">Member</th>
                                        <th className="px-5 py-4">Membership no.</th>
                                        <th className="px-5 py-4">Contact</th>
                                        <th className="px-5 py-4">Status</th>
                                        <th className="px-5 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {members.data.map((member) => (
                                        <tr key={member.id}>
                                            <td className="px-5 py-4">
                                                <p className="font-semibold text-slate-900">{[member.first_name, member.middle_name, member.last_name].filter(Boolean).join(' ')}</p>
                                            </td>
                                            <td className="px-5 py-4 text-slate-600">{member.membership_number}</td>
                                            <td className="px-5 py-4 text-sm text-slate-600">{member.phone || member.email || '—'}</td>
                                            <td className="px-5 py-4"><span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold">{statusLabel(member.status)}</span></td>
                                            <td className="px-5 py-4 text-right"><Link href={`/admin/members/${member.id}/edit`} className="font-semibold text-emerald-700">Edit</Link></td>
                                        </tr>
                                    ))}
                                    {members.data.length === 0 && <tr><td colSpan={5} className="px-5 py-12 text-center text-slate-500">No members found.</td></tr>}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </>
    );
}