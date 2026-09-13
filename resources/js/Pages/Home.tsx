import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    CalendarDays,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    HeartHandshake,
    Landmark,
    ShieldCheck,
    Users,
    Vote,
} from 'lucide-react';
import { useEffect, useState } from 'react';
import PublicFooter from '../Components/PublicFooter';
import PublicHeader from '../Components/PublicHeader';

// Public media is intentionally remote/configurable. Replace these URLs with
// administrator-managed media later without changing the page structure.
const heroMedia = [
    {
        type: 'image' as const,
        src: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=2400&q=85',
        alt: 'People gathered together',
    },
    {
        type: 'image' as const,
        src: 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=2400&q=85',
        alt: 'Community members together',
    },
    {
        type: 'image' as const,
        src: 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=2400&q=85',
        alt: 'People collaborating',
    },
];

const showcaseSlides = [
    {
        eyebrow: 'Membership',
        title: 'A clearer home for every member.',
        text: 'Keep member profiles, participation, communication, and community records organized in one experience.',
        image: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1800&q=85',
        icon: Users,
    },
    {
        eyebrow: 'Welfare',
        title: 'Support people with structure and care.',
        text: 'Turn approved welfare decisions into transparent obligations, collections, and support records.',
        image: 'https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?auto=format&fit=crop&w=1800&q=85',
        icon: HeartHandshake,
    },
    {
        eyebrow: 'Governance',
        title: 'Preserve decisions beyond the people who made them.',
        text: 'Meetings, elections, resolutions, leadership history, and institutional records stay organized over time.',
        image: 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1800&q=85',
        icon: Landmark,
    },
];

const features = [
    { icon: Users, title: 'Membership', text: 'Profiles, participation, communication, and community records in one organized space.' },
    { icon: HeartHandshake, title: 'Welfare', text: 'Support proposals, approvals, collections, obligations, and accountability with clear workflows.' },
    { icon: Landmark, title: 'Contributions', text: 'Track contribution obligations, payments, balances, and financial activity with transparency.' },
    { icon: Vote, title: 'Governance', text: 'Support leadership, nominations, elections, meetings, resolutions, and institutional decisions.' },
];

function SectionEyebrow({ children }: { children: React.ReactNode }) {
    return (
        <span className="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-white/90 px-3 py-1.5 text-[9px] font-black uppercase tracking-[0.18em] text-emerald-700 shadow-sm backdrop-blur-md">
            <span className="h-1.5 w-1.5 rounded-full bg-emerald-500" />
            {children}
        </span>
    );
}

function HeroMedia({ active }: { active: number }) {
    const media = heroMedia[active];

    return (
        <div className="absolute inset-0 -z-20 overflow-hidden bg-slate-100">
            {media.type === 'video' ? (
                <video className="h-full w-full object-cover" src={media.src} autoPlay muted loop playsInline />
            ) : (
                <img className="h-full w-full object-cover" src={media.src} alt={media.alt} />
            )}
            <div className="absolute inset-0 bg-white/75 backdrop-blur-[4px]" />
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_50%_35%,rgba(255,255,255,0.2),rgba(248,250,252,0.86)_65%,rgba(248,250,252,0.98)_100%)]" />
        </div>
    );
}

