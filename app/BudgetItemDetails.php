<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetItemDetails extends Model
{
    protected $table = 'budget_item_detail';
    public $timestamps = false;
    
    protected $fillable = [
        'sea_id', 'lea_id', 'ses_id', 'item_id', 'has_lock', 'is_online', 'has_recurring', 'has_multi_schedule', 'is_locked', 'is_billed', 
        'is_complete', 'is_in_inventory', 'total_attendee_count', 'renewal_date', 'version', 'has_license_key', 'expiration_date', 'software_category_id'

    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
