<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
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
            'branch_id'         => $this->branch_id,
            'customer_id'       => $this->customer_id,
            'customer_code'     => $this->customer_code,
            'customer_type'     => $this->customer_type,
            'name'              => $this->name,
            'address'           => $this->address,
            'tin'               => $this->tin,
            'business_style'    => $this->business_style,
            'user_id'           => $this->user_id,
            'wallet'            => (double)$this->wallet, 
            'points'            => (double)$this->points,
            'mobile_number'     => $this->mobile_number,
            'birthdate'         => $this->birthdate,
            'is_loyalty'        => $this->is_loyalty,
            'is_inhouse'        => $this->is_inhouse,
            'scpwd_id'          => $this->scpwd_id,
        ];
    }
}
