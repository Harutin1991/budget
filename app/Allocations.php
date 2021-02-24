<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allocations extends Model
{
    protected $table = 'allocation';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
