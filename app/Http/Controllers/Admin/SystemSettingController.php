<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Services\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SystemSettingController extends Controller
{
    public function index(Request $request, OrganizationContext $context): Response
    {
        $org = $context->forUser($request->user());
        return Inertia::render('Admin/Settings/Index', [
            'organization' => $org->only(['id','name']),
            'settings' => $org->settings()->current()->orderBy('group')->orderBy('key')->get(),
        ]);
    }

    public function store(Request $request, OrganizationContext $context): RedirectResponse
    {
        $org = $context->forUser($request->user());
        $data = $this->validateData($request, $org->id);
        $this->validateValue($data['value'], $data['type']);
        $this->ensureNoOverlappingVersion($org->id, $data['key'], $data['effective_from']);
        $setting = $org->settings()->create($data);
        $this->audit($request, $org->id, $setting, 'created', null, $setting->fresh()->toArray());
        return back()->with('success', 'Setting created successfully.');
    }

    public function update(Request $request, OrganizationContext $context, SystemSetting $setting): RedirectResponse
    {
        $org = $context->forUser($request->user());
        $this->ensureBelongs($setting, $org->id);
        $data = $request->validate([
            'value' => ['nullable','string'],
            'type' => ['required', Rule::in(['string','integer','decimal','boolean','json'])],
            'group' => ['required','string','max:100'],
            'description' => ['nullable','string'],
            'is_public' => ['required','boolean'],
            'effective_from' => ['required','date'],
        ]);
        $this->validateValue($data['value'], $data['type']);
        $effectiveFrom = $data['effective_from'];
        if (now()->startOfDay()->gt(now()->parse($effectiveFrom)->startOfDay())) {
            throw ValidationException::withMessages(['effective_from' => 'A new setting version cannot begin in the past.']);
        }

        $previous = $setting->fresh()->toArray();
        $new = DB::transaction(function () use ($setting, $data, $effectiveFrom, $org) {
            $latest = $org->settings()->where('key', $setting->key)->orderByDesc('effective_from')->lockForUpdate()->first();
            if ($latest && (int) $latest->id !== (int) $setting->id && $latest->effective_from->gte(now()->parse($effectiveFrom))) {
                throw ValidationException::withMessages(['effective_from' => 'The effective date must be after the latest version of this setting.']);
            }
            if ((int) $setting->id === (int) ($latest?->id ?? 0) && $setting->effective_from->eq(now()->parse($effectiveFrom))) {
                throw ValidationException::withMessages(['effective_from' => 'Use a future effective date to create a new version.']);
            }
            $newSetting = $org->settings()->create([
                'key' => $setting->key,
                'value' => $data['value'] ?? null,
                'type' => $data['type'],
                'group' => $data['group'],
                'description' => $data['description'] ?? null,
                'is_public' => $data['is_public'],
                'effective_from' => $effectiveFrom,
            ]);
            if ($setting->effective_until === null && $setting->effective_from->lt($newSetting->effective_from)) {
                $setting->update(['effective_until' => $newSetting->effective_from->toDateString()]);
            }
            return $newSetting;
        });

        $this->audit($request, $org->id, $new, 'updated', $previous, $new->fresh()->toArray());
        return back()->with('success', 'A new setting version was created successfully.');
    }

    public function destroy(Request $request, OrganizationContext $context, SystemSetting $setting): RedirectResponse
    {
        $org = $context->forUser($request->user());
        $this->ensureBelongs($setting, $org->id);
        $previous = $setting->fresh()->toArray();
        if ($setting->effective_until !== null) {
            return back()->with('error', 'This setting version is already retired.');
        }
        $retirementBoundary = now()->startOfDay()->lt($setting->effective_from->startOfDay())
            ? $setting->effective_from->toDateString()
            : now()->toDateString();
        $setting->update(['effective_until' => $retirementBoundary]);
        $this->audit($request, $org->id, $setting, 'retired', $previous, $setting->fresh()->toArray());
        return back()->with('success', 'Setting retired successfully; its history was preserved.');
    }

    private function validateData(Request $request, int $orgId): array
    {
        $data = $request->validate([
            'key' => ['required','string','max:150','regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/'],
            'value' => ['nullable','string'],
            'type' => ['required', Rule::in(['string','integer','decimal','boolean','json'])],
            'group' => ['required','string','max:100'],
            'description' => ['nullable','string'],
            'is_public' => ['required','boolean'],
            'effective_from' => ['nullable','date'],
        ]);
        $data['effective_from'] = $data['effective_from'] ?? now()->toDateString();
        $existing = SystemSetting::query()->where('organization_id',$orgId)->where('key',$data['key'])->current()->exists();
        if ($existing && $data['effective_from'] <= now()->toDateString()) {
            throw ValidationException::withMessages(['key' => 'This setting already exists. Create a future version instead.']);
        }
        return $data;
    }

    private function validateValue(?string $value, string $type): void
    {
        if ($value === null || $value === '') return;
        $valid = match ($type) {
            'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false,
            'decimal' => is_numeric($value),
            'boolean' => in_array(strtolower($value), ['true','false','1','0','yes','no'], true),
            'json' => json_validate($value),
            default => true,
        };
        if (! $valid) throw ValidationException::withMessages(['value' => "The value is not valid for type {$type}."]);
    }

    private function ensureNoOverlappingVersion(int $orgId, string $key, string $effectiveFrom): void
    {
        $existing = SystemSetting::query()->where('organization_id',$orgId)->where('key',$key)->whereDate('effective_from',$effectiveFrom)->exists();
        if ($existing) throw ValidationException::withMessages(['effective_from' => 'A setting version already exists for this effective date.']);
    }

    private function ensureBelongs(SystemSetting $setting, int $orgId): void { abort_unless((int) $setting->organization_id === $orgId, 404); }

    private function audit(Request $request, int $orgId, SystemSetting $setting, string $action, ?array $previous, ?array $new): void
    {
        AuditLog::create([
            'organization_id'=>$orgId,'user_id'=>$request->user()->id,'role_name'=>$request->user()->activeRoles()->value('name'),
            'action'=>$action,'module'=>'settings','auditable_type'=>$setting::class,'auditable_id'=>$setting->id,
            'previous_values'=>$previous,'new_values'=>$new,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent(),
        ]);
    }
}
