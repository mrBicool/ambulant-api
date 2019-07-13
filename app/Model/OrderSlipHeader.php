<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable; // extension trait
use Sofa\Eloquence\Mutable; // extension trait

class OrderSlipHeader extends Model
{
    // 
    use Eloquence, Mappable, Mutable;
    //
    protected $table 		= 'OrderSlipHeader';  
    public $incrementing = false;
    public $timestamps 		= false;
     
    //model mapping
    protected $maps = [  
      // simple alias
	    'orderslip_header_id'	=> 'ORDERSLIPNO',
		'branch_id' 			=> 'BRANCHID',
		'transaction_type_id'	=> 'TRANSACTTYPEID',
		'total_amount'			=> 'TOTALAMOUNT',
		'discount_amount'		=> 'DISCOUNT',
		'net_amount' 			=> 'NETAMOUNT',
		'status' 				=> 'STATUS',    // [ 'P' => 'Pending', 'C' => 'Completed'. 'B' => 'CurrentSelected' ]
		'customer_id' 			=> 'CUSTOMERCODE',
		'mobile_number' 		=> 'CELLULARNUMBER',
		'customer_name' 		=> 'CUSTOMERNAME',
        'created_at' 			=> 'OSDATE',
        'orig_invoice_date'     => 'ORIGINALINVOICEDATE',
        'encoded_date'          => 'ENCODEDDATE',
        'encoded_by'            => 'ENCODEDBY',
        'prepared_by'           => 'PREPAREDBY',
        'cce_name'              => 'CCENAME',
        'total_hc'              => 'TOTALHEADCOUNT',
        'outlet_id'             => 'OUTLET_ID',
        'table_id'              => 'TABLENO'
    ];
    
    protected $getterMutators = [
        'prepared_by'       => 'trim',
        'cce_name'          => 'trim',
        'customer_name'     => 'trim',
        'customer_id'       => 'trim',
        'mobile_number'     => 'trim'
    ];

    //logic
    public function getNewId(){
    	$result = static::where('branch_id', config('settings.branch_id'))
    				->orderBy('orderslip_header_id','desc')
                    ->first();

        if( is_null($result)){
            return 1;
        }			
    	return $result->orderslip_header_id + 1;
    }

    public function getActiveOrder($ambulant_id){
        return static::where('encoded_by', $ambulant_id)
            ->where('status','B')
            ->first();
    }

    public function getMaps(){
    	return $this->maps;
    }

    public function removeByHeaderIdAndBranchId($header_id, $branch_id){
        return static::where('orderslip_header_id', $header_id)
            ->where('branch_id', $branch_id)
            ->delete();
    }

    //relationship
    public function transType(){
        return $this->belongsTo('App\TransactionType', $this->maps['transaction_type_id']);
    }
}
