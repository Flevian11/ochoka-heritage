<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Organization;
use App\Models\WelfareCase;
use App\Models\WelfareContribution;
use App\Models\WelfareSupportObligation;
use App\Services\WelfareContributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelfareFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_welfare_case_supports_approval_workflow_and_beneficiary(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->for($organization)->create();

        $case = WelfareCase::factory()->create([
            'organization_id' => $organization->id,
            'beneficiary_member_id' => $member->id,
            'status' => WelfareCase::STATUS_PROPOSED,
            'proposed_support_amount' => 1000,
        ]);

        $case->update([
            'status' => WelfareCase::STATUS_APPROVED,
            'approved_support_amount' => 1000,
        ]);

        $this->assertSame($member->id, $case->fresh()->beneficiary_member_id);
        $this->assertSame(WelfareCase::STATUS_APPROVED, $case->fresh()->status);
        $this->assertSame('1000.00', $case->fresh()->approved_support_amount);
    }

    public function test_welfare_case_tracks_each_member_contribution_and_payment_source(): void
    {
        $organization = Organization::factory()->create();
        $case = WelfareCase::factory()->create(['organization_id' => $organization->id]);
        $memberA = Member::factory()->for($organization)->create();
        $memberB = Member::factory()->for($organization)->create();

        WelfareContribution::factory()->create([
            'organization_id' => $organization->id,
            'welfare_case_id' => $case->id,
            'member_id' => $memberA->id,
            'amount' => 500,
            'source_type' => WelfareContribution::SOURCE_INDEPENDENT_PAYMENT,
            'status' => WelfareContribution::STATUS_VERIFIED,
        ]);
        WelfareContribution::factory()->create([
            'organization_id' => $organization->id,
            'welfare_case_id' => $case->id,
            'member_id' => $memberB->id,
            'amount' => 1000,
            'source_type' => WelfareContribution::SOURCE_MEMBER_CONTRIBUTION_BALANCE,
            'status' => WelfareContribution::STATUS_VERIFIED,
        ]);

        $this->assertCount(2, $case->fresh()->contributions);
        $this->assertSame(1500.0, (float) $case->fresh()->contributions()->sum('amount'));
        $this->assertSame(1, $case->fresh()->contributions()->where('source_type', WelfareContribution::SOURCE_INDEPENDENT_PAYMENT)->count());
        $this->assertSame(1, $case->fresh()->contributions()->where('source_type', WelfareContribution::SOURCE_MEMBER_CONTRIBUTION_BALANCE)->count());
    }

    public function test_member_can_see_the_welfares_they_contributed_to_and_those_they_did_not(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->for($organization)->create();
        $contributedCase = WelfareCase::factory()->create(['organization_id' => $organization->id, 'title' => 'Madam Ujiu Bereavement']);
        $missedCase = WelfareCase::factory()->create(['organization_id' => $organization->id, 'title' => 'Medical Support']);

        WelfareContribution::factory()->create([
            'organization_id' => $organization->id,
            'welfare_case_id' => $contributedCase->id,
            'member_id' => $member->id,
            'amount' => 200,
            'status' => WelfareContribution::STATUS_VERIFIED,
        ]);

        $this->assertSame(1, WelfareContribution::query()->where('member_id', $member->id)->count());
        $this->assertTrue(WelfareContribution::query()->where('member_id', $member->id)->where('welfare_case_id', $contributedCase->id)->exists());
        $this->assertFalse(WelfareContribution::query()->where('member_id', $member->id)->where('welfare_case_id', $missedCase->id)->exists());
    }

    public function test_support_obligation_reports_full_partial_and_outstanding_amount(): void
    {
        $organization = Organization::factory()->create();
        $case = WelfareCase::factory()->create(['organization_id' => $organization->id]);
        $member = Member::factory()->for($organization)->create();

        $obligation = WelfareSupportObligation::factory()->create([
            'welfare_case_id' => $case->id,
            'member_id' => $member->id,
            'amount' => 1000,
            'paid_amount' => 400,
            'status' => WelfareSupportObligation::STATUS_PARTIAL,
        ]);

        $this->assertSame(600.0, $obligation->outstandingAmount());
        $this->assertSame(WelfareSupportObligation::STATUS_PARTIAL, $obligation->status);
    }

    public function test_welfare_contribution_requires_same_organization_as_case_and_member(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $case = WelfareCase::factory()->create(['organization_id' => $organization->id]);
        $member = Member::factory()->for($otherOrganization)->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The member must belong to the same organization as the welfare case.');

        app(WelfareContributionService::class)->create([
            'organization_id' => $organization->id,
            'welfare_case_id' => $case->id,
            'member_id' => $member->id,
            'amount' => 200,
            'source_type' => WelfareContribution::SOURCE_INDEPENDENT_PAYMENT,
            'payment_method' => 'cash',
            'reference' => 'WEL-ORG-MISMATCH',
            'paid_on' => now()->toDateString(),
            'status' => WelfareContribution::STATUS_PENDING,
        ]);
    }
}
