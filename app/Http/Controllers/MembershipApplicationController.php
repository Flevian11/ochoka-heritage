<?php
namespace App\Http\Controllers;
use App\Models\MembershipApplication;
use App\Models\Organization;
use App\Services\MembershipApplicationNumberGenerator;
use App\Services\MembershipApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
class MembershipApplicationController extends Controller
{
 public function create(): Response { return Inertia::render('Membership/Apply'); }
 public function store(Request $request, MembershipApplicationNumberGenerator $numbers, MembershipApplicationService $service): RedirectResponse {
  $data=$request->validate(['first_name'=>['required','string','max:100'],'middle_name'=>['nullable','string','max:100'],'last_name'=>['required','string','max:100'],'phone'=>['required','string','max:50'],'alternate_phone'=>['nullable','string','max:50'],'email'=>['required','email','max:255'],'date_of_birth'=>['required','date','before:today'],'national_id'=>['nullable','string','max:100'],'address'=>['nullable','string','max:255'],'city'=>['nullable','string','max:100'],'county'=>['nullable','string','max:100'],'eligibility_answers'=>['nullable','array']]);
  $organization=Organization::query()->where('slug','ochoka-heritage')->where('is_active',true)->firstOrFail();
  $data['organization_id']=$organization->id; $data['user_id']=$request->user()?->id; $data['application_number']=$numbers->generate();
  $application=MembershipApplication::create($data); $service->submit($application,$request->user());
  return redirect()->route('membership.application.create')->with('success','Your membership application has been submitted.');
 }
}
