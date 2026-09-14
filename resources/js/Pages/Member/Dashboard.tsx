import { Head, Link } from '@inertiajs/react';
import { CalendarDays, CheckCircle2, ShieldCheck, UserRound } from 'lucide-react';
import MemberLayout from '../../Components/MemberLayout';

type Appointment = { id: number; title: string | null; starts_at: string | null; ends_at: string | null };
type History = { id: number; from_status: string | null; to_status: string; reason: string; changed_at: string | null };
type Props = {
    member: { id: number; membership_number: string | null; full_name: string; status: string; joined_at: string | null; phone: string | null; email: string | null };
    organization: { id: number; name: string; currency: string; timezone: string };
    executive_appointments: Appointment[];
    status_history: History[];
};

const statusLabel = (value: string) => value.replace(/_/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());

export default function Dashboard({ member, organization, executive_appointments, status_history }: Props) {
    return (
        <MemberLayout organization={organization}>
            <Head title="Member Dashboard" />
            <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700">Member portal</p>
                    <h1 className="mt-1 text-3xl font-bold tracking-tight">Welcome, {member.full_name}</h1>
                    <p className="mt-2 text-slate-500">Your membership information and community account at a glance.</p>
                </div>
                <Link href="/member/profile" className="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-800">
                    <UserRound size={17} /> Manage profile
                </Link>
            </div>

            <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p className="text-sm text-slate-500">Membership number</p>
                    <p className="mt-2 text-lg font-bold text-slate-900">{member.membership_number ?? 'Not assigned'}</p>
                </div>
                <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p className="text-sm text-slate-500">Membership status</p>
                    <p className="mt-2 inline-flex items-center gap-2 text-lg font-bold text-emerald-700"><CheckCircle2 size={19} /> {statusLabel(member.status)}</p>
                </div>
                <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p className="text-sm text-slate-500">Joined</p>
                    <p className="mt-2 text-lg font-bold text-slate-900">{member.joined_at ?? 'Not recorded'}</p>
                </div>
                <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p className="text-sm text-slate-500">Organization</p>
                    <p className="mt-2 text-lg font-bold text-slate-900">{organization.name}</p>
                </div>
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-xl bg-emerald-50 p-2.5 text-emerald-700"><ShieldCheck size={20} /></div>
                        <div><h2 className="font-bold">Current responsibilities</h2><p className="text-sm text-slate-500">Active executive appointments linked to your member record.</p></div>
                    </div>
                    <div className="mt-5 space-y-3">
                        {executive_appointments.length === 0 ? <p className="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">You currently have no active executive appointment.</p> : executive_appointments.map((appointment) => (
                            <div key={appointment.id} className="rounded-xl border border-slate-200 p-4">
                                <p className="font-semibold">{appointment.title ?? 'Executive position'}</p>
                                <p className="mt-1 text-sm text-slate-500">From {appointment.starts_at ?? '—'}{appointment.ends_at ? ` to ${appointment.ends_at}` : ''}</p>
                            </div>
                        ))}
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-xl bg-emerald-50 p-2.5 text-emerald-700"><CalendarDays size={20} /></div>
                        <div><h2 className="font-bold">Membership history</h2><p className="text-sm text-slate-500">Recent status changes preserved for your record.</p></div>
                    </div>
                    <div className="mt-5 space-y-3">
                        {status_history.length === 0 ? <p className="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">No status history is available.</p> : status_history.map((item) => (
                            <div key={item.id} className="rounded-xl border border-slate-200 p-4">
                                <p className="font-semibold">{statusLabel(item.to_status)}</p>
                                <p className="mt-1 text-sm text-slate-500">{item.changed_at ? new Date(item.changed_at).toLocaleString() : 'Date not recorded'}</p>
                                <p className="mt-2 text-sm text-slate-600">{item.reason}</p>
                            </div>
                        ))}
                    </div>
                </section>
            </div>

            <div className="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-5 text-sm text-emerald-900">
                Financial, welfare, meetings, and notifications self-service will be connected to their respective audited modules rather than duplicating or mutating their records here.
            </div>
        </MemberLayout>
    );
}
