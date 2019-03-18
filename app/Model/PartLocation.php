<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class PartLocation extends Model
{
    //
    use Eloquence, Mappable, Mutable;
    //
    protected $table    = 'PartsLocation';
    public $timestamps  = false;

    /**
     * Model Mapping
     */
    protected $maps = [
        'product_id'        => 'PRODUCT_ID',
        'outlet_id'         => 'OUTLETID',
        'description'       => 'DESCRIPTION',
        'group_id'          => 'GROUP',
        'category_id'       => 'CATEGORY',
        'short_code'        => 'SHORTCODE',
        'retail'            => 'RETAIL',
        'postmix'           => 'POSTMIX',
        'prepartno'         => 'PREPARTNO',
        'ssbuffer'          => 'SSBUFFER',
        'is_food'           => 'MSGROUP',
        'qty'               => 'QUANTITY',
        'kitchen_location'  => 'PRODGRP'
    ];

    protected $getterMutators = [
        'description'   => 'trim',
        'group_id'      => 'trim',
        'category'      => 'trim',
        'short_code'    => 'trim',
    ];

    /**
     * RELATIONSHIT
     */
    public function group(){
        return $this->belongsTo('App\Model\Group', 'group_id');
    }
    
    public function postmixModifiableComponents(){
        return $this->hasMany('App\Model\Postmix','PRODUCT_ID','PRODUCT_ID')
            ->where('MODIFIABLE',1);
    }

    public function postmixNoneModifiableComponents(){ 
       return $this->hasMany('App\Model\Postmix','PRODUCT _ID','PRODUCT _ID')
            ->where('MODIFIABLE',0);
     }

     /**
      * LOGIC
      */
    public static function getByOutletAndGroupAndCategory($outlet_id, $gid, $cid, $limit = 15){
        return static::where('outlet_id', $outlet_id)
                ->where('group_id', $gid)
                ->where('category_id', $cid)
                ->simplePaginate($limit);
    }
}
