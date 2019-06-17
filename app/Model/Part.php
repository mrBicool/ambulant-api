<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class Part extends Model
{
    //
    use Eloquence, Mappable, Mutable;
    //
    protected $table 		= 'parts';
    protected $primaryKey   = 'PRODUCT_ID';
    public $timestamps 		= false;

    //model mapping 
    protected $maps = [
        'part_id'               => 'PRODUCT_ID',
        'product_number'        => 'PRODUCTNO',
        'description'           => 'DESCRIPTION',
        'img_url'               => 'IMAGE'
    ];

    protected $getterMutators = [
        'description' => 'trim', 
    ];
}
