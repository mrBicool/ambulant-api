<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderSlipDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
        'branch_id' 			=> $this->branch_id, 
    	'orderslip_detail_id' 	=> $this->orderslip_detail_id, 
     	'orderslip_header_id' 	=> $this->orderslip_header_id, 
    	'product_id' 			=> $this->product_id, 
    	'part_number'			=> $this->part_number, 
    	'product_group_id'		=> $this->product_group_id, 
    	'qty' 					=> $this->qty, 
    	'srp' 					=> $this->srp, 
		'amount' 				=> $this->amount,
        'net_amount'            => $this->net_amount,
		'remarks'				=> $this->remarks,
		'order_type'			=> $this->order_type,  
		'status'				=> $this->status,
		'postmix_id' 			=> $this->postmix_id,
		'is_modify'				=> $this->is_modify,
        'line_number'           => $this->line_number,
        'old_comp_id'           => $this->old_comp_id,
		'or_number' 			=> $this->or_number,
		'customer_id'			=> $this->customer_id,
        'encoded_date'          => $this->encoded_date,
        'main_product_id'       => $this->main_product_id,
        'main_product_comp_id'  => $this->main_product_comp_id,
        'main_product_comp_qty' => $this->main_product_comp_qty
        ];
    }
}
