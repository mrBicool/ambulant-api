<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class KitchenOrder extends Model
{
    //
    use Eloquence, Mappable, Mutable;

    /**
     * Model Mapping
     */
    protected $maps = [
        'remarks'        => 'REMARKS', 
    ];

    protected $getterMutators = [
        'remarks'   => 'trim', 
    ];
}
