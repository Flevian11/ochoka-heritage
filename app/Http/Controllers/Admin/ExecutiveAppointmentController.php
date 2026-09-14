<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ExecutiveAppointment;
use App\Models\ExecutivePosition;
use App\Models\Member;
use App\Services\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ExecutiveAppointmentController extends Controller
{
    public function index(Request $request, OrganizationContext $context): Response
    {
        $org=$context->forUser($request->user());
        return Inertia::render('Admin/Governance/Appointments/Index',['organization'=>$org->only(['id','name']),'appointments'=>$org->executiveAppointments()->with(['member:id,organization_id,membership_number,first_name,middle_name,last_name','position:id,organization_id,name'])->orderByDesc('starts_at')->orderByDesc('id')->paginate(25)]);
    }

    public function create(Request $request, OrganizationContext $context): Response
    {
        $org=$context->forUser($request->user());
        return Inertia::render('Admin/Governance/Appointments/Create',[
            'organization'=>$org->only(['id','name']),
            'members'=>$org->members()->whereIn('status',[Member::STATUS_ACTIVE,Member::STATUS_PENDING])->orderBy('last_name')->orderBy('first_name')->get(['id','membership_number','first_name','middle_name','last_name']),
            'positions'=>$org->executivePositions()->where('is_active',true)->orderBy('display_order')->orderBy('name')->get(['id','name']),
        ]);
    }

    public function store(Request $request, OrganizationContext $context): RedirectResponse
    {
        $org=$context->forUser($request->user());
        $data=$request->validate(['member_id'=>['required','integer'],'executive_position_id'=>['required','integer'],'starts_at'=>['required','date'],'ends_at'=>['nullable','date','after_or_equal:starts_at'],'reason'=>['nullable','string']]);

        $appointment = DB::transaction(function () use ($data, $org, $request) {
            $member=$org->members()->whereKey($data['member_id'])->lockForUpdate()->first();
            $position=$org->executivePositions()->where('is_active',true)->whereKey($data['executive_position_id'])->lockForUpdate()->first();
            if(!$member) throw ValidationException::withMessages(['member_id'=>'The selected member does not belong to this organization.']);
            if(!$position) throw ValidationException::withMessages(['executive_position_id'=>'The selected position does not belong to this organization or is inactive.']);
            if($this->hasOverlap($org->id,'executive_position_id',$position->id,$data['starts_at'],$data['ends_at']??null)) throw ValidationException::withMessages(['executive_position_id'=>'This position already has an overlapping active appointment.']);
            if($this->hasOverlap($org->id,'member_id',$member->id,$data['starts_at'],$data['ends_at']??null)) throw ValidationException::withMessages(['member_id'=>'This member already has an overlapping active executive appointment.']);
            return $org->executiveAppointments()->create(['member_id'=>$member->id,'executive_position_id'=>$position->id,'starts_at'=>$data['starts_at'],'ends_at'=>$data['ends_at']??null,'status'=>ExecutiveAppointment::STATUS_ACTIVE,'appointed_by'=>$request->user()->id,'appointed_at'=>now(),'reason'=>$data['reason']??null]);
        });
        $this->audit($request,$org->id,$appointment,'created',null,$appointment->fresh()->toArray());
        return redirect()->route('admin.governance.appointments.index')->with('success','Executive appointment created successfully.');
    }

    public function revoke(Request $request, OrganizationContext $context, ExecutiveAppointment $appointment): RedirectResponse
    {
        $org=$context->forUser($request->user()); $this->ensureBelongs($appointment,$org->id);
        if($appointment->status!==ExecutiveAppointment::STATUS_ACTIVE || $appointment->revoked_at!==null) return back()->with('error','Only an active appointment can be revoked.');
        $data=$request->validate(['reason'=>['nullable','string']]); $previous=$appointment->fresh()->toArray();
        $appointment->update(['status'=>ExecutiveAppointment::STATUS_REVOKED,'revoked_at'=>now(),'revoked_by'=>$request->user()->id,'reason'=>$data['reason']??$appointment->reason]);
        $this->audit($request,$org->id,$appointment,'revoked',$previous,$appointment->fresh()->toArray());
        return back()->with('success','Executive appointment revoked successfully.');
    }

    private function hasOverlap(int $orgId,string $column,int $value,string $start,?string $end): bool
    {
        return ExecutiveAppointment::where('organization_id',$orgId)->where($column,$value)->where('status',ExecutiveAppointment::STATUS_ACTIVE)
            ->where(function($q)use($start){$q->whereNull('ends_at')->orWhereDate('ends_at','>=',$start);})
            ->whereDate('starts_at','<=',$end??'9999-12-31')->exists();
    }
    private function ensureBelongs(ExecutiveAppointment $a,int $orgId):void { abort_unless((int)$a->organization_id===$orgId,404); }
    private function audit(Request $request,int $orgId,ExecutiveAppointment $a,string $action,?array $previous,?array $new):void { AuditLog::create(['organization_id'=>$orgId,'user_id'=>$request->user()->id,'role_name'=>$request->user()->activeRoles()->value('name'),'action'=>$action,'module'=>'governance','auditable_type'=>$a::class,'auditable_id'=>$a->id,'previous_values'=>$previous,'new_values'=>$new,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent()]); }
}
