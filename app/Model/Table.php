<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class Table extends Model
{
    //
    use Eloquence, Mappable, Mutable;

    protected $table        = 'tablefile'; 
    public $timestamps      = false;

    /**
     * Model Mapping
     */
    protected $maps = [  
        // 'branch_id'             => 'branch_id',
        'id'                    => 'table_id',
        'number'                => 'TABLENO',
        'code'                  => 'TABLECODE',
        'description'           => 'DESCRIPTION',
        'guests'                => 'GUESTS',
        'status'                => 'STATUS',
        'status2'               => 'STATUS2'

    ];
  
    protected $getterMutators = [
        'description'           => 'trim',
        'code'                  => 'trim',
    ];

}
