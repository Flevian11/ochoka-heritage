import { Link, router } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { LayoutDashboard, LogOut, UserRound } from 'lucide-react';

type Props = {
    organization: { name: string };
    children: ReactNode;
};

export default function MemberLayout({ organization, children }: Props) {
    const logout = () => router.post('/logout');

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900">
            <header className="border-b border-slate-200 bg-white">
                <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <Link href="/member" className="text-xl font-extrabold tracking-tight text-emerald-700">
                        {organization.name}
                    </Link>
                    <div className="flex items-center gap-2">
                        <Link href="/member/profile" className="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">
                            <UserRound size={17} /> Profile
                        </Link>
                        <button onClick={logout} className="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">
                            <LogOut size={17} /> Sign out
                        </button>
                    </div>
                </div>
            </header>
            <nav className="border-b border-slate-200 bg-white">
                <div className="mx-auto flex max-w-7xl gap-2 px-6 py-2">
                    <Link href="/member" className="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700">
                        <LayoutDashboard size={16} /> Dashboard
                    </Link>
                    <Link href="/member/profile" className="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700">
                        <UserRound size={16} /> My profile
                    </Link>
                </div>
            </nav>
            <main className="mx-auto max-w-7xl px-6 py-8">{children}</main>
        </div>
    );
}
