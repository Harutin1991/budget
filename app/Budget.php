<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budget_item';

    public static $allocationTypes = [
        'title1' => 1,
        'title2' => 2,
        'title3' => 3,
        'title4' => 4,
        'esser' => 5,
        'geer' => 6,
    ];

    //public static $status = ['fn' => 1, 'pr' => 0];
    //public static $allocationTypesRegular = [5, 6];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'sea_id', 'lea_id', 'ses_id', 'id', 'school_id', 'school_year_id', 'campus_id', 'supplier_id', 'allocation_id',
        'allocation_type_id', 'inventory_category_type_id', 'category_id', 'subcategory_id', 'approval_status_id', 'approval_type_id','category_tracking_id','billing_status_id',
        'attendee_type_id', 'status_id', 'name', 'description', 'budget_unti_id', 'quantity', 'unit_cost', 'unit_total_cost', 'unti_cost_markup',
        'unit_total_cost_markup', 'markup_percentage', 'markup_fee', 'approver', 'note', 'item_type_id', 'purchase_date', 'start_date', 'end_date', 'completed_date',
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function details()
    {
        return $this->belongsTo('App\BudgetItemDetails', 'id','item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo('App\BudgetUnit', 'budget_unti_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory()
    {
        return $this->belongsTo('App\Category', 'subcategory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo('App\School', 'school_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campus()
    {
        return $this->belongsTo('App\Campus', 'campus_id');
    }
    
     /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function traking()
    {
        return $this->belongsTo('App\CategoryTracking', 'category_tracking_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function billing()
    {
        return $this->belongsTo('App\BillingStatus', 'billing_status_id');
    }
    
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activitySchedule()
    {
        return $this->hasMany('App\ActivitySchedule');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->belongsTo('App\BudgetItemStatus', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function supplier()
    {
        return $this->belongsTo('App\Supplier', 'supplier_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attendeeSummary()
    {
        return $this->hasMany('App\ActivityAttendeeSummary','activity_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->belongsTo('App\BudgetItemType', 'item_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function allocation()
    {
        return $this->belongsTo('App\AllocationType', 'allocation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function approvalStatus()
    {
        return $this->belongsTo('App\ApprovalStatus', 'approval_status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function approvalTypes()
    {
        return $this->belongsTo('App\ApprovalType', 'approval_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attendeeType()
    {
        return $this->belongsTo('App\ActivityAttendeeType', 'attendee_type_id');
    }

    public function getAttendySummary()
    {
        $activity = self::find($this->id);
        $activityAttendies = $activity->attendeeSummary;

        $attendySuumary = ['count' => 0];
        foreach ($activityAttendies as $attendee) {
            $attendySuumary['count'] += $attendee->count;
        }

        return $attendySuumary;
    }
}
