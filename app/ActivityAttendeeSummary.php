<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityAttendeeSummary extends Model
{
    protected $table = 'activity_attendee_summary';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'sea_id','lea_id','ses_id', 'activity_id', 'count', 'activity_attendee_type_id',
        'is_all'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }
    
    public function attendyRelation()
    {
        return $this->hasMany('App\ActivityAttendee');
    }

    public function type()
    {
        return $this->belongsTo('App\ActivityAttendeeType', 'activity_attendee_type_id','id');
    }
}
