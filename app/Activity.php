<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';

    public static $allocationTypes = [
        'title1' => 1,
        'title2' => 2,
        'title3' => 3,
        'title4' => 4,
        'esser' => 5,
        'geer' => 6,
    ];

    //public static $status = ['fn' => 1, 'pr' => 0];
    //public static $allocationTypesRegular = [5, 6];
    
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'sea_id','lea_id','ses_id','school_id', 'campus_id', 'supplier_id', 'activity_status_id', 'activity_approval_status_id', 'activity_approval_type_id',
        'approver', 'activity_name', 'cost', 'total_cost','allocation_id', 'allocation_type_categories_id', 'start_date', 'end_date',
        'has_recurring', 'has_multi_schedule', 'activity_note', 'has_lock','total_attendee_count', 'activity_attendee_type_id','upcharge_percentage','upcharge_fee','is_online'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo('App\School', 'school_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activitySchedule()
    {
        return $this->hasMany('App\ActivitySchedule');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->belongsTo('App\ActivityStatus','activity_status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function supplier()
    {
        return $this->belongsTo('App\Supplier', 'supplier_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attendeeSummary()
    {
        return $this->hasMany('App\ActivityAttendeeSummary');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->belongsTo('App\ActivityType', 'allocation_type_categories_id');
    }

     /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function allocation()
    {
        return $this->belongsTo('App\AllocationType', 'allocation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function approvalStatus()
    {
        return $this->belongsTo('App\ActivityApprovalStatus', 'activity_approval_status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function approvalTypes()
    {
        return $this->belongsTo('App\ActivityApprovalType', 'activity_approval_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attendeeType()
    {
        return $this->belongsTo('App\ActivityAttendeeType', 'activity_attendee_type_id');
    }
    
    public function getAttendySummary()
    {
        $activity = self::find($this->id);
        $activityAttendies = $activity->attendeeSummary;
        
        $attendySuumary = ['count'=>0];
        foreach($activityAttendies as $attendee) {
            $attendySuumary['count'] +=  $attendee->count;
        }
        
        return $attendySuumary;
    }
}
