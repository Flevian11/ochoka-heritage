<?php
namespace Database\Factories;
use App\Models\Member;
use App\Models\Organization;
use App\Models\ExecutivePosition;
use Illuminate\Database\Eloquent\Factories\Factory;
class ExecutiveAppointmentFactory extends Factory {
 public function definition(): array {
  $organization=Organization::factory();
  return ['organization_id'=>$organization,'member_id'=>Member::factory()->for($organization),'executive_position_id'=>ExecutivePosition::factory()->for($organization),'starts_at'=>fake()->dateTimeBetween('-2 years','now')->format('Y-m-d'),'ends_at'=>null,'status'=>'active','appointed_by'=>null,'appointed_at'=>now(),'revoked_at'=>null,'revoked_by'=>null,'reason'=>null];
 }
}