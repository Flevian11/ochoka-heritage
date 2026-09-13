import { Link } from '@inertiajs/react';
import { Menu, X, ArrowRight } from 'lucide-react';
import { useState } from 'react';

const navigation = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/#about' },
    { label: 'Community', href: '/#community' },
    { label: 'Governance', href: '/#governance' },
    { label: 'Contact', href: '/#contact' },
];

export default function PublicHeader() {
    const [open, setOpen] = useState(false);

    return (
        <header className="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="flex min-h-[68px] items-center justify-between gap-4 sm:min-h-[72px]">
                    <Link href="/" className="flex min-w-0 items-center gap-2.5" onClick={() => setOpen(false)}>
                        <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-[11px] bg-emerald-500 text-xs font-black text-white shadow-sm shadow-emerald-500/20 sm:h-10 sm:w-10 sm:text-sm">
                            OH
                        </span>
                        <span className="min-w-0">
                            <span className="block truncate text-[14px] font-extrabold tracking-tight text-slate-950 sm:text-[15px]">
                                Ochoka Heritage
                            </span>
                            <span className="hidden text-[10px] font-medium text-slate-500 sm:block">
                                Community • Heritage • Progress
                            </span>
                        </span>
                    </Link>

                    <nav className="hidden items-center gap-6 lg:flex" aria-label="Main navigation">
                        {navigation.map((item) => (
                            <a key={item.label} href={item.href} className="relative py-2 text-[12px] font-bold text-slate-600 transition hover:text-emerald-600">
                                {item.label}
                            </a>
                        ))}
                    </nav>

                    <div className="hidden items-center gap-2 sm:flex">
                        <Link href="/login" className="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                            Member Login
                        </Link>
                        <a href="/#community" className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-500 px-4 py-2.5 text-xs font-extrabold text-white shadow-sm shadow-emerald-500/20 transition hover:-translate-y-0.5 hover:bg-emerald-600">
                            Get Started <ArrowRight size={13} />
                        </a>
                    </div>

                    <button type="button" className="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 sm:hidden" aria-label={open ? 'Close navigation' : 'Open navigation'} aria-expanded={open} onClick={() => setOpen((value) => !value)}>
                        {open ? <X size={19} /> : <Menu size={19} />}
                    </button>
                </div>

                {open && (
                    <div className="border-t border-slate-100 py-3 sm:hidden">
                        <nav className="grid gap-1" aria-label="Mobile navigation">
                            {navigation.map((item) => (
                                <a key={item.label} href={item.href} onClick={() => setOpen(false)} className="rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-emerald-600">
                                    {item.label}
                                </a>
                            ))}
                        </nav>
                        <div className="mt-2 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3">
                            <Link href="/login" onClick={() => setOpen(false)} className="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">
                                Member Login
                            </Link>
                            <a href="/#community" onClick={() => setOpen(false)} className="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white">
                                Get Started
                            </a>
                        </div>
                    </div>
                )}
            </div>
        </header>
    );
}
