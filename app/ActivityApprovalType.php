<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityApprovalType extends Model
{
    protected $table = 'activity_approval_type';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
