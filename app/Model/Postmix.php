<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class Postmix extends Model
{
    //
    use Eloquence, Mappable, Mutable;

    protected $table    = 'Postmix';
    public $timestamps  = false;

    /**
     * Model Mapping
     */
    protected $maps = [
    	'parent_id'        	=> 'PRODUCT_ID',
        'product_id'       	=> 'PARTSID',
        'quantity'         	=> 'QUANTITY',
        'unit_cost'        	=> 'UNITCOST',
        'extend_cost'      	=> 'EXTENDCOST',
        'type'             	=> 'TYPE',
        'description'      	=> 'DESCRIPTION',
        'parent_partno'     => 'PARTNO',
        'product_partno'    => 'PARTSPARTNO',
        'yield'            	=> 'YIELD',
        'modifiable'        => 'MODIFIABLE',
        'is_free'           => 'ISFREE',
        'comp_cat_id'       => 'COMPCATID',
        'display'           => 'Display'
    ];
    protected $getterMutators = [
        'description'		=> 'trim',
        'parent_partno'		=> 'trim',
        'product_partno'	=> 'trim',
        'comp_cat_id'	    => 'trim',
    ];

    /**
     * RELATIONSHIT
     */
    public function partLocation(){
        return $this->belongsTo('App\Model\PartLocation','PARTSID','PRODUCT_ID');
    }

    public function sitePart(){ 
       return $this->belongsTo('App\Model\SitePart', 'PARTSID');
    }

}
