<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\MeetingActionItem;
use App\Models\MeetingAttendance;
use App\Models\MeetingDecision;
use App\Models\MeetingDocument;
use App\Models\MeetingParticipant;
use App\Models\MeetingType;
use App\Models\Member;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_meeting_supports_configurable_type_lifecycle_and_online_details(): void
    {
        $organization = Organization::factory()->create();
        $type = MeetingType::factory()->create(['organization_id' => $organization->id]);

        $meeting = Meeting::factory()->create([
            'organization_id' => $organization->id,
            'meeting_type_id' => $type->id,
            'format' => Meeting::FORMAT_HYBRID,
            'google_meet_url' => 'https://meet.google.com/example',
            'status' => Meeting::STATUS_SCHEDULED,
        ]);

        $this->assertSame($organization->id, $meeting->organization_id);
        $this->assertSame($type->id, $meeting->meeting_type_id);
        $this->assertSame(Meeting::FORMAT_HYBRID, $meeting->format);
        $this->assertSame(Meeting::STATUS_SCHEDULED, $meeting->status);
        $this->assertSame('https://meet.google.com/example', $meeting->google_meet_url);

        $meeting->update(['status' => Meeting::STATUS_MINUTES_PENDING]);
        $this->assertSame(Meeting::STATUS_MINUTES_PENDING, $meeting->refresh()->status);
    }

    public function test_meeting_tracks_participants_attendance_decisions_and_actions(): void
    {
        $organization = Organization::factory()->create();
        $type = MeetingType::factory()->create(['organization_id' => $organization->id]);
        $meeting = Meeting::factory()->create([
            'organization_id' => $organization->id,
            'meeting_type_id' => $type->id,
        ]);
        $member = Member::factory()->create(['organization_id' => $organization->id]);

        MeetingParticipant::factory()->create([
            'meeting_id' => $meeting->id,
            'member_id' => $member->id,
            'is_required' => true,
        ]);
        MeetingAttendance::factory()->create([
            'meeting_id' => $meeting->id,
            'member_id' => $member->id,
            'status' => 'present',
        ]);
        MeetingDecision::factory()->create([
            'meeting_id' => $meeting->id,
            'sequence' => 1,
            'description' => 'Approve the annual plan.',
        ]);
        MeetingActionItem::factory()->create([
            'meeting_id' => $meeting->id,
            'responsible_member_id' => $member->id,
        ]);

        $this->assertCount(1, $meeting->refresh()->participants);
        $this->assertCount(1, $meeting->attendances);
        $this->assertCount(1, $meeting->decisions);
        $this->assertCount(1, $meeting->actionItems);
    }

    public function test_minutes_and_other_documents_are_optional_and_support_pdf_or_docx(): void
    {
        $meeting = Meeting::factory()->create();

        $pdf = MeetingDocument::factory()->create([
            'meeting_id' => $meeting->id,
            'document_type' => MeetingDocument::TYPE_MINUTES,
            'original_name' => 'approved-minutes.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $docx = MeetingDocument::factory()->create([
            'meeting_id' => $meeting->id,
            'document_type' => MeetingDocument::TYPE_MINUTES,
            'original_name' => 'approved-minutes.docx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);

        $this->assertSame(MeetingDocument::TYPE_MINUTES, $pdf->refresh()->document_type);
        $this->assertSame('application/pdf', $pdf->mime_type);
        $this->assertSame(MeetingDocument::TYPE_MINUTES, $docx->refresh()->document_type);
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            $docx->mime_type
        );
        $this->assertCount(2, $meeting->refresh()->documents);
    }

    public function test_meeting_participant_and_attendance_records_are_unique_per_member(): void
    {
        $meeting = Meeting::factory()->create();
        $member = Member::factory()->create([
            'organization_id' => $meeting->organization_id,
        ]);

        MeetingParticipant::factory()->create([
            'meeting_id' => $meeting->id,
            'member_id' => $member->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        MeetingParticipant::factory()->create([
            'meeting_id' => $meeting->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_meeting_cannot_use_a_meeting_type_from_another_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $type = MeetingType::factory()->create(['organization_id' => $otherOrganization->id]);

        $this->expectException(\InvalidArgumentException::class);

        Meeting::create([
            'organization_id' => $organization->id,
            'meeting_type_id' => $type->id,
            'title' => 'Cross organization meeting',
            'starts_at' => now()->addDay(),
            'format' => Meeting::FORMAT_PHYSICAL,
            'status' => Meeting::STATUS_SCHEDULED,
        ]);
    }
}
