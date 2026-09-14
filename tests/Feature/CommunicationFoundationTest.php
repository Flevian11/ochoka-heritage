<?php
namespace Tests\Feature;
use App\Models\CommunicationCampaign;
use App\Models\CommunicationChannel;
use App\Models\CommunicationRecipient;
use App\Models\Member;
use App\Models\NotificationSchedule;
use App\Models\NotificationTemplate;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class CommunicationFoundationTest extends TestCase {
 use RefreshDatabase;

 public function test_channel_supports_configurable_provider_and_channel_type():void{
  $org=Organization::factory()->create();
  $channel=CommunicationChannel::factory()->create(['organization_id'=>$org->id,'channel_type'=>'sms','provider'=>'jbs_sms','configuration'=>['sender_id'=>'OCHOKA']]);
  $this->assertSame($org->id,$channel->organization_id);
  $this->assertSame('jbs_sms',$channel->provider);
  $this->assertSame('OCHOKA',$channel->configuration['sender_id']);
 }

 public function test_template_supports_variables_and_belongs_to_channel_and_organization():void{
  $org=Organization::factory()->create();
  $channel=CommunicationChannel::factory()->create(['organization_id'=>$org->id]);
  $template=NotificationTemplate::factory()->create(['communication_channel_id'=>$channel->id]);
  $this->assertSame($org->id,$template->organization_id);
  $this->assertContains('member_name',$template->variables);
 }

 public function test_campaign_can_be_draft_or_scheduled_and_tracks_audience_and_timestamps():void{
  $org=Organization::factory()->create();
  $campaign=CommunicationCampaign::factory()->create(['organization_id'=>$org->id,'status'=>CommunicationCampaign::SCHEDULED,'scheduled_at'=>now()->addDay(),'audience_criteria'=>['member_status'=>'active']]);
  $this->assertSame($org->id,$campaign->organization_id);
  $this->assertSame('active',$campaign->audience_criteria['member_status']);
  $this->assertNotNull($campaign->scheduled_at);
 }

 public function test_recipient_records_delivery_lifecycle_separately_from_campaign():void{
  $org=Organization::factory()->create();
  $campaign=CommunicationCampaign::factory()->create(['organization_id'=>$org->id]);
  $member=Member::factory()->create(['organization_id'=>$org->id]);
  $recipient=CommunicationRecipient::factory()->create(['communication_campaign_id'=>$campaign->id,'member_id'=>$member->id,'status'=>'delivered','sent_at'=>now()->subMinute(),'delivered_at'=>now()]);
  $this->assertSame('delivered',$recipient->status);
  $this->assertSame($member->id,$recipient->member_id);
 }

 public function test_notification_schedule_supports_recurrence_audience_and_enablement():void{
  $org=Organization::factory()->create();
  $schedule=NotificationSchedule::factory()->create(['organization_id'=>$org->id,'frequency'=>'monthly','audience_criteria'=>['member_status'=>'active','months_overdue'=>2]]);
  $this->assertTrue($schedule->is_enabled);
  $this->assertSame(2,$schedule->audience_criteria['months_overdue']);
  $this->assertSame($org->id,$schedule->template->organization_id);
 }

 public function test_communication_records_reject_cross_organization_references():void{
  $orgA=Organization::factory()->create(); $orgB=Organization::factory()->create();
  $channelB=CommunicationChannel::factory()->create(['organization_id'=>$orgB->id]);
  $campaign=CommunicationCampaign::factory()->create(['organization_id'=>$orgA->id]);
  $this->expectException(\InvalidArgumentException::class);
  $campaign->communication_channel_id=$channelB->id; $campaign->save();
 }

 public function test_recipient_rejects_member_from_another_organization():void{
  $orgA=Organization::factory()->create(); $orgB=Organization::factory()->create();
  $campaign=CommunicationCampaign::factory()->create(['organization_id'=>$orgA->id]);
  $memberB=Member::factory()->create(['organization_id'=>$orgB->id]);
  $this->expectException(\InvalidArgumentException::class);
  CommunicationRecipient::factory()->create(['communication_campaign_id'=>$campaign->id,'member_id'=>$memberB->id]);
 }
}