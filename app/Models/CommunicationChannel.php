<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CommunicationChannel extends Model {
 use HasFactory;
 protected $fillable=['organization_id','name','code','channel_type','provider','configuration','is_active'];
 protected $casts=['configuration'=>'array','is_active'=>'boolean'];
 public function organization(){return $this->belongsTo(Organization::class);}
 public function templates(){return $this->hasMany(NotificationTemplate::class);}
 public function campaigns(){return $this->hasMany(CommunicationCampaign::class);}
 public function schedules(){return $this->hasMany(NotificationSchedule::class);}
}