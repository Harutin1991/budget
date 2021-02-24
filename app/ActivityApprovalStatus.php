<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityApprovalStatus extends Model
{
    protected $table = 'activity_approval_status';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
