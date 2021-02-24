<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationTypeCategories extends Model
{
    protected $table = 'allocation_type_categories';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
