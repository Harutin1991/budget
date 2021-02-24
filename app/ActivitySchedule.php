<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivitySchedule extends Model
{
    protected $table = 'activity_schedule';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'schedule_id','activity_id','note','location'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    public function schedule()
    {
        return $this->belongsTo('App\Schedule', 'schedule_id','id');
    }

     /**
     * Get the recurred activity
     */
    public function recurredSchedules()
    {
        return $this->belongsTo('App\Schedule', 'schedule_id','id')->withDefault([
            'has_recurring' => 1,
        ]);
    }

    public function activity()
    {
        return $this->belongsTo('App\Budget', 'activity_id');
    }

}
