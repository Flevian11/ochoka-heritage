import { Head, Link, router } from '@inertiajs/react';

type Position={id:number;name:string;slug:string;description?:string|null;display_order:number;is_active:boolean;appointments_count:number};
export default function Index({organization,positions}:{organization:{id:number;name:string};positions:Position[]}) {
 return <><Head title="Executive Positions"/><main className="min-h-screen bg-slate-50"><div className="mx-auto max-w-6xl px-6 py-8">
  <Link href="/admin" className="text-sm font-semibold text-emerald-700">← Dashboard</Link>
  <div className="mt-2 flex items-center justify-between"><div><h1 className="text-3xl font-bold text-slate-900">Executive Positions</h1><p className="text-slate-500">{organization.name}</p></div><Link href="/admin/governance/positions/create" className="rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white">Add position</Link></div>
  <div className="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white"><table className="w-full text-left"><thead className="bg-slate-50 text-xs uppercase text-slate-500"><tr><th className="px-5 py-4">Position</th><th className="px-5 py-4">Order</th><th className="px-5 py-4">Status</th><th className="px-5 py-4">Appointments</th><th className="px-5 py-4 text-right">Action</th></tr></thead><tbody className="divide-y divide-slate-100">
  {positions.map(p=><tr key={p.id}><td className="px-5 py-4"><div className="font-semibold">{p.name}</div><div className="text-sm text-slate-500">{p.slug}</div></td><td className="px-5 py-4">{p.display_order}</td><td className="px-5 py-4">{p.is_active?'Active':'Inactive'}</td><td className="px-5 py-4">{p.appointments_count}</td><td className="px-5 py-4 text-right"><Link href={`/admin/governance/positions/${p.id}/edit`} className="font-semibold text-emerald-700">Edit</Link>{p.appointments_count===0&&<button onClick={()=>{if(confirm('Archive this position?')) router.delete(`/admin/governance/positions/${p.id}`)}} className="ml-4 font-semibold text-red-600">Archive</button>}</td></tr>)}
  {positions.length===0&&<tr><td colSpan={5} className="px-5 py-12 text-center text-slate-500">No executive positions configured.</td></tr>}</tbody></table></div>
  <div className="mt-5"><Link href="/admin/governance/appointments" className="font-semibold text-emerald-700">Manage appointments →</Link></div>
 </div></main></>
}