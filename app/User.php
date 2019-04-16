<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class User extends Authenticatable
{
    use Notifiable, Eloquence, Mappable, Mutable;

    protected $table        = 'UserSite';
    protected $primaryKey   = 'ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Model Mapping
     */
    protected $maps = [  
        '_id'             => 'ID',
        'username'        => 'NUMBER', 
        'password'        => 'PW',
        'api_token'       => 'TOKEN',
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

    public function current_outlet($clarionDate){
      return $this->isOnDuty($clarionDate)->outlet;
    }

    /**
     * RELATIONSHIT
     */
    public function duties(){
      return $this->hasMany('App\Model\OnDuty', 'CCENUMBER', 'username');
    }
}
