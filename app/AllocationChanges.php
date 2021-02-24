<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class AllocationChanges extends Model
{
    protected $table = 'allocation_changes';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }


}
