<?php
namespace Database\Factories;
use App\Models\CommunicationCampaign;
use App\Models\CommunicationRecipient;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
class CommunicationRecipientFactory extends Factory {
 protected $model=CommunicationRecipient::class;
 public function definition():array{return ['communication_campaign_id'=>CommunicationCampaign::factory(),'member_id'=>null,'destination'=>fake()->safeEmail(),'recipient_name'=>fake()->name(),'status'=>'pending','provider_message_id'=>null,'queued_at'=>null,'sent_at'=>null,'delivered_at'=>null,'failed_at'=>null,'failure_reason'=>null];}
 public function configure(){return $this->afterMaking(function(CommunicationRecipient $r){if($r->member_id===null){$campaign=CommunicationCampaign::find($r->communication_campaign_id);$r->member_id=Member::factory()->create(['organization_id'=>$campaign->organization_id])->id;}});}
}