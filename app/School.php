<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = 'school';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    public function allocation()
    {
        return $this->hasMany('App\Allocations','school_id');
    }
}