function Hero() {
    const [activeMedia, setActiveMedia] = useState(0);

    useEffect(() => {
        const timer = window.setInterval(() => setActiveMedia((value) => (value + 1) % heroMedia.length), 7000);
        return () => window.clearInterval(timer);
    }, []);

    return (
        <section className="relative isolate flex min-h-[650px] items-center overflow-hidden border-b border-slate-200 bg-slate-50 sm:min-h-[700px] lg:min-h-[720px]">
            <HeroMedia active={activeMedia} />
            <div className="absolute left-[8%] top-[18%] -z-10 h-48 w-48 rounded-full bg-emerald-200/35 blur-3xl" />
            <div className="absolute right-[8%] top-[8%] -z-10 h-64 w-64 rounded-full bg-sky-200/40 blur-3xl" />

            <div className="mx-auto w-full max-w-5xl px-5 py-20 text-center sm:px-6 sm:py-24 lg:px-8 lg:py-28">
                <SectionEyebrow>Empower. Learn. Achieve.</SectionEyebrow>

                <h1 className="mx-auto mt-7 max-w-4xl text-[2.7rem] font-black leading-[0.98] tracking-[-0.055em] text-slate-950 sm:text-6xl lg:text-[5.35rem]">
                    A stronger community
                    <span className="block">builds a <span className="text-emerald-500">brighter tomorrow.</span></span>
                </h1>

                <p className="mx-auto mt-7 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base sm:leading-7">
                    Ochoka Heritage brings people, welfare, contributions, governance, and shared history together in one simple, trusted digital platform.
                </p>

                <div className="mt-7 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="#community" className="group inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-6 py-3.5 text-xs font-extrabold text-white shadow-lg shadow-emerald-500/20 transition hover:-translate-y-0.5 hover:bg-emerald-600 sm:text-sm">
                        Explore Our Community <ArrowRight size={16} className="transition-transform group-hover:translate-x-1" />
                    </a>
                    <a href="#about" className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white/95 px-6 py-3.5 text-xs font-extrabold text-slate-700 shadow-sm backdrop-blur-sm transition hover:border-slate-300 hover:bg-white sm:text-sm">
                        See How It Works
                    </a>
                </div>

                <div className="mx-auto mt-12 grid max-w-4xl grid-cols-2 overflow-hidden rounded-2xl border border-slate-200/90 bg-white/95 text-left shadow-xl shadow-slate-300/25 backdrop-blur-xl sm:grid-cols-4">
                    {[
                        ['Membership', 'Organized records'],
                        ['Welfare', 'Clear support workflows'],
                        ['Contributions', 'Traceable activity'],
                        ['Governance', 'Institutional continuity'],
                    ].map(([title, text], index) => (
                        <div key={title} className={`px-4 py-4 sm:px-5 ${index > 1 ? 'border-t border-slate-100 sm:border-l sm:border-t-0' : ''} ${index === 1 ? 'border-l border-slate-100 sm:border-l' : ''}`}>
                            <p className="text-[11px] font-black text-slate-950">{title}</p>
                            <p className="mt-1 text-[9px] leading-4 text-slate-400">{text}</p>
                        </div>
                    ))}
                </div>

                <div className="mt-5 flex items-center justify-center gap-2" aria-label="Hero background media selector">
                    {heroMedia.map((item, index) => (
                        <button key={item.src} type="button" onClick={() => setActiveMedia(index)} aria-label={`Show hero media ${index + 1}`} aria-current={activeMedia === index} className={`h-1.5 rounded-full transition-all ${activeMedia === index ? 'w-7 bg-emerald-500' : 'w-1.5 bg-slate-300 hover:bg-slate-400'}`} />
                    ))}
                </div>
            </div>
        </section>
    );
}

