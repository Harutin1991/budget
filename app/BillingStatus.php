<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingStatus extends Model
{
    protected $table = 'billing_status';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
