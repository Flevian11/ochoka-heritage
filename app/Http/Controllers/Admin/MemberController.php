<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Services\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function index(Request $request, OrganizationContext $context): Response
    {
        $organization = $context->forUser($request->user());
        $search = trim((string) $request->string('search'));
        $status = $request->string('status')->toString();

        $members = $organization->members()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('membership_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, [
                Member::STATUS_PENDING,
                Member::STATUS_ACTIVE,
                Member::STATUS_SUSPENDED,
                Member::STATUS_INACTIVE,
            ], true), fn ($query) => $query->where('status', $status))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Members/Index', [
            'organization' => $organization->only(['id', 'name']),
            'members' => $members,
            'filters' => ['search' => $search, 'status' => $status],
        ]);
    }

    public function create(Request $request, OrganizationContext $context): Response
    {
        return Inertia::render('Admin/Members/Create', [
            'organization' => $context->forUser($request->user())->only(['id', 'name']),
            'statuses' => [
                Member::STATUS_PENDING,
                Member::STATUS_ACTIVE,
                Member::STATUS_SUSPENDED,
                Member::STATUS_INACTIVE,
            ],
        ]);
    }

    public function store(Request $request, OrganizationContext $context): RedirectResponse
    {
        $organization = $context->forUser($request->user());

        $data = $request->validate([
            'membership_number' => ['required', 'string', 'max:100', 'unique:members,membership_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'alternate_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'national_id' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'county' => ['nullable', 'string', 'max:100'],
            'joined_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in([
                Member::STATUS_PENDING,
                Member::STATUS_ACTIVE,
                Member::STATUS_SUSPENDED,
                Member::STATUS_INACTIVE,
            ])],
            'notes' => ['nullable', 'string'],
        ]);

        $member = $organization->members()->create($data);

        $this->audit($request, $organization->id, $member, 'created', null, $member->fresh()->toArray());

        return redirect()->route('admin.members.index')->with('success', 'Member created successfully.');
    }

    public function edit(Request $request, OrganizationContext $context, Member $member): Response
    {
        $organization = $context->forUser($request->user());
        $this->ensureMemberBelongsToOrganization($member, $organization->id);

        return Inertia::render('Admin/Members/Edit', [
            'organization' => $organization->only(['id', 'name']),
            'member' => $member,
            'statuses' => [
                Member::STATUS_PENDING,
                Member::STATUS_ACTIVE,
                Member::STATUS_SUSPENDED,
                Member::STATUS_INACTIVE,
            ],
        ]);
    }

    public function update(Request $request, OrganizationContext $context, Member $member): RedirectResponse
    {
        $organization = $context->forUser($request->user());
        $this->ensureMemberBelongsToOrganization($member, $organization->id);

        $data = $request->validate([
            'membership_number' => ['required', 'string', 'max:100', Rule::unique('members', 'membership_number')->ignore($member->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'alternate_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'national_id' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'county' => ['nullable', 'string', 'max:100'],
            'joined_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in([
                Member::STATUS_PENDING,
                Member::STATUS_ACTIVE,
                Member::STATUS_SUSPENDED,
                Member::STATUS_INACTIVE,
            ])],
            'notes' => ['nullable', 'string'],
        ]);

        $previous = $member->fresh()->toArray();
        $member->update($data);
        $this->audit($request, $organization->id, $member, 'updated', $previous, $member->fresh()->toArray());

        return redirect()->route('admin.members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Request $request, OrganizationContext $context, Member $member): RedirectResponse
    {
        $organization = $context->forUser($request->user());
        $this->ensureMemberBelongsToOrganization($member, $organization->id);

        $previous = $member->fresh()->toArray();
        $member->delete();
        $this->audit($request, $organization->id, $member, 'deleted', $previous, null);

        return redirect()->route('admin.members.index')->with('success', 'Member archived successfully.');
    }

    private function ensureMemberBelongsToOrganization(Member $member, int $organizationId): void
    {
        abort_unless((int) $member->organization_id === $organizationId, 404);
    }

    private function audit(
        Request $request,
        int $organizationId,
        Member $member,
        string $action,
        ?array $previous,
        ?array $new
    ): void {
        AuditLog::create([
            'organization_id' => $organizationId,
            'user_id' => $request->user()->id,
            'role_name' => $request->user()->activeRoles()->value('name'),
            'action' => $action,
            'module' => 'members',
            'auditable_type' => $member::class,
            'auditable_id' => $member->id,
            'previous_values' => $previous,
            'new_values' => $new,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}