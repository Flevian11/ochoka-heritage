<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CommunicationRecipient extends Model {
 use HasFactory;
 protected $fillable=['communication_campaign_id','member_id','destination','recipient_name','status','provider_message_id','queued_at','sent_at','delivered_at','failed_at','failure_reason'];
 protected $casts=['queued_at'=>'datetime','sent_at'=>'datetime','delivered_at'=>'datetime','failed_at'=>'datetime'];
 public function campaign(){return $this->belongsTo(CommunicationCampaign::class);}
 public function member(){return $this->belongsTo(Member::class);}
 protected static function booted(): void {
  static::creating(fn(self $recipient)=>$recipient->ensureOrganizationIntegrity());
  static::updating(fn(self $recipient)=>$recipient->ensureOrganizationIntegrity());
 }
 private function ensureOrganizationIntegrity(): void {
  if(!$this->member_id)return;
  $campaign=CommunicationCampaign::find($this->communication_campaign_id);
  $member=Member::find($this->member_id);
  if($campaign && $member && (int)$campaign->organization_id !== (int)$member->organization_id)
   throw new \InvalidArgumentException('Recipient member must belong to the same organization as the campaign.');
 }
}