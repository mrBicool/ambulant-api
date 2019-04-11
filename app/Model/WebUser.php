<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class WebUser extends Model
{
    //
    use Eloquence, Mappable, Mutable;

    /**
     * Override
     */
    protected $table    = 'web_users';

    /**
     * Relationship
     */
    public function customer(){
        return $this->hasOne('App\Customer','user_id');
    }

    /**
     * Logic
     */
    public function getNewId(){
        $result = static::orderBy('id','desc')->first(); 
        if( is_null($result)){
            return 1;
        }
    	return $result->id + 1;
    } 
    
    public static function findByEmail($val){
        return static::where('email',$val)->first();
    }

    public static function findByMobile($val){
        return static::where('mobile_number',$val)->first();
    }
}
