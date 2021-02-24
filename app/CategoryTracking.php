<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryTracking extends Model
{
    protected $table = 'category_tracking';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
