<?php
namespace Database\Factories;
use App\Models\CommunicationChannel;
use App\Models\NotificationSchedule;
use App\Models\NotificationTemplate;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
class NotificationScheduleFactory extends Factory {
 protected $model=NotificationSchedule::class;
 public function definition():array{return ['organization_id'=>Organization::factory(),'communication_channel_id'=>null,'notification_template_id'=>null,'name'=>fake()->words(3,true),'frequency'=>'monthly','cron_expression'=>null,'audience_criteria'=>['member_status'=>'active'],'starts_at'=>now(),'ends_at'=>null,'last_run_at'=>null,'next_run_at'=>now()->addMonth(),'is_enabled'=>true];}
 public function configure(){return $this->afterMaking(function(NotificationSchedule $s){if($s->communication_channel_id===null){$c=CommunicationChannel::create(['organization_id'=>$s->organization_id,'name'=>'Schedule Channel','code'=>'schedule-'.fake()->unique()->numberBetween(1000,9999),'channel_type'=>'sms','provider'=>'jbs_sms','configuration'=>null,'is_active'=>true]);$s->communication_channel_id=$c->id;} if($s->notification_template_id===null){$t=NotificationTemplate::create(['organization_id'=>$s->organization_id,'communication_channel_id'=>$s->communication_channel_id,'name'=>'Schedule Template','key'=>'schedule-'.fake()->unique()->numberBetween(1000,9999),'subject'=>null,'body'=>'Scheduled notification','variables'=>null,'is_active'=>true]);$s->notification_template_id=$t->id;}});}
}