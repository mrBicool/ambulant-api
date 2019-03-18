<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class UserSite extends Model
{
    //
    use Eloquence, Mappable, Mutable;

    protected $table        = 'UserSite';
    protected $primaryKey   = 'ID';
    public $timestamps = false;

    /**
     * Model Mapping
     */
    protected $maps = [  
      'username'        => 'NUMBER', 
      'password'        => 'PW',
      'token'           => 'TOKEN',
      'name'            => 'NAME'
    ];

    protected $getterMutators = [
        'password'  => 'trim',
        'name'      => 'trim'
    ];

    /**
     * Logic
     */
    public static function findByUsername($username){
      return static::where('username', $username)->first();
    }

    public static function findByToken($token){
      return static::where('token', $token)->first();
    }

    public function isOnDuty($clarionDate){
        
        $result = $this->duties->sortByDesc('date')->first();  
        
        if( is_null($result) ){
            return false;
        }
 
        if( $clarionDate == $result->date){
          return $result;
        }

        return false;
    }

    /**
     * RELATIONSHIT
     */
    public function duties(){
      return $this->hasMany('App\Model\OnDuty', 'CCENUMBER', 'username');
    }
    
}
