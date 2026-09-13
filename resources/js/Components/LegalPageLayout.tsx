import type { ReactNode } from 'react';
import { FileText, ShieldCheck } from 'lucide-react';
import PublicFooter from './PublicFooter';
import PublicHeader from './PublicHeader';
import PublicPageBackdrop from './PublicPageBackdrop';

interface Section { title: string; body: string[] }

interface LegalPageLayoutProps {
    title: string;
    intro: string;
    effectiveDate: string;
    icon: 'terms' | 'privacy';
    sections: Section[];
    image: string;
    children?: ReactNode;
}

export default function LegalPageLayout({ title, intro, effectiveDate, icon, sections, image }: LegalPageLayoutProps) {
    const Icon = icon === 'privacy' ? ShieldCheck : FileText;

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900">
            <PublicHeader />
            <PublicPageBackdrop image={image}>
                <main className="mx-auto w-full max-w-[1440px] px-4 pb-16 pt-12 sm:px-6 sm:pt-16 lg:px-10 lg:pb-24 lg:pt-20">
                    <div className="text-center">
                        <span className="inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-white/90 bg-white/90 text-emerald-600 shadow-xl shadow-slate-400/20 backdrop-blur-md">
                            <Icon size={24} />
                        </span>
                        <p className="mt-5 text-[10px] font-black uppercase tracking-[0.24em] text-emerald-600">Legal &amp; transparency</p>
                        <h1 className="mx-auto mt-3 max-w-4xl text-4xl font-black tracking-[-0.055em] text-slate-950 sm:text-5xl lg:text-7xl">{title}</h1>
                        <p className="mx-auto mt-5 max-w-3xl text-sm leading-7 text-slate-600 sm:text-base lg:text-lg lg:leading-8">{intro}</p>
                        <p className="mt-3 text-xs font-semibold text-slate-400">Effective {effectiveDate}</p>
                    </div>

                    <div className="mx-auto mt-12 w-full rounded-[2rem] border border-slate-200/90 bg-white/95 shadow-2xl shadow-slate-300/30 backdrop-blur-xl lg:mt-16">
                        <div className="grid lg:grid-cols-[270px_minmax(0,1fr)]">
                            <aside className="hidden border-r border-slate-100 bg-slate-50/85 p-7 lg:block">
                                <div className="sticky top-24">
                                    <p className="text-[10px] font-black uppercase tracking-[0.16em] text-slate-400">On this page</p>
                                    <nav className="mt-5 space-y-1.5" aria-label="Legal page sections">
                                        {sections.map((section, index) => (
                                            <a key={section.title} href={`#legal-${index + 1}`} className="block rounded-xl px-3 py-2.5 text-xs font-semibold leading-5 text-slate-500 transition hover:bg-white hover:text-emerald-600 hover:shadow-sm">
                                                {section.title.replace(/^\d+\.\s*/, '')}
                                            </a>
                                        ))}
                                    </nav>
                                </div>
                            </aside>

                            <article className="min-w-0 p-6 sm:p-9 lg:p-14 xl:p-16">
                                <div className="rounded-2xl border border-emerald-100 bg-emerald-50/75 px-5 py-4 text-sm leading-7 text-emerald-900 sm:px-6">
                                    This page explains the general rules and practices that apply to use of the Ochoka Heritage platform. Where organizational policies or applicable law impose additional requirements, those requirements take precedence.
                                </div>

                                <div className="mt-10 grid gap-9 lg:grid-cols-2 lg:gap-x-14 lg:gap-y-11">
                                    {sections.map((section, index) => (
                                        <section key={section.title} id={`legal-${index + 1}`} className="scroll-mt-28 border-b border-slate-100 pb-9 last:border-b-0 lg:last:border-b lg:[&:nth-last-child(-n+2)]:border-b-0 lg:[&:nth-last-child(-n+2)]:pb-0">
                                            <div className="flex items-start gap-3">
                                                <span className="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[10px] font-black text-emerald-600">{String(index + 1).padStart(2, '0')}</span>
                                                <h2 className="text-lg font-extrabold tracking-tight text-slate-950 sm:text-xl">{section.title.replace(/^\d+\.\s*/, '')}</h2>
                                            </div>
                                            <div className="mt-4 space-y-3 pl-10 text-sm leading-7 text-slate-600 sm:text-[15px]">
                                                {section.body.map((paragraph) => <p key={paragraph}>{paragraph}</p>)}
                                            </div>
                                        </section>
                                    ))}
                                </div>

                                <div className="mt-12 border-t border-slate-100 pt-7 text-xs leading-6 text-slate-400">
                                    Please check this page periodically for the current published version.
                                </div>
                            </article>
                        </div>
                    </div>
                </main>
            </PublicPageBackdrop>
            <PublicFooter />
        </div>
    );
}
