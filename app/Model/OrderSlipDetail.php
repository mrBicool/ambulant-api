<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class OrderSlipDetail extends Model
{
    //
    use Eloquence, Mappable, Mutable;
    //
    protected $table 		= 'OrderSLipDetails';
    public $timestamps 		= false;

    //model mapping
    protected $maps = [ 
        'part'                  => ['part_id', 'description'],
    	'branch_id' 			=> 'BRANCHID',
    	'orderslip_detail_id' 	=> 'ORDERSLIPDETAILID',
     	'orderslip_header_id' 	=> 'ORDERSLIPNO',
    	'product_id' 			=> 'PRODUCT_ID',
    	'part_number'			=> 'PARTNO',
    	'product_group_id'		=> 'PRODUCTGROUP',
    	'qty' 					=> 'QUANTITY',
    	'srp' 					=> 'RETAILPRICE', 
		'amount' 				=> 'AMOUNT',
        'net_amount'            => 'NETAMOUNT',
		'remarks'				=> 'REMARKS',
		'order_type'			=> 'OSTYPE',
		'status'				=> 'STATUS',
		'postmix_id' 			=> 'POSTMIXID',
		'is_modify'				=> 'IS_MODIFY',
        'line_number'           => 'LINE_NO',
        'old_comp_id'           => 'OLD_COMP_ID',
        'order_no' 			    => 'ORNO',
        'sequence'              => 'SEQUENCE',
		'customer_id'			=> 'CUSTOMERCODE',
        'encoded_date'          => 'ENCODEDDATE',
        'main_product_id'       => 'MAIN_PRODUCT_ID',
        'main_product_comp_id'  => 'MAIN_PRODUCT_COMPONENT_ID',
        'main_product_comp_qty' => 'MAIN_PRODUCT_COMPONENT_QTY',
        'guest_no'              => 'GUESTNO',
        'guest_type'            => 'GUEST_TYPE'
    ];

    protected $getterMutators = [
        'part_number'   => 'trim', 
        'order_type'    => 'trim'
    ];

    /**
     * Relationship
     */
    public function part(){
        return $this->belongsTo('App\Model\Part','product_id');
    }

    public function sitePart(){
        return $this->belongsTo('App\Model\SitePart','product_id');
    }



    /**
     * Logic
     */
    public function getNewId(){
    	$result = static::where('branch_id', config('settings.branch_id'))
    				->orderBy('orderslip_detail_id','desc')
                    ->first();

        if( is_null($result)){
            return 1;
        }			
    	return $result->orderslip_detail_id + 1;
    }

    public function getOrderTypeValue($str, $bool = null){
        if($str == 'true'){
            return 2; // take out
        }else {
            return 1; // dine in
        }
    }

    public function getByOrderSlipHeaderId($id){
        return static::where('orderslip_header_id',$id)
            ->where('branch_id', config('settings.branch_id'))
            ->get();
    }

    public function getNewSequence($branch_id, $header_id, $product_id){
        $result = static::where('branch_id', $branch_id)
                    ->where('orderslip_header_id',$header_id)
                    ->where('product_id',$product_id)
                    ->orderBy('encoded_date','desc')
                    ->first();

        if(is_null($result)){
            return 1;
        }else{
            return $result->sequence+1;
        } 
    }

    public function removeByHeaderIdAndBranchId($header_id, $branch_id, $sequence, $main_product_id){
        return static::where('orderslip_header_id', $header_id)
            ->where('branch_id', $branch_id)
            ->where('sequence', $sequence)
            ->where('main_product_id', $main_product_id)
            ->delete();
    }

    public function getSingleOrder(
        $header_id,
        $main_product_id,
        $sequence
    ){
        return static::where('branch_id', config('settings.branch_id'))
            ->where('orderslip_header_id',$header_id)
            ->where('main_product_id', $main_product_id)
            ->where('sequence', $sequence)
            ->get(); 
    }

    public static function getLastLineNumber($branch_id, $os_id){
        $result =  static::where('branch_id', $branch_id)
            ->where('orderslip_header_id',$os_id) 
            ->orderby('line_number','desc')
            ->first(); 

        if($result == null){
            return 1;
        }

        return $result->line_number;
    }
    
}
