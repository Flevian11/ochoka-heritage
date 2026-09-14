<?php
namespace Database\Factories;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
class SystemSettingFactory extends Factory {
 public function definition(): array { return ['organization_id'=>Organization::factory(),'key'=>fake()->unique()->slug(2),'value'=>fake()->sentence(),'type'=>'string','group'=>'general','description'=>fake()->optional()->sentence(),'is_public'=>false,'effective_from'=>now()->toDateString(),'effective_until'=>null]; }
}
