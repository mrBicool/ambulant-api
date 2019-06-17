<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class OrderSlipHeader extends JsonResource
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
        $created_at = new Carbon($this->created_at);
        return [
            'orderslip_header_id' => $this->orderslip_header_id,
            'branch_id' => $this->branch_id,
            'transaction_type_id' => $this->transaction_type_id,
            'total_amount' => $this->total_amount,
            'discount_amount' => $this->discount_amount,
            'net_amount' => (double)$this->net_amount,
            'status' => $this->status,
            'customer_id' => $this->customer_id,
            'mobile_number' => $this->mobile_number,
            'customer_name' => $this->customer_name,
            'created_at' => $created_at->diffForHumans(),
            'orig_invoice_date' => $this->orig_invoice_date,
            'encoded_date' => $this->encoded_date,
            'encoded_by' => $this->encoded_by,
            'prepared_by' => $this->prepared_by,
            'cce_name' => $this->cce_name,
            'total_hc' => $this->total_hc,
            'outlet_id' => $this->outlet_id,
            'table_id' => $this->table_id
        ];
    }
}
