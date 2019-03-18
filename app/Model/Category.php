<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class Category extends Model
{
    //
    use Eloquence, Mappable, Mutable;

    protected $table        = 'Category';
    protected $primaryKey   = 'CATEGORYCODE';
    public $timestamps      = false;

    /**
     * Model Mapping
     */
    protected $maps = [
        'group_id'      => 'GROUPCODE',
        'category_id'   => 'CATEGORYCODE',
        'description'   => 'DESC'
    ];
    protected $getterMutators = [
        'description' => 'trim'
    ];

    public static function getByGroupId($id){
        return static::where( 'group_id', $id )
            ->get();
    }
}
