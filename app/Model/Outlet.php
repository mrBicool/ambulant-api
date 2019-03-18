<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class Outlet extends Model
{
    use Eloquence, Mappable, Mutable;
    //
    protected $table        = 'Outlets';
    protected $primaryKey   = 'OUTLETID';
    public $timestamps      = false;

    /**
     * Model Mapping
     */
    protected $maps = [
        'outlet_id'     => 'OUTLETID',
        'code'          => 'OUTLETCODE',
        'description'   => 'DESCRIPTION',
        'zone_id'       => 'ZONEID'
    ];

    protected $getterMutators = [
        'code'          => 'trim',
        'description'   => 'trim'
    ];
}
