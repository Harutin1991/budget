<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationType extends Model
{
    protected $table = 'allocation_type';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
