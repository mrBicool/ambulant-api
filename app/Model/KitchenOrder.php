<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class KitchenOrder extends Model
{
    //
    use Eloquence, Mappable, Mutable;

    /**
     * Model Mapping
     */
    protected $maps = [
        'remarks'       => 'REMARKS', 
        'postmix_id'    => 'POSTMIXID',
        'origin'        => 'ORIGIN',
        'table_id'      => 'TABLE_ID',
        'status'        => 'STATUS'
    ];

    protected $getterMutators = [
        'remarks'   => 'trim', 
    ];

    /**
     * NOTES
     * 
     * origin values
     *  1 = POS
     *  2 = WEB
     *  3 = 
     */
    
     /**
      * logic
      */
      public static function findByBranchAndCode($branch_id, $code){
          return static::where('branch_id', $branch_id)
            ->where('barcode', $code)
            ->first();
      }
}
