<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class NotificationSchedule extends Model {
 use HasFactory;
 protected $fillable=['organization_id','communication_channel_id','notification_template_id','name','frequency','cron_expression','audience_criteria','starts_at','ends_at','last_run_at','next_run_at','is_enabled'];
 protected $casts=['audience_criteria'=>'array','starts_at'=>'datetime','ends_at'=>'datetime','last_run_at'=>'datetime','next_run_at'=>'datetime','is_enabled'=>'boolean'];
 public function organization(){return $this->belongsTo(Organization::class);}
 public function channel(){return $this->belongsTo(CommunicationChannel::class,'communication_channel_id');}
 public function template(){return $this->belongsTo(NotificationTemplate::class,'notification_template_id');}
 protected static function booted(): void {
  static::creating(fn(self $schedule)=>$schedule->ensureOrganizationIntegrity());
  static::updating(fn(self $schedule)=>$schedule->ensureOrganizationIntegrity());
 }
 private function ensureOrganizationIntegrity(): void {
  $channel=CommunicationChannel::find($this->communication_channel_id);
  $template=NotificationTemplate::find($this->notification_template_id);
  if($channel && (int)$channel->organization_id !== (int)$this->organization_id)
   throw new \InvalidArgumentException('Communication channel must belong to the same organization as the schedule.');
  if($template && (int)$template->organization_id !== (int)$this->organization_id)
   throw new \InvalidArgumentException('Notification template must belong to the same organization as the schedule.');
 }
}