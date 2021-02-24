<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityAttendee extends Model
{
    protected $table = 'activity_attendee';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'sea_id','lea_id','ses_id', 'activity_id', 'activity_school_attendee_list_id', 'activity_attendee_type_id',
        'activity_attendee_summary_id'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    public function summary()
    {
        return $this->belongsTo('App\ActivityAttendeeSummary', 'activity_attendee_summary_id','id');
    }

    public function attendeeType()
    {
        return $this->hasOne('App\ActivityAttendeeType', 'activity_attendee_type_id');
    }

    public function attendeeSchool()
    {
        return $this->belongsTo('App\ActivitySchoolAttendeeList', 'activity_school_attendee_list_id','id');
    }
}
