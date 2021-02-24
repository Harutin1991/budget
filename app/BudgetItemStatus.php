<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetItemStatus extends Model
{
    protected $table = 'budget_item_status';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
