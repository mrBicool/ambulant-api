<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\KitchenOrder;
use App\Model\SitePart;

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

        $ko = KitchenOrder::where('branch_id',$this->branch_id)
            ->where('origin',2)
            ->where('header_id', $this->orderslip_header_id)
            ->where('detail_id', $this->orderslip_detail_id)
            ->where('part_id', $this->product_id)
            ->where('status', 'A')
            ->first();

        $ko_status = null;

        if($ko){
            $ko_status = 'FOR PICKUP';
        }

        // sitepart
        $sp = SitePart::where('branch_id', $this->branch_id)
                ->where('sitepart_id', $this->product_id)
                ->first();
        \Log::debug($sp);

        //return parent::toArray($request);
        return [
            'branch_id' 			=> $this->branch_id, 
            'orderslip_detail_id' 	=> $this->orderslip_detail_id, 
            'orderslip_header_id' 	=> $this->orderslip_header_id, 
            'product_id' 			=> $this->product_id, 
            'name'                  => $this->part->description,
            'part_number'			=> $this->part_number, 
            'product_group_id'		=> $this->product_group_id, 
            'qty' 					=> (double)$this->qty, 
            'srp' 					=> (double)$this->srp, 
            'amount' 				=> $this->amount,
            'net_amount'            => $this->net_amount,
            'remarks'				=> $this->remarks,
            'order_type'			=> $this->order_type,  
            'status'				=> $this->status,
            'postmix_id' 			=> $this->postmix_id,
            'is_modify'				=> $this->is_modify,
            'line_number'           => $this->line_number,
            'old_comp_id'           => $this->old_comp_id,
            'sequence' 			    => $this->sequence,
            'customer_id'			=> $this->customer_id,
            'encoded_date'          => $this->encoded_date,
            'main_product_id'       => $this->main_product_id,
            'main_product_comp_id'  => $this->main_product_comp_id,
            'main_product_comp_qty' => $this->main_product_comp_qty,
            'guest_no'              => $this->guest_no,
            'guest_type'            => $this->guest_type,
            'kitchen_status'        => $ko_status,
            'is_vatable'            => $sp->is_vat,
            'is_food'               => $sp->is_food,
            'is_admission'          => $sp->pre_part_no,
            'is_unli'               => $sp->is_unli
        ];
    }
}
