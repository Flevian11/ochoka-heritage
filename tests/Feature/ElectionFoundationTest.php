<?php

namespace Tests\Feature;

use App\Models\Election;
use App\Models\ElectionBallot;
use App\Models\ElectionNomination;
use App\Models\ElectionNominationSeconder;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use App\Models\ElectionVoterEligibility;
use App\Models\ExecutivePosition;
use App\Models\Member;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use LogicException;
use Tests\TestCase;

class ElectionFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_election_supports_configurable_lifecycle_and_eligibility_criteria(): void
    {
        $org = Organization::factory()->create();

        $election = Election::factory()->create([
            'organization_id' => $org->id,
            'status' => Election::NOMINATIONS_OPEN,
            'eligibility_criteria' => [
                'membership_status' => 'active',
                'minimum_membership_months' => 6,
            ],
        ]);

        $this->assertSame($org->id, $election->organization_id);
        $this->assertSame('active', $election->eligibility_criteria['membership_status']);
        $this->assertSame(6, $election->eligibility_criteria['minimum_membership_months']);
    }

    public function test_election_position_is_bound_to_an_executive_position(): void
    {
        $org = Organization::factory()->create();
        $position = ExecutivePosition::factory()->create(['organization_id' => $org->id]);
        $election = Election::factory()->create(['organization_id' => $org->id]);

        $electionPosition = ElectionPosition::factory()->create([
            'election_id' => $election->id,
            'executive_position_id' => $position->id,
            'seats' => 1,
            'max_votes_per_voter' => 1,
        ]);

        $this->assertSame($position->id, $electionPosition->executive_position_id);
        $this->assertSame($election->id, $electionPosition->election_id);
    }

    public function test_nomination_can_track_candidate_nominator_seconders_and_verification_snapshot(): void
    {
        $org = Organization::factory()->create();
        $election = Election::factory()->create(['organization_id' => $org->id]);
        $position = ExecutivePosition::factory()->create(['organization_id' => $org->id]);
        $electionPosition = ElectionPosition::factory()->create([
            'election_id' => $election->id,
            'executive_position_id' => $position->id,
        ]);
        $candidate = Member::factory()->create(['organization_id' => $org->id]);
        $nominator = Member::factory()->create(['organization_id' => $org->id]);
        $seconder = Member::factory()->create(['organization_id' => $org->id]);

        $nomination = ElectionNomination::factory()->create([
            'election_position_id' => $electionPosition->id,
            'candidate_member_id' => $candidate->id,
            'nominated_by' => $nominator->id,
            'status' => ElectionNomination::VERIFIED,
            'eligibility_snapshot' => ['active' => true, 'months' => 12],
        ]);

        ElectionNominationSeconder::factory()->create([
            'nomination_id' => $nomination->id,
            'member_id' => $seconder->id,
        ]);

        $this->assertCount(1, $nomination->seconders);
        $this->assertSame($candidate->id, $nomination->candidate_member_id);
        $this->assertTrue($nomination->eligibility_snapshot['active']);
    }

    public function test_voter_eligibility_is_snapshotted_per_election(): void
    {
        $org = Organization::factory()->create();
        $election = Election::factory()->create(['organization_id' => $org->id]);
        $member = Member::factory()->create(['organization_id' => $org->id]);

        $snapshot = ElectionVoterEligibility::factory()->create([
            'election_id' => $election->id,
            'member_id' => $member->id,
            'eligible' => true,
            'criteria_snapshot' => ['status' => 'active', 'as_of' => '2026-09-14'],
        ]);

        $this->assertTrue($snapshot->eligible);
        $this->assertSame('active', $snapshot->criteria_snapshot['status']);
        $this->assertSame($member->id, $snapshot->member_id);
    }

    public function test_one_voter_can_have_only_one_ballot_per_election(): void
    {
        $org = Organization::factory()->create();
        $election = Election::factory()->create(['organization_id' => $org->id]);
        $member = Member::factory()->create(['organization_id' => $org->id]);

        ElectionBallot::factory()->create([
            'election_id' => $election->id,
            'voter_member_id' => $member->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        ElectionBallot::factory()->create([
            'election_id' => $election->id,
            'voter_member_id' => $member->id,
        ]);
    }

    public function test_secret_ballot_has_no_voter_reference_on_the_vote_record_and_ballot_is_sealed_immutably(): void
    {
        $org = Organization::factory()->create();
        $election = Election::factory()->create([
            'organization_id' => $org->id,
            'status' => Election::VOTING_OPEN,
        ]);
        $position = ExecutivePosition::factory()->create(['organization_id' => $org->id]);
        $electionPosition = ElectionPosition::factory()->create([
            'election_id' => $election->id,
            'executive_position_id' => $position->id,
        ]);
        $candidate = Member::factory()->create(['organization_id' => $org->id]);
        $voter = Member::factory()->create(['organization_id' => $org->id]);

        $ballot = ElectionBallot::factory()->create([
            'election_id' => $election->id,
            'voter_member_id' => $voter->id,
            'ballot_token' => (string) Str::uuid(),
            'sealed_at' => now(),
        ]);

        $vote = ElectionVote::factory()->create([
            'ballot_id' => $ballot->id,
            'election_position_id' => $electionPosition->id,
            'candidate_member_id' => $candidate->id,
        ]);

        $this->assertSame($ballot->id, $vote->ballot_id);
        $this->assertArrayNotHasKey('voter_member_id', $vote->getAttributes());

        $this->expectException(LogicException::class);
        $ballot->status = 'tampered';
        $ballot->save();
    }

    public function test_election_records_are_scoped_by_organization_at_data_model_level(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        $election = Election::factory()->create(['organization_id' => $orgA->id]);
        $positionB = ExecutivePosition::factory()->create(['organization_id' => $orgB->id]);

        $this->expectException(\InvalidArgumentException::class);

        ElectionPosition::factory()->create([
            'election_id' => $election->id,
            'executive_position_id' => $positionB->id,
        ]);
    }
}
