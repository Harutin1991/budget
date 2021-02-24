<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetUnit extends Model
{
    protected $table = 'budget_unit';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
