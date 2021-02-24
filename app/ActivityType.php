<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    protected $table = 'allocation_type_categories';

    public static $types = [
        1 => 'PD',
        2 => 'FE',
        3 => 'PD',
        4 => 'M',
        5 => 'PD',
        6 => 'I',
        7 => 'WR',
        8 => 'SM',
        9 => 'TPD',
        10 => 'TI',
        11 => 'S',
        12 => 'M',
        13 => 'PD'
    ];

    public static $typesByAllocations = [
        1 => [
            'professional_development' => 'PD',
            'family_engagement' => 'FE'
        ],
        2 => [
            'professional_development' => 'PD',
            'materials' => 'M'
        ],
        3 => [
            'professional_development' => 'PD',
            'total_instruction' => 'I'
        ],
        4 => [
            'well_rounded_amount' => 'WR',
            'safe_healthy_amount' => 'SM',
            'teach_professional_development_amount' => 'TPD',
            'teach_instruction_amount' => 'TI'
        ],
        5 => [
            'professional_development' => 'PD',
            'materials' => 'M'
        ]
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
