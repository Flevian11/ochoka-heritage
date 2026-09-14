import { Head, Link, router } from '@inertiajs/react';

type Props = {
    organization: { id: number; name: string; currency: string; timezone: string };
    stats: {
        total_members: number;
        active_members: number;
        pending_members: number;
        suspended_members: number;
    };
};

export default function Dashboard({ organization, stats }: Props) {
    const logout = () => router.post('/logout');

    const cards = [
        ['Total members', stats.total_members],
        ['Active members', stats.active_members],
        ['Pending approval', stats.pending_members],
        ['Suspended', stats.suspended_members],
    ];

    return (
        <>
            <Head title="Admin Dashboard" />
            <div className="min-h-screen bg-slate-50">
                <header className="border-b border-slate-200 bg-white">
                    <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                        <Link href="/admin" className="text-xl font-extrabold text-emerald-700">{organization.name}</Link>
                        <button onClick={logout} className="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Sign out</button>
                    </div>
                </header>

                <main className="mx-auto max-w-7xl px-6 py-10">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p className="text-sm font-semibold uppercase tracking-wide text-emerald-700">Administration</p>
                            <h1 className="mt-1 text-3xl font-bold text-slate-900">Dashboard</h1>
                            <p className="mt-2 text-slate-500">Operational overview for {organization.name}.</p>
                        </div>
                        <Link href="/admin/members" className="rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-800">
                            Manage members
                        </Link>
                    </div>

                    <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        {cards.map(([label, value]) => (
                            <div key={label} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <p className="text-sm text-slate-500">{label}</p>
                                <p className="mt-2 text-3xl font-bold text-slate-900">{value}</p>
                            </div>
                        ))}
                    </div>
                </main>
            </div>
        </>
    );
}