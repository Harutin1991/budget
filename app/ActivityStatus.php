<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityStatus extends Model
{
    protected $table = 'activity_status';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
