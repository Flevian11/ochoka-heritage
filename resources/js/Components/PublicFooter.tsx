import { Link } from '@inertiajs/react';
import { ArrowUpRight, BookOpen, Compass, FileText, Mail, ShieldCheck, Users, HeartHandshake, Landmark } from 'lucide-react';

const explore = [
    ['About', '/#about', Compass],
    ['Community', '/#community', Users],
    ['Governance', '/#governance', Landmark],
    ['Contact', '/#contact', Mail],
] as const;

const legal = [
    ['Terms of Use', '/terms', FileText],
    ['Privacy Notice', '/privacy', ShieldCheck],
] as const;

export default function PublicFooter() {
    return (
        <footer id="contact" className="border-t border-slate-200/80 bg-white">
            <div className="mx-auto max-w-7xl px-5 py-14 sm:px-6 lg:px-8 lg:py-16">
                <div className="grid gap-11 lg:grid-cols-[1.55fr_0.8fr_0.8fr_1fr]">
                    <div className="max-w-md">
                        <Link href="/" className="inline-flex items-center gap-3">
                            <span className="flex h-11 w-11 items-center justify-center rounded-[13px] bg-emerald-500 text-sm font-black text-white shadow-lg shadow-emerald-500/20">OH</span>
                            <span>
                                <span className="block text-[15px] font-extrabold tracking-tight text-slate-950">Ochoka Heritage</span>
                                <span className="block text-[10px] font-medium text-slate-500">Community • Heritage • Progress</span>
                            </span>
                        </Link>
                        <p className="mt-5 max-w-sm text-sm leading-7 text-slate-500">
                            A trusted digital foundation for membership, welfare, contributions, governance, elections, meetings, and institutional memory.
                        </p>
                        <div className="mt-5 inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-[10px] font-bold text-emerald-700">
                            <ShieldCheck size={13} /> Built around trust and accountability
                        </div>
                    </div>

                    <FooterList title="Explore" items={explore} />
                    <FooterList title="Legal" items={legal} />

                    <div>
                        <h3 className="text-[11px] font-black uppercase tracking-[0.16em] text-slate-400">Member access</h3>
                        <p className="mt-4 text-sm leading-6 text-slate-500">Members and authorized leaders can access the secure community portal.</p>
                        <Link href="/login" className="mt-4 inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-extrabold text-white transition hover:bg-emerald-600">
                            Member Login <ArrowUpRight size={14} />
                        </Link>
                        <div className="mt-5 flex items-start gap-2 text-xs leading-5 text-slate-400"><Mail className="mt-0.5 shrink-0" size={13} /> Official contact details can be configured by administrators.</div>
                    </div>
                </div>

                <div className="mt-12 flex flex-col gap-4 border-t border-slate-100 pt-6 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                    <p>© {new Date().getFullYear()} Ochoka Heritage. All rights reserved.</p>
                    <div className="flex flex-wrap items-center gap-x-5 gap-y-2">
                        <Link href="/terms" className="hover:text-emerald-600">Terms</Link>
                        <Link href="/privacy" className="hover:text-emerald-600">Privacy</Link>
                        <span className="inline-flex items-center gap-1.5"><BookOpen size={12} /> Community • Heritage • Progress</span>
                    </div>
                </div>
            </div>
        </footer>
    );
}

function FooterList({ title, items }: { title: string; items: readonly (readonly [string, string, typeof Compass])[] }) {
    return (
        <div>
            <h3 className="text-[11px] font-black uppercase tracking-[0.16em] text-slate-400">{title}</h3>
            <ul className="mt-4 space-y-2.5 text-sm font-semibold text-slate-600">
                {items.map(([label, href, Icon]) => (
                    <li key={label}>
                        <a href={href} className="group inline-flex items-center gap-2.5 rounded-lg py-1 transition hover:text-emerald-600">
                            <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 text-slate-400 transition group-hover:bg-emerald-50 group-hover:text-emerald-600">
                                <Icon size={14} />
                            </span>
                            {label}
                        </a>
                    </li>
                ))}
            </ul>
        </div>
    );
}
