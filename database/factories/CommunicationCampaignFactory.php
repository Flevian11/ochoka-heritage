<?php
namespace Database\Factories;
use App\Models\CommunicationCampaign;
use App\Models\CommunicationChannel;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
class CommunicationCampaignFactory extends Factory {
 protected $model=CommunicationCampaign::class;
 public function definition():array{return ['organization_id'=>Organization::factory(),'communication_channel_id'=>null,'notification_template_id'=>null,'created_by'=>null,'name'=>fake()->words(3,true),'campaign_type'=>'announcement','status'=>CommunicationCampaign::DRAFT,'subject'=>'Announcement','body'=>'Hello {{member_name}}','audience_criteria'=>['member_status'=>'active'],'scheduled_at'=>null,'sent_at'=>null];}
 public function configure(){return $this->afterMaking(function(CommunicationCampaign $campaign){if($campaign->communication_channel_id===null){$channel=CommunicationChannel::create(['organization_id'=>$campaign->organization_id,'name'=>'Test Channel','code'=>'test-'.fake()->unique()->numberBetween(1000,9999),'channel_type'=>'email','provider'=>'mail','configuration'=>null,'is_active'=>true]);$campaign->communication_channel_id=$channel->id;}});}
}