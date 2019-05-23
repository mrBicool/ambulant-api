<?php 

namespace App\Services;
use App\Model\BranchLastIssuedNumber;
use Carbon\Carbon;

class BranchLastIssuedNumberServices {

	public $id 		= null;
	public $new_id	= null;
	private $blin; //model

	public function __construct($branch_id){ 
        $this->blin = new BranchLastIssuedNumber;
        $this->findOrCreate($branch_id);
	}

	public function findOrCreate($branch_id){ 
		$r = $this->blin->where('branch_id', $branch_id )->first();

		if( is_null($r) ){
			$this->create($branch_id);
			return;
		} 

		$this->blin = $r;
	}

	public function create($branch_id){
		$this->blin->branch_id 				= $branch_id;
		$this->blin->save();  
	}

	public function getNewIdForSalesOrderHeader(){
		$this->blin->sales_order_header_no += 1;
		$this->blin->save();
		return $this->blin->sales_order_header_no;
	}

	public function getNewIdForSalesOrderDetails(){ 
		$this->blin->sales_order_details_no += 1;
		$this->blin->save(); 
		return $this->blin->sales_order_details_no;
	}

	public function getNewIdForCustomer(){ 
		$this->blin->customer_no += 1;
		$this->blin->save(); 
		return $this->blin->customer_no;
	}

	public function getNewIdForRedemptionHeader(){ 
		$this->blin->redemption_header_no += 1;
		$this->blin->save(); 
		return $this->blin->redemption_header_no;
	}

	public function getNewIdForRedemptionDetails(){ 
		$this->blin->redemption_details_no += 1;
		$this->blin->save(); 
		return $this->blin->redemption_details_no;
	}

	public function getNewIdForOrderSlipHeader(){ 
		$this->blin->order_slip_header_no += 1;
		$this->blin->save(); 
		return $this->blin->order_slip_header_no;
	}

	public function getNewIdForOrderSlipDetails(){ 
		$this->blin->order_slip_detail_no += 1;
		$this->blin->save(); 
		return $this->blin->order_slip_detail_no;
	}

	public function getNewIdForInvoice()
	{ 
		$this->blin->invoice_no += 1;
		$this->blin->save();
		return $this->blin->invoice_no;
	}

	public function getInvoice(){
		return $this->blin->invoice_no;
	}

	public function getNewIdForNoneInvoice()
	{ 
		$this->blin->invoice_no += 1;
		$this->blin->save();
		return $this->blin->invoice_no;
	}

	public function increaseInvoiceResetCounter()
	{ 
		$this->blin->invoice_reset_counter += 1;
		$this->blin->save();
		return true;
	}

	public function getNewIdForTransaction()
	{ 
		$this->blin->transaction_no += 1;
		$this->blin->save();
		return $this->blin->transaction_no;
	}

	public function getTransaction()
	{
		return $this->blin->transaction_no;
	}

	public function getNewIdForKitchenOrder(){ 
		$this->blin->kitchen_order_no += 1;
		$this->blin->save();
		return $this->blin->kitchen_order_no;
	}

	public function getKitchenOrder()
	{ 
		return $this->blin->kitchen_order_no;
	}

}