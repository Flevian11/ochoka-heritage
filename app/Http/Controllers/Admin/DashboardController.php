<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrganizationContext;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, OrganizationContext $context): Response
    {
        $organization = $context->forUser($request->user());

        return Inertia::render('Admin/Dashboard', [
            'organization' => $organization->only(['id', 'name', 'currency', 'timezone']),
            'stats' => [
                'total_members' => $organization->members()->count(),
                'active_members' => $organization->members()->where('status', 'active')->count(),
                'pending_members' => $organization->members()->where('status', 'pending')->count(),
                'suspended_members' => $organization->members()->where('status', 'suspended')->count(),
            ],
            'permissions' => [
                'members_view' => $request->user()->hasPermission('members.view'),
                'members_manage' => $request->user()->hasPermission('members.manage'),
            ],
        ]);
    }
}