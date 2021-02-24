<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $table = 'campus';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description', 'school_id','description','district_id'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    public function address()
    {
        return $this->hasMany('App\CampusAddress');
    }

}
