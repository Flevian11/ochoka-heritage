<?php
namespace Database\Factories;
use App\Models\CommunicationChannel;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
class CommunicationChannelFactory extends Factory {
 protected $model=CommunicationChannel::class;
 public function definition():array{return ['organization_id'=>Organization::factory(),'name'=>fake()->words(2,true),'code'=>fake()->unique()->slug(2),'channel_type'=>fake()->randomElement(['email','sms','whatsapp','in_app']),'provider'=>fake()->randomElement(['mail','jbs_sms','whatsapp','database']),'configuration'=>['sender'=>'configured'],'is_active'=>true];}
}