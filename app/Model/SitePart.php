<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class SitePart extends Model
{
    //
    use Eloquence, Mappable, Mutable;
    //
    protected $table 		= 'SiteParts';
    protected $primaryKey   = 'PRODUCT_ID';
    public $timestamps 		= false;

    //model mapping 
    protected $maps = [
      // implicit relation mapping:
      'group' => ['group_code', 'description'],
      'part'  => ['part_id', 'description'],
      // explicit relation mapping:
      //'picture' => 'profile.picture_path',

      // simple alias 
      	'sitepart_id' 			=> 'PRODUCT_ID',
      	'branch_id' 			=> 'ARNOC',
      	'product_name'			=> 'SHORTCODE',
      	'product_description' 	=> 'DESCRIPTION',
        'part_no' 				=> 'PARTNO',
        'cost'                  => 'COST',
      	'srp' 					=> 'RETAIL',
      	'category_id' 			=> 'CATEGORY',
      	'group_id' 				=> 'GROUP',
        'img_url' 				=> 'IMAGE',
        'pre_part_no'           => 'PREPARTNO',     // this is use to identify the admission 0|1  to exclude from the list
        'is_food'               => 'MSGROUP',       // food
        'is_unli'               => 'SSBUFFER', 
        'postmix'               => 'POSTMIX',       // 

        'kitchen_loc'           => 'PRODGRP',       // KITCHEN LOCATION 
        'parts_type'            => 'PARTSTYPE',     // identifier if this product will be save to the kitchen
        // TAX PART
        'is_vat'                => 'VAT',               // is vatable
        'admission_fee'         => 'ADMISSIONFEE',      // admission fee amount
        'amusement_tax'         => 'AMUSEMENTTAX',      // 
        'special_discount'      => 'STDCARCASSWEIGHT'   // 
    ];
    
    protected $getterMutators = [
    	'product_name' 			=>  'trim',
        'product_description' 	=>  'trim',
        'group_id'              =>  'trim'
    ];

    //logic
    public static function findByIdAndBranch($product_id, $branch_id){
        return static::where('ARNOC',       $branch_id)
                    ->where('PRODUCT_ID',   $product_id)
                    ->first();
    }

    public static function getKitchenLocationById($product_id){
        $result = static::where('ARNOC',       config('custom.branch_id'))
                    ->where('PRODUCT_ID',   $product_id)
                    ->first();

        $val = null;
        if( $result ){
            $val = $result->kitchen_loc;
        }
  
        return $val;
    }

    public static function getPartsTypeById($product_id){
     
       $result = static::where('ARNOC',       config('custom.branch_id'))
            ->where('PRODUCT_ID',   $product_id)
            ->first();

        $parts_type = null;
        if($result){
             $parts_type = $result->parts_type;
        }

        return $parts_type;
    }

    //relationshit
    public function group(){
        return $this->belongsTo('App\Group', 'GROUP');
    }

    public function components(){
        return $this->hasMany('App\Postmix', 'PRODUCT_ID', 'PRODUCT_ID');
    }

    public function part(){
        return $this->belongsTo('App\Model\Part','sitepart_id');
    }

     
 

    /**
     * Query Scope
     */
    public function scopeGetByCategory($query,$cat_id){
        return $query->where('category_id',$cat_id)
            ->where('branch_id', config('custom.branch_id'));
    }
}
