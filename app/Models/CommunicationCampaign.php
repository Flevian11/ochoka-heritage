<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CommunicationCampaign extends Model {
 use HasFactory;
 public const DRAFT='draft', SCHEDULED='scheduled', PROCESSING='processing', SENT='sent', FAILED='failed', CANCELLED='cancelled';
 protected $fillable=['organization_id','communication_channel_id','notification_template_id','created_by','name','campaign_type','status','subject','body','audience_criteria','scheduled_at','sent_at'];
 protected $casts=['audience_criteria'=>'array','scheduled_at'=>'datetime','sent_at'=>'datetime'];
 public function organization(){return $this->belongsTo(Organization::class);}
 public function channel(){return $this->belongsTo(CommunicationChannel::class,'communication_channel_id');}
 public function template(){return $this->belongsTo(NotificationTemplate::class,'notification_template_id');}
 public function creator(){return $this->belongsTo(User::class,'created_by');}
 public function recipients(){return $this->hasMany(CommunicationRecipient::class);}
 protected static function booted(): void {
  static::creating(fn(self $campaign)=>$campaign->ensureOrganizationIntegrity());
  static::updating(fn(self $campaign)=>$campaign->ensureOrganizationIntegrity());
 }
 private function ensureOrganizationIntegrity(): void {
  $channel=CommunicationChannel::find($this->communication_channel_id);
  if($channel && (int)$channel->organization_id !== (int)$this->organization_id)
   throw new \InvalidArgumentException('Communication channel must belong to the same organization as the campaign.');
  if($this->notification_template_id){
   $template=NotificationTemplate::find($this->notification_template_id);
   if($template && (int)$template->organization_id !== (int)$this->organization_id)
    throw new \InvalidArgumentException('Notification template must belong to the same organization as the campaign.');
  }
 }
}