<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class Group extends Model
{
    //
    use Eloquence, Mappable, Mutable;

    protected $table        = 'groups';
    protected $primaryKey   = 'GROUPCODE';
    public $timestamps      = false;

    /**
     * Model Mapping
     */
    protected $maps = [
        'group_id'      => 'GROUPCODE',
        'description'   => 'DESCRIPTION'
    ];
    protected $getterMutators = [
        'description' => 'trim'
    ];
}
