<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityAttendeeType extends Model
{
    protected $table = 'activity_attendee_type';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
