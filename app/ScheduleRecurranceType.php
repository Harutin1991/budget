<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class ScheduleRecurranceType extends Model
{
    protected $table = 'schedule_recurrance_type';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'recurrance_type'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    /**
     * Get the school for the allocation type.
     */
    public function inventoryCategory()
    {
        return $this->belongsTo('App\InventoryCategoryType','inventory_category_type_id');
    }
}