function CommunityShowcase() {
    const [active, setActive] = useState(0);
    const slide = showcaseSlides[active];
    const Icon = slide.icon;

    useEffect(() => {
        const timer = window.setInterval(() => setActive((value) => (value + 1) % showcaseSlides.length), 6500);
        return () => window.clearInterval(timer);
    }, []);

    return (
        <section className="relative overflow-hidden bg-slate-50 py-16 sm:py-20 lg:py-24">
            <div className="absolute left-[-10%] top-16 h-72 w-72 rounded-full bg-emerald-100/70 blur-3xl" aria-hidden="true" />
            <div className="absolute right-[-8%] bottom-10 h-80 w-80 rounded-full bg-sky-100/70 blur-3xl" aria-hidden="true" />

            <div className="relative mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
                <div className="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div className="max-w-2xl">
                        <SectionEyebrow>What we do</SectionEyebrow>
                        <h2 className="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                            Everything our community needs, <span className="text-emerald-500">in one place.</span>
                        </h2>
                        <p className="mt-3 max-w-xl text-sm leading-6 text-slate-500">Simple tools. Meaningful impact. A stronger community.</p>
                    </div>
                    <div className="flex gap-2 self-start sm:self-auto">
                        <button type="button" onClick={() => setActive((value) => (value - 1 + showcaseSlides.length) % showcaseSlides.length)} aria-label="Previous showcase" className="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-emerald-200 hover:text-emerald-600">
                            <ChevronLeft size={18} />
                        </button>
                        <button type="button" onClick={() => setActive((value) => (value + 1) % showcaseSlides.length)} aria-label="Next showcase" className="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-emerald-200 hover:text-emerald-600">
                            <ChevronRight size={18} />
                        </button>
                    </div>
                </div>

                <div className="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/50">
                    <div className="grid lg:grid-cols-[1.05fr_0.95fr]">
                        <div className="relative min-h-[520px] overflow-hidden sm:min-h-[580px] lg:min-h-[440px]">
                            <img key={slide.image} src={slide.image} alt="" className="absolute inset-0 h-full w-full object-cover transition duration-700" />
                            <div className="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/15 to-transparent lg:bg-gradient-to-tr lg:from-slate-950/35 lg:via-transparent lg:to-emerald-500/10" />

                            <div className="absolute inset-x-5 bottom-5 text-white sm:inset-x-7 sm:bottom-7 lg:bottom-7">
                                <div className="max-w-md rounded-2xl border border-white/30 bg-slate-950/35 p-4 shadow-xl backdrop-blur-md sm:p-5 lg:max-w-sm">
                                    <div className="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/90 px-3 py-1.5 text-[10px] font-extrabold text-slate-800 shadow-sm">
                                        <Icon size={14} className="text-emerald-600" /> Ochoka Heritage
                                    </div>
                                    <p className="mt-4 text-[10px] font-black uppercase tracking-[0.18em] text-emerald-300">{slide.eyebrow}</p>
                                    <h3 className="mt-1 text-2xl font-black tracking-tight text-white sm:text-3xl lg:hidden">{slide.title}</h3>
                                    <p className="mt-2 text-xs leading-5 text-white/85 lg:hidden">{slide.text}</p>
                                </div>
                            </div>
                        </div>

                        <div className="hidden flex-col justify-center p-7 sm:p-10 lg:flex lg:p-12">
                            <p className="text-[10px] font-black uppercase tracking-[0.18em] text-emerald-600">{slide.eyebrow}</p>
                            <h3 className="mt-3 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">{slide.title}</h3>
                            <p className="mt-4 text-sm leading-7 text-slate-600 sm:text-base">{slide.text}</p>
                            <Link href="#about" className="mt-6 inline-flex w-fit items-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-slate-800">
                                Learn more <ArrowRight size={15} />
                            </Link>
                        </div>
                    </div>

                    <div className="flex items-center justify-center gap-2 border-t border-slate-100 px-5 py-4">
                        {showcaseSlides.map((item, index) => (
                            <button key={item.eyebrow} type="button" onClick={() => setActive(index)} aria-label={`Show ${item.eyebrow}`} aria-current={active === index} className={`h-1.5 rounded-full transition-all ${active === index ? 'w-8 bg-emerald-500' : 'w-1.5 bg-slate-300 hover:bg-slate-400'}`} />
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}

export default function Home() {
    return (
        <div className="min-h-screen overflow-x-hidden bg-white text-slate-900">
            <PublicHeader />
            <main>
                <Hero />

                <section id="community" className="bg-white py-16 sm:py-20 lg:py-24">
                    <div className="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
                        <div className="mx-auto max-w-2xl text-center">
                            <SectionEyebrow>Our community</SectionEyebrow>
                            <h2 className="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">The essential parts of community life, connected.</h2>
                            <p className="mt-3 text-sm leading-6 text-slate-500">A shared digital foundation for members and leaders.</p>
                        </div>
                        <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            {features.map((feature) => {
                                const Icon = feature.icon;
                                return <article key={feature.title} className="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg hover:shadow-slate-200/60">
                                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-500 group-hover:text-white"><Icon size={21} /></div>
                                    <h3 className="mt-5 text-base font-extrabold text-slate-950">{feature.title}</h3>
                                    <p className="mt-2 text-sm leading-6 text-slate-500">{feature.text}</p>
                                    <a href="#about" className="mt-4 inline-flex items-center gap-1 text-xs font-extrabold text-emerald-600 hover:text-emerald-700">Learn more <ArrowRight size={13} /></a>
                                </article>;
                            })}
                        </div>
                    </div>
                </section>

                <CommunityShowcase />

                <section id="about" className="relative overflow-hidden border-y border-slate-200 bg-white py-16 sm:py-20 lg:py-24">
                    <div className="absolute -right-20 top-0 h-72 w-72 rounded-full bg-sky-100/60 blur-3xl" />
                    <div className="mx-auto grid max-w-7xl gap-10 px-5 sm:px-6 lg:grid-cols-[1fr_0.9fr] lg:items-center lg:px-8">
                        <div className="relative">
                            <SectionEyebrow>About Ochoka Heritage</SectionEyebrow>
                            <h2 className="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">More than a platform. <span className="text-emerald-500">A community legacy.</span></h2>
                            <p className="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">Community organizations carry relationships, responsibilities, decisions, leadership, memories, and shared history. Ochoka Heritage provides a structured digital foundation for managing those responsibilities while preserving the records that matter.</p>
                            <div className="mt-7 grid gap-3 sm:grid-cols-2">
                                {['Transparent and accountable', 'People-centered', 'Built for continuity', 'Organized institutional records'].map((item) => <div key={item} className="flex items-center gap-2 text-xs font-bold text-slate-600"><CheckCircle2 size={16} className="shrink-0 text-emerald-500" />{item}</div>)}
                            </div>
                        </div>
                        <div className="relative hidden overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-100 shadow-xl shadow-slate-200/50 lg:block">
                            <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1800&q=85" alt="Community members together" className="h-[330px] w-full object-cover sm:h-[400px]" />
                            <div className="absolute inset-x-5 bottom-5 rounded-2xl border border-white/50 bg-white/90 p-4 shadow-lg backdrop-blur-md sm:inset-x-7 sm:bottom-7">
                                <p className="text-xs font-black text-slate-950">Stronger together</p>
                                <p className="mt-1 text-[10px] text-slate-500">People • Heritage • Progress</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="governance" className="bg-slate-50 py-16 sm:py-20 lg:py-24">
                    <div className="mx-auto max-w-7xl px-5 text-center sm:px-6 lg:px-8">
                        <SectionEyebrow>Built for continuity</SectionEyebrow>
                        <h2 className="mx-auto mt-4 max-w-3xl text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Leadership changes. <span className="text-emerald-500">The institution remains.</span></h2>
                        <p className="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">Membership, financial activity, welfare decisions, meetings, elections, and governance history can remain part of the institution as people and leadership change.</p>
                        <div className="mx-auto mt-9 grid max-w-4xl gap-3 text-left sm:grid-cols-2">
                            {['Clear responsibilities and permissions', 'Traceable financial and welfare activity', 'Structured elections and governance', 'Searchable organizational records'].map((item) => <div key={item} className="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-bold text-slate-700 shadow-sm"><CheckCircle2 size={18} className="shrink-0 text-emerald-500" />{item}</div>)}
                        </div>
                    </div>
                </section>

                <section className="relative overflow-hidden bg-white py-16 sm:py-20 lg:py-24">
                    <div className="absolute left-1/2 top-1/2 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-100/50 blur-3xl" />
                    <div className="relative mx-auto max-w-4xl px-5 text-center sm:px-6 lg:px-8">
                        <SectionEyebrow>The journey starts together</SectionEyebrow>
                        <h2 className="mx-auto mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-5xl">Strong communities are built on trust, participation, and a shared future.</h2>
                        <p className="mx-auto mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">Ochoka Heritage provides the digital foundation to organize today’s responsibilities while preserving tomorrow’s institutional memory.</p>
                        <a href="#community" className="mt-7 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-emerald-500/20 transition hover:-translate-y-0.5 hover:bg-emerald-600">Explore the community <ArrowRight size={16} /></a>
                    </div>
                </section>
            </main>
            <PublicFooter />
        </div>
    );
}
