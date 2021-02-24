<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetItemType extends Model
{
    protected $table = 'budget_item_type';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
