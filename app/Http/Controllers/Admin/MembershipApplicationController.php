<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\MembershipApplication;
use App\Services\MembershipApplicationService;
use App\Services\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
class MembershipApplicationController extends Controller
{
 public function index(Request $request,OrganizationContext $context): Response {
  $org=$context->forUser($request->user()); $status=$request->string('status')->toString();
  $allowed=[MembershipApplication::STATUS_DRAFT,MembershipApplication::STATUS_SUBMITTED,MembershipApplication::STATUS_UNDER_REVIEW,MembershipApplication::STATUS_APPROVED,MembershipApplication::STATUS_REJECTED,MembershipApplication::STATUS_WITHDRAWN];
  $applications=$org->membershipApplications()->when(in_array($status,$allowed,true),fn($q)=>$q->where('status',$status))->orderByDesc('created_at')->paginate(25)->withQueryString();
  return Inertia::render('Admin/MembershipApplications/Index',['organization'=>$org->only(['id','name']),'applications'=>$applications,'filters'=>['status'=>$status],'statuses'=>$allowed]);
 }
 public function show(Request $request,OrganizationContext $context,MembershipApplication $application): Response {
  $org=$context->forUser($request->user()); $this->ensureOrganization($application,$org->id);
  return Inertia::render('Admin/MembershipApplications/Show',['organization'=>$org->only(['id','name']),'application'=>$application->load(['documents','reviewer','approvedMember'])]);
 }
 public function review(Request $request,OrganizationContext $context,MembershipApplication $application,MembershipApplicationService $service): RedirectResponse { $org=$context->forUser($request->user()); $this->ensureOrganization($application,$org->id); $service->startReview($application,$request->user()); return back()->with('success','Application moved to review.'); }
 public function approve(Request $request,OrganizationContext $context,MembershipApplication $application,MembershipApplicationService $service): RedirectResponse { $org=$context->forUser($request->user()); $this->ensureOrganization($application,$org->id); $member=$service->approve($application,$request->user()); return redirect()->route('admin.members.edit',$member)->with('success','Application approved and membership created.'); }
 public function reject(Request $request,OrganizationContext $context,MembershipApplication $application,MembershipApplicationService $service): RedirectResponse { $org=$context->forUser($request->user()); $this->ensureOrganization($application,$org->id); $data=$request->validate(['reason'=>['required','string','max:2000']]); $service->reject($application,$request->user(),$data['reason']); return back()->with('success','Application rejected.'); }
 private function ensureOrganization(MembershipApplication $application,int $organizationId): void { abort_unless((int)$application->organization_id===$organizationId,404); }
}
