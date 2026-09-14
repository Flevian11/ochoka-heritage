<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Member;
use App\Services\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MemberPortalController extends Controller
{
    public function dashboard(Request $request, OrganizationContext $context): Response
    {
        $user = $request->user();
        $member = $user->member;

        if (! $member) {
            abort(403, 'This account is not linked to a member record.');
        }

        $organization = $context->forUser($user);

        abort_unless((int) $member->organization_id === (int) $organization->id, 403);

        $member->load([
            'organization:id,name,currency,timezone',
            'activeExecutiveAppointments.position:id,title',
            'statusHistories:id,member_id,from_status,to_status,reason,changed_at',
        ]);

        return Inertia::render('Member/Dashboard', [
            'member' => [
                'id' => $member->id,
                'membership_number' => $member->membership_number,
                'full_name' => $member->full_name,
                'status' => $member->status,
                'joined_at' => $member->joined_at?->toDateString(),
                'phone' => $member->phone,
                'email' => $member->email,
            ],
            'organization' => $organization->only(['id', 'name', 'currency', 'timezone']),
            'executive_appointments' => $member->activeExecutiveAppointments->map(fn ($appointment) => [
                'id' => $appointment->id,
                'title' => $appointment->position?->title,
                'starts_at' => $appointment->starts_at?->toDateString(),
                'ends_at' => $appointment->ends_at?->toDateString(),
            ])->values(),
            'status_history' => $member->statusHistories->take(8)->map(fn ($history) => [
                'id' => $history->id,
                'from_status' => $history->from_status,
                'to_status' => $history->to_status,
                'reason' => $history->reason,
                'changed_at' => $history->changed_at?->toIso8601String(),
            ])->values(),
        ]);
    }

    public function profile(Request $request, OrganizationContext $context): Response
    {
        $user = $request->user();
        $member = $user->member;

        if (! $member) {
            abort(403, 'This account is not linked to a member record.');
        }

        $organization = $context->forUser($user);
        abort_unless((int) $member->organization_id === (int) $organization->id, 403);

        return Inertia::render('Member/Profile', [
            'member' => [
                'membership_number' => $member->membership_number,
                'first_name' => $member->first_name,
                'middle_name' => $member->middle_name,
                'last_name' => $member->last_name,
                'phone' => $member->phone,
                'alternate_phone' => $member->alternate_phone,
                'email' => $member->email,
                'date_of_birth' => $member->date_of_birth?->toDateString(),
                'national_id' => $member->national_id,
                'address' => $member->address,
                'city' => $member->city,
                'county' => $member->county,
                'status' => $member->status,
            ],
            'organization' => $organization->only(['id', 'name']),
        ]);
    }

    public function updateProfile(Request $request, OrganizationContext $context): RedirectResponse
    {
        $user = $request->user();
        $member = $user->member;

        if (! $member) {
            abort(403, 'This account is not linked to a member record.');
        }

        $organization = $context->forUser($user);
        abort_unless((int) $member->organization_id === (int) $organization->id, 403);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:120'],
            'county' => ['nullable', 'string', 'max:120'],
        ]);

        $before = $member->only(array_keys($validated));

        DB::transaction(function () use ($member, $validated, $before, $request, $organization): void {
            $member->fill($validated);
            $member->save();

            $after = $member->only(array_keys($validated));
            $changes = array_filter($after, fn ($value, $key) => $before[$key] !== $value, ARRAY_FILTER_USE_BOTH);

            if ($changes === []) {
                return;
            }

            AuditLog::create([
                'organization_id' => $organization->id,
                'user_id' => $request->user()->id,
                'role_name' => 'member',
                'action' => 'profile_updated',
                'module' => 'member_portal',
                'auditable_type' => Member::class,
                'auditable_id' => $member->id,
                'previous_values' => array_intersect_key($before, $changes),
                'new_values' => $changes,
                'reason' => 'Member self-service profile update',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        });

        return back()->with('success', 'Your profile has been updated.');
    }
}
