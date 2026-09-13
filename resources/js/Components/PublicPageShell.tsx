import type { ReactNode } from 'react';
import PublicFooter from './PublicFooter';
import PublicHeader from './PublicHeader';
import PublicPageBackdrop from './PublicPageBackdrop';

interface Props { eyebrow: string; title: ReactNode; intro: string; image: string; children: ReactNode; }

export default function PublicPageShell({ eyebrow, title, intro, image, children }: Props) {
    return <div className="min-h-screen bg-white text-slate-900">
        <PublicHeader />
        <PublicPageBackdrop image={image}>
            <main>
                <section className="mx-auto max-w-7xl px-5 pb-12 pt-14 sm:px-6 sm:pb-16 sm:pt-20 lg:px-8 lg:pb-20 lg:pt-24">
                    <div className="max-w-4xl">
                        <span className="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-white/90 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-700 shadow-sm backdrop-blur-md"><span className="h-1.5 w-1.5 rounded-full bg-emerald-500" />{eyebrow}</span>
                        <h1 className="mt-5 max-w-4xl text-4xl font-black leading-[1.02] tracking-[-0.055em] text-slate-950 sm:text-5xl lg:text-7xl">{title}</h1>
                        <p className="mt-5 max-w-3xl text-sm leading-7 text-slate-600 sm:text-base sm:leading-8 lg:text-lg">{intro}</p>
                    </div>
                </section>
                {children}
            </main>
        </PublicPageBackdrop>
        <PublicFooter />
    </div>;
}
