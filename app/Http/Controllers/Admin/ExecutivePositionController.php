<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ExecutivePosition;
use App\Services\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ExecutivePositionController extends Controller
{
    public function index(Request $request, OrganizationContext $context): Response
    {
        $org = $context->forUser($request->user());
        return Inertia::render('Admin/Governance/Positions/Index', [
            'organization' => $org->only(['id','name']),
            'positions' => $org->executivePositions()->withCount('appointments')->orderBy('display_order')->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request, OrganizationContext $context): Response
    {
        return Inertia::render('Admin/Governance/Positions/Create', ['organization' => $context->forUser($request->user())->only(['id','name'])]);
    }

    public function store(Request $request, OrganizationContext $context): RedirectResponse
    {
        $org = $context->forUser($request->user());
        $data = $this->validateData($request, $org->id);
        $position = $org->executivePositions()->create($data);
        $this->audit($request,$org->id,$position,'created',null,$position->fresh()->toArray());
        return redirect()->route('admin.governance.positions.index')->with('success','Executive position created successfully.');
    }

    public function edit(Request $request, OrganizationContext $context, ExecutivePosition $position): Response
    {
        $org=$context->forUser($request->user()); $this->ensureBelongs($position,$org->id);
        return Inertia::render('Admin/Governance/Positions/Edit',['organization'=>$org->only(['id','name']),'position'=>$position]);
    }

    public function update(Request $request, OrganizationContext $context, ExecutivePosition $position): RedirectResponse
    {
        $org=$context->forUser($request->user()); $this->ensureBelongs($position,$org->id);
        $data=$this->validateData($request,$org->id,$position->id); $previous=$position->fresh()->toArray();
        $position->update($data); $this->audit($request,$org->id,$position,'updated',$previous,$position->fresh()->toArray());
        return redirect()->route('admin.governance.positions.index')->with('success','Executive position updated successfully.');
    }

    public function destroy(Request $request, OrganizationContext $context, ExecutivePosition $position): RedirectResponse
    {
        $org=$context->forUser($request->user()); $this->ensureBelongs($position,$org->id);
        if($position->appointments()->exists()) return back()->with('error','This position has appointment history and cannot be deleted. Deactivate it instead.');
        $previous=$position->fresh()->toArray(); $position->delete();
        $this->audit($request,$org->id,$position,'deleted',$previous,null);
        return redirect()->route('admin.governance.positions.index')->with('success','Executive position archived successfully.');
    }

    private function validateData(Request $request,int $orgId,?int $ignore=null): array
    {
        return $request->validate([
            'name'=>['required','string','max:150'],
            'slug'=>['required','string','max:150','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',Rule::unique('executive_positions','slug')->where(fn($q)=>$q->where('organization_id',$orgId))->ignore($ignore)],
            'description'=>['nullable','string'],
            'display_order'=>['required','integer','min:0','max:10000'],
            'is_active'=>['required','boolean'],
        ]);
    }

    private function ensureBelongs(ExecutivePosition $position,int $orgId): void { abort_unless((int)$position->organization_id===$orgId,404); }
    private function audit(Request $request,int $orgId,ExecutivePosition $position,string $action,?array $previous,?array $new): void {
        AuditLog::create(['organization_id'=>$orgId,'user_id'=>$request->user()->id,'role_name'=>$request->user()->activeRoles()->value('name'),'action'=>$action,'module'=>'governance','auditable_type'=>$position::class,'auditable_id'=>$position->id,'previous_values'=>$previous,'new_values'=>$new,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent()]);
    }
}
