<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class ScheduleRecurrance extends Model
{
    protected $table = 'schedule_recurrance';
    public $timestamps = false;
    
    public static $recurranceTypes = [
            'Day' => 1,
            'Week' =>2,
            'Month' =>3
        ];
    
    public static $dayOfWeek = [
            0 => 'S',
            1 => 'M',
            2 => 'Th',
            3 => 'W',
            4 => 'T',
            5 => 'F',
            6 => 'S',
        ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'sea_id','lea_id','ses_id','schedule_id', 'recurrance_type_id', 'separation_count', 'number_of_occurrences', 'day_of_week', 'week_of_month',
        'day_of_month', 'month_of_year','exclude_day'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function scheduleRecurranceType()
    {
        return $this->belongsTo('App\ScheduleRecurranceType','recurrance_type_id');
    }
    
    public function getReapeatOnDays()
    {
        if(!$this->day_of_week) return [];
        
        $daysOfWeek = ['reapeatOn'=>[],'reapeatDays'=>''];
        $days = explode(',',$this->day_of_week);
        $count = count($days);
        $daysOfWeek['reapeatOn'] = $days;
        foreach($days as $key=>$day) {
            if($key < $count - 1) {
                $daysOfWeek['reapeatDays'] .= self::$dayOfWeek[$day]. ', ';
            } else {
                $daysOfWeek['reapeatDays'] .= self::$dayOfWeek[$day] . '';
            }
        }
        
        return $daysOfWeek;
    }
}

