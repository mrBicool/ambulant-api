<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class Customer extends Model
{
    //
    use Eloquence, Mappable, Mutable;
    //
    protected $table = 'Customers'; 
    public $timestamps = false;

    //model mapping 
    protected $maps = [
        'branch_id'         => 'BRANCHID',
        'customer_id'       => 'CUSTOMERID',
        'customer_code'     => 'CUSTOMERCODE',
        'customer_type'     => 'CUSTOMETYPE',
        'name'              => 'NAME',
        'address'           => 'ADDRESS',
        'tin'               => 'TIN',
        'business_style'    => 'BUSINESSSTYLE',
        'user_id'           => 'USER_ID',
        'wallet'            => 'WALLET', 
        'points'            => 'POINTS',
        'mobile_number'     => 'MOBILE_NUMBER',
        'birthdate'         => 'BIRTHDATE',
        'is_loyalty'        => 'IS_LOYALTY',
        'is_inhouse'        => 'IS_INHOUSE',
        'scpwd_id'          => 'SCPWD_ID',
    ];
    
    protected $getterMutators = [
        'name'              => 'trim',
        'address'           => 'trim',
        'scpwd_id'          => 'trim', 
    ];

    /**
     * Relationship
     * 
     */
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * LOGIC
     */
    public function getNewId(){
    	$result = static::where('branch_id', config('custom.branch_id'))
    				->orderBy('customer_id','desc')
    				->first(); 
        if( is_null($result)){
            return 1;
        }
    	return $result->customer_id + 1;
    }

    public function findByMobile($val){ 
        return static::where('mobile_number',$val)->first(); 
    }
}
