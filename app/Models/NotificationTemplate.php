<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class NotificationTemplate extends Model {
 use HasFactory;
 protected $fillable=['organization_id','communication_channel_id','name','key','subject','body','variables','is_active'];
 protected $casts=['variables'=>'array','is_active'=>'boolean'];
 public function organization(){return $this->belongsTo(Organization::class);}
 public function channel(){return $this->belongsTo(CommunicationChannel::class,'communication_channel_id');}
 public function campaigns(){return $this->hasMany(CommunicationCampaign::class);}
 public function schedules(){return $this->hasMany(NotificationSchedule::class);}
}