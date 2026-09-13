import { Head } from '@inertiajs/react';
import {
    ArrowRight,
    CalendarDays,
    CheckCircle2,
    ChevronRight,
    HeartHandshake,
    Landmark,
    Menu,
    ShieldCheck,
    Users,
    Vote,
} from 'lucide-react';

type IconType = typeof Users;

interface Feature {
    icon: IconType;
    title: string;
    description: string;
}

interface Stat {
    value: string;
    label: string;
    description: string;
}

const features: Feature[] = [
    {
        icon: Users,
        title: 'Membership',
        description:
            'A clear digital home for members, profiles, participation and community communication.',
    },
    {
        icon: HeartHandshake,
        title: 'Welfare',
        description:
            'Coordinate community support through structured proposals, approvals and obligations.',
    },
    {
        icon: Landmark,
        title: 'Governance',
        description:
            'Keep leadership, decisions, meetings and organizational records structured and accessible.',
    },
    {
        icon: Vote,
        title: 'Elections',
        description:
            'Support transparent nominations, eligibility, voting and leadership transitions.',
    },
];

const principles = [
    'Transparent community records',
    'Structured welfare processes',
    'Accountable governance',
    'Continuity across leadership changes',
];

const heroStats: Stat[] = [
    {
        value: 'Community',
        label: 'MEMBERSHIP',
        description: 'People first',
    },
    {
        value: 'Welfare',
        label: 'SUPPORT',
        description: 'When it matters',
    },
    {
        value: 'Governance',
        label: 'LEADERSHIP',
        description: 'Clear & accountable',
    },
    {
        value: 'Heritage',
        label: 'CONTINUITY',
        description: 'Built to last',
    },
];

function BrandMark() {
    return (
        <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-black/[0.04]">
            <span className="text-sm font-black tracking-tight text-[#151c2e]">
                OH
            </span>
        </div>
    );
}

function SectionEyebrow({ children }: { children: React.ReactNode }) {
    return (
        <span className="inline-flex items-center gap-2 rounded-full border border-[#d9e4f3] bg-white px-4 py-2 text-[10px] font-extrabold uppercase tracking-[0.18em] text-[#2164c1] shadow-sm">
            <span className="h-1.5 w-1.5 rounded-full bg-[#2164c1]" />
            {children}
        </span>
    );
}

function PrimaryButton({
    children,
    href = '#about',
}: {
    children: React.ReactNode;
    href?: string;
}) {
    return (
        <a
            href={href}
            className="group inline-flex items-center justify-center gap-2 rounded-xl bg-[#2164c1] px-6 py-3.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(33,100,193,0.22)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#1955a5]"
        >
            {children}

            <ArrowRight
                size={16}
                className="transition-transform duration-200 group-hover:translate-x-0.5"
            />
        </a>
    );
}

function SecondaryButton({
    children,
    href = '#about',
}: {
    children: React.ReactNode;
    href?: string;
}) {
    return (
        <a
            href={href}
            className="inline-flex items-center justify-center rounded-xl border border-[#dbe2eb] bg-white px-6 py-3.5 text-sm font-bold text-[#273247] shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-[#f8fafc]"
        >
            {children}
        </a>
    );
}

function FeatureCard({ feature }: { feature: Feature }) {
    const Icon = feature.icon;

    return (
        <article className="group rounded-2xl border border-[#e5eaf1] bg-white p-6 shadow-[0_8px_30px_rgba(35,53,79,0.035)] transition duration-300 hover:-translate-y-1 hover:border-[#d6e1ee] hover:shadow-[0_18px_45px_rgba(35,53,79,0.08)]">
            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-[#edf5ff] text-[#2164c1]">
                <Icon size={20} strokeWidth={1.9} />
            </div>

            <h3 className="mt-5 text-lg font-extrabold tracking-tight text-[#1d2739]">
                {feature.title}
            </h3>

            <p className="mt-2.5 text-sm leading-6 text-[#718096]">
                {feature.description}
            </p>

            <div className="mt-5 inline-flex items-center gap-1 text-xs font-bold text-[#2164c1]">
                Explore
                <ChevronRight
                    size={14}
                    className="transition-transform group-hover:translate-x-1"
                />
            </div>
        </article>
    );
}

function HeroStats() {
    return (
        <div className="relative mx-auto mt-14 w-full max-w-4xl">
            <div className="absolute inset-0 rounded-[28px] bg-white/40 blur-xl" />

            <div className="relative grid overflow-hidden rounded-[22px] border border-white/80 bg-white/90 shadow-[0_20px_60px_rgba(39,61,91,0.12)] backdrop-blur-xl sm:grid-cols-2 lg:grid-cols-4">
                {heroStats.map((stat, index) => (
                    <div
                        key={stat.label}
                        className={`flex min-h-[88px] items-center gap-4 px-5 py-5 sm:px-6 ${
                            index !== 0
                                ? 'border-t border-[#e9edf3] sm:border-t-0 sm:border-l'
                                : ''
                        }`}
                    >
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#edf5ff] text-[#2164c1]">
                            {index === 0 && <Users size={17} />}
                            {index === 1 && <HeartHandshake size={17} />}
                            {index === 2 && <Landmark size={17} />}
                            {index === 3 && <ShieldCheck size={17} />}
                        </div>

                        <div className="min-w-0">
                            <div className="text-base font-black tracking-tight text-[#202b3d]">
                                {stat.value}
                            </div>

                            <div className="mt-0.5 text-[8px] font-black uppercase tracking-[0.13em] text-[#2164c1]">
                                {stat.label}
                            </div>

                            <div className="mt-1 text-[9px] text-[#9aa5b4]">
                                {stat.description}
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default function Home() {
    const siteUrl =
        typeof window !== 'undefined'
            ? window.location.origin
            : 'https://ochoka-heritage.example';

    return (
        <>
            <Head>
                <title>
                    Ochoka Heritage | Community, Welfare & Governance
                </title>

                <meta
                    name="description"
                    content="Ochoka Heritage brings community membership, welfare, contributions, governance, elections, meetings and organizational heritage together in one trusted digital platform."
                />

                <meta
                    name="keywords"
                    content="Ochoka Heritage, community, membership, welfare, contributions, governance, elections, meetings, community organization"
                />

                <meta
                    name="robots"
                    content="index, follow, max-image-preview:large"
                />

                <link rel="canonical" href={siteUrl} />

                <meta
                    property="og:title"
                    content="Ochoka Heritage | Community, Welfare & Governance"
                />

                <meta
                    property="og:description"
                    content="A modern digital home for community membership, welfare, contributions, governance and heritage."
                />

                <meta property="og:type" content="website" />
                <meta property="og:url" content={siteUrl} />

                <meta
                    name="twitter:card"
                    content="summary_large_image"
                />

                <meta
                    name="twitter:title"
                    content="Ochoka Heritage | Community, Welfare & Governance"
                />

                <meta
                    name="twitter:description"
                    content="A modern digital home for community membership, welfare, governance and heritage."
                />
            </Head>

            <div className="min-h-screen overflow-x-hidden bg-white text-[#1d2739]">

                {/* =====================================================
                    HEADER
                ====================================================== */}

                <header className="absolute left-0 right-0 top-0 z-50">
                    <div className="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
                        <div className="flex h-[78px] items-center justify-between border-b border-white/60">

                            <a
                                href="/"
                                className="flex items-center gap-3"
                            >
                                <BrandMark />

                                <div>
                                    <div className="text-[15px] font-extrabold tracking-tight text-[#151c2e]">
                                        Ochoka Heritage
                                    </div>

                                    <div className="text-[9px] font-medium text-[#6f7c8f]">
                                        Community • Heritage • Progress
                                    </div>
                                </div>
                            </a>

                            <nav className="hidden items-center gap-8 md:flex">
                                <a
                                    href="#about"
                                    className="text-[13px] font-semibold text-[#59677a] transition hover:text-[#2164c1]"
                                >
                                    About
                                </a>

                                <a
                                    href="#community"
                                    className="text-[13px] font-semibold text-[#59677a] transition hover:text-[#2164c1]"
                                >
                                    Community
                                </a>

                                <a
                                    href="#governance"
                                    className="text-[13px] font-semibold text-[#59677a] transition hover:text-[#2164c1]"
                                >
                                    Governance
                                </a>

                                <a
                                    href="#contact"
                                    className="text-[13px] font-semibold text-[#59677a] transition hover:text-[#2164c1]"
                                >
                                    Contact
                                </a>
                            </nav>

                            <div className="flex items-center gap-2">
                                <a
                                    href="/login"
                                    className="hidden rounded-xl border border-[#dce3eb] bg-white/80 px-4 py-2.5 text-[12px] font-bold text-[#293448] shadow-sm transition hover:bg-white sm:block"
                                >
                                    Member Login
                                </a>

                                <button
                                    type="button"
                                    aria-label="Open navigation"
                                    className="flex h-10 w-10 items-center justify-center rounded-xl border border-[#dce3eb] bg-white/80 text-[#45536a] shadow-sm md:hidden"
                                >
                                    <Menu size={18} />
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <main>

                    {/* =================================================
                        HERO
                    ================================================== */}

                    <section className="relative isolate overflow-hidden bg-[#f7faff]">

                        {/* Soft background image treatment */}
                        <div className="absolute inset-0 -z-20">
                            <div
                                className="absolute inset-0 bg-cover bg-center opacity-[0.16]"
                                style={{
                                    backgroundImage:
                                        "url('https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=2200&q=80')",
                                }}
                            />

                            <div className="absolute inset-0 bg-white/70" />

                            <div className="absolute inset-0 bg-gradient-to-b from-white/85 via-[#f5f9ff]/90 to-[#f8fbff]" />
                        </div>

                        {/* Soft colored light */}
                        <div className="pointer-events-none absolute -left-32 top-20 -z-10 h-[420px] w-[420px] rounded-full bg-[#dcecff] opacity-60 blur-[110px]" />

                        <div className="pointer-events-none absolute -right-20 top-10 -z-10 h-[420px] w-[420px] rounded-full bg-[#e7efff] opacity-70 blur-[120px]" />

                        <div className="mx-auto flex min-h-[760px] max-w-7xl flex-col items-center justify-center px-5 pb-20 pt-32 text-center sm:px-6 lg:px-8">

                            <SectionEyebrow>
                                Empower. Connect. Preserve.
                            </SectionEyebrow>

                            <h1 className="mx-auto mt-7 max-w-5xl text-[48px] font-black leading-[0.98] tracking-[-0.055em] text-[#10182a] sm:text-[64px] lg:text-[78px]">
                                Our community.
                                <br />
                                <span className="text-[#2164c1]">
                                    Our heritage.
                                </span>
                                <br />
                                Our future.
                            </h1>

                            <p className="mx-auto mt-7 max-w-2xl text-[15px] leading-7 text-[#5d6c81] sm:text-[17px]">
                                Ochoka Heritage brings membership, welfare,
                                contributions, governance, elections and
                                community records together in one trusted
                                digital platform.
                            </p>

                            <div className="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                                <PrimaryButton href="#community">
                                    Explore the community
                                </PrimaryButton>

                                <SecondaryButton href="#about">
                                    See how it works
                                </SecondaryButton>
                            </div>

                            <HeroStats />

                        </div>
                    </section>

                    {/* =================================================
                        ABOUT
                    ================================================== */}

                    <section
                        id="about"
                        className="bg-white"
                    >
                        <div className="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8 lg:py-28">

                            <div className="grid gap-14 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">

                                <div>
                                    <SectionEyebrow>
                                        About Ochoka Heritage
                                    </SectionEyebrow>

                                    <h2 className="mt-5 text-3xl font-black leading-tight tracking-[-0.035em] text-[#172033] sm:text-4xl">
                                        More than records.
                                        <span className="block text-[#2164c1]">
                                            A living community.
                                        </span>
                                    </h2>
                                </div>

                                <div>
                                    <p className="text-base leading-7 text-[#69778a]">
                                        Ochoka Heritage is designed to give
                                        members and leaders a shared digital
                                        foundation for the work that keeps a
                                        community strong.
                                    </p>

                                    <p className="mt-5 text-base leading-7 text-[#69778a]">
                                        From everyday membership activity to
                                        welfare support, financial
                                        accountability, meetings, elections
                                        and institutional history, important
                                        information belongs in one organized
                                        place.
                                    </p>
                                </div>

                            </div>

                        </div>
                    </section>

                    {/* =================================================
                        COMMUNITY
                    ================================================== */}

                    <section
                        id="community"
                        className="bg-[#f7faff]"
                    >
                        <div className="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8 lg:py-28">

                            <div className="mx-auto max-w-2xl text-center">
                                <SectionEyebrow>
                                    One connected platform
                                </SectionEyebrow>

                                <h2 className="mt-5 text-3xl font-black tracking-[-0.035em] text-[#172033] sm:text-4xl">
                                    Everything important,
                                    <span className="block text-[#2164c1]">
                                        organized together.
                                    </span>
                                </h2>

                                <p className="mt-5 text-sm leading-6 text-[#728095] sm:text-base">
                                    Purpose-built areas for the people,
                                    processes and responsibilities that make
                                    Ochoka Heritage work.
                                </p>
                            </div>

                            <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                                {features.map((feature) => (
                                    <FeatureCard
                                        key={feature.title}
                                        feature={feature}
                                    />
                                ))}
                            </div>

                        </div>
                    </section>

                    {/* =================================================
                        GOVERNANCE
                    ================================================== */}

                    <section
                        id="governance"
                        className="bg-white"
                    >
                        <div className="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8 lg:py-28">

                            <div className="grid gap-12 lg:grid-cols-2 lg:items-center">

                                <div>
                                    <SectionEyebrow>
                                        Governance & continuity
                                    </SectionEyebrow>

                                    <h2 className="mt-5 text-3xl font-black leading-tight tracking-[-0.035em] text-[#172033] sm:text-4xl">
                                        Leadership changes.
                                        <span className="block text-[#2164c1]">
                                            Heritage remains.
                                        </span>
                                    </h2>

                                    <p className="mt-5 max-w-xl text-base leading-7 text-[#69778a]">
                                        The platform is designed so that
                                        leadership transitions do not erase
                                        the organization's history. Members,
                                        decisions, welfare activity,
                                        elections and important records can
                                        remain part of a continuous
                                        institutional record.
                                    </p>

                                    <div className="mt-7 space-y-3">
                                        {principles.map((principle) => (
                                            <div
                                                key={principle}
                                                className="flex items-center gap-3"
                                            >
                                                <CheckCircle2
                                                    size={18}
                                                    className="shrink-0 text-[#2164c1]"
                                                />

                                                <span className="text-sm font-semibold text-[#526176]">
                                                    {principle}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                <div className="relative">
                                    <div className="absolute inset-8 rounded-[35px] bg-[#e6f0ff] blur-3xl" />

                                    <div className="relative rounded-[28px] border border-[#e0e7ef] bg-white p-5 shadow-[0_25px_70px_rgba(32,52,80,0.09)] sm:p-7">

                                        <div className="rounded-2xl bg-[#f6f9fd] p-5">

                                            <div className="flex items-center justify-between">
                                                <div>
                                                    <p className="text-[9px] font-black uppercase tracking-[0.16em] text-[#98a4b5]">
                                                        Institutional
                                                        foundation
                                                    </p>

                                                    <h3 className="mt-1 text-xl font-black text-[#202b3d]">
                                                        Built for continuity
                                                    </h3>
                                                </div>

                                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-[#eaf3ff] text-[#2164c1]">
                                                    <ShieldCheck size={19} />
                                                </div>
                                            </div>

                                            <div className="mt-6 space-y-3">
                                                {[
                                                    [
                                                        Users,
                                                        'Members',
                                                        'People and participation',
                                                    ],
                                                    [
                                                        HeartHandshake,
                                                        'Welfare',
                                                        'Support and community care',
                                                    ],
                                                    [
                                                        Vote,
                                                        'Elections',
                                                        'Representation and leadership',
                                                    ],
                                                    [
                                                        CalendarDays,
                                                        'Meetings',
                                                        'Decisions and action',
                                                    ],
                                                ].map(
                                                    ([Icon, title, description]) => {
                                                        const ItemIcon =
                                                            Icon as IconType;

                                                        return (
                                                            <div
                                                                key={
                                                                    title as string
                                                                }
                                                                className="flex items-center gap-3 rounded-xl border border-[#e4eaf1] bg-white p-3.5"
                                                            >
                                                                <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#edf5ff] text-[#2164c1]">
                                                                    <ItemIcon
                                                                        size={
                                                                            16
                                                                        }
                                                                    />
                                                                </div>

                                                                <div>
                                                                    <div className="text-xs font-extrabold text-[#354157]">
                                                                        {
                                                                            title as string
                                                                        }
                                                                    </div>

                                                                    <div className="mt-0.5 text-[10px] text-[#919dac]">
                                                                        {
                                                                            description as string
                                                                        }
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        );
                                                    },
                                                )}
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </section>

                    {/* =================================================
                        CTA
                    ================================================== */}

                    <section
                        id="contact"
                        className="bg-[#f7faff] px-5 py-20 sm:px-6 lg:px-8 lg:py-28"
                    >
                        <div className="mx-auto max-w-5xl overflow-hidden rounded-[30px] border border-[#dce7f4] bg-white px-6 py-16 text-center shadow-[0_20px_60px_rgba(32,55,85,0.06)] sm:px-12">

                            <SectionEyebrow>
                                Ochoka Heritage
                            </SectionEyebrow>

                            <h2 className="mx-auto mt-5 max-w-3xl text-3xl font-black tracking-[-0.035em] text-[#172033] sm:text-4xl">
                                Strong communities are built
                                <span className="text-[#2164c1]">
                                    {' '}
                                    together.
                                </span>
                            </h2>

                            <p className="mx-auto mt-5 max-w-2xl text-sm leading-6 text-[#728095] sm:text-base">
                                A shared digital foundation for our people,
                                responsibilities, decisions and heritage.
                            </p>

                            <div className="mt-8 flex justify-center">
                                <PrimaryButton href="#about">
                                    Discover Ochoka Heritage
                                </PrimaryButton>
                            </div>

                        </div>
                    </section>

                </main>

                {/* =====================================================
                    FOOTER
                ====================================================== */}

                <footer className="border-t border-[#e6ebf1] bg-white">
                    <div className="mx-auto max-w-7xl px-5 py-10 sm:px-6 lg:px-8">

                        <div className="flex flex-col gap-8 md:flex-row md:items-center md:justify-between">

                            <div className="flex items-center gap-3">
                                <BrandMark />

                                <div>
                                    <div className="text-sm font-extrabold text-[#273247]">
                                        Ochoka Heritage
                                    </div>

                                    <div className="text-[10px] text-[#929dad]">
                                        Community • Heritage • Progress
                                    </div>
                                </div>
                            </div>

                            <div className="flex flex-wrap gap-x-7 gap-y-3 text-xs font-semibold text-[#7c899b]">
                                <a href="#about" className="hover:text-[#2164c1]">
                                    About
                                </a>

                                <a
                                    href="#community"
                                    className="hover:text-[#2164c1]"
                                >
                                    Community
                                </a>

                                <a
                                    href="#governance"
                                    className="hover:text-[#2164c1]"
                                >
                                    Governance
                                </a>

                                <a href="#contact" className="hover:text-[#2164c1]">
                                    Contact
                                </a>
                            </div>

                        </div>

                        <div className="mt-8 border-t border-[#edf0f4] pt-6 text-xs text-[#9aa5b4]">
                            © {new Date().getFullYear()} Ochoka Heritage. All
                            rights reserved.
                        </div>

                    </div>
                </footer>

            </div>
        </>
    );
}