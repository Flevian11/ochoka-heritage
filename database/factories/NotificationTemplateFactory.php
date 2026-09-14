<?php
namespace Database\Factories;
use App\Models\CommunicationChannel;
use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
class NotificationTemplateFactory extends Factory {
 protected $model=NotificationTemplate::class;
 public function definition():array{return ['organization_id'=>null,'communication_channel_id'=>CommunicationChannel::factory(),'name'=>fake()->words(3,true),'key'=>fake()->unique()->slug(3),'subject'=>'Notification','body'=>'Hello {{member_name}}','variables'=>['member_name'],'is_active'=>true];}
 public function configure(){return $this->afterMaking(function(NotificationTemplate $template){if($template->organization_id===null){$channel=CommunicationChannel::find($template->communication_channel_id);$template->organization_id=$channel->organization_id;}});}
}