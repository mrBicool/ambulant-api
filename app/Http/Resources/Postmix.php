<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Postmix extends JsonResource
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
            'parent_id'        	=> $this->parent_id,
            'product_id'       	=> $this->product_id,
            'quantity'         	=> $this->quantity,
            'unit_cost'        	=> $this->unit_cost,
            'extend_cost'      	=> $this->extend_cost,
            'type'             	=> $this->type,
            'description'      	=> $this->description,
            'parent_partno'    	=> $this->parent_partno,
            'product_partno'    => $this->product_partno,
            'yield'            	=> $this->yield,

            'modifiable'        => $this->modifiable,
            'is_free'           => $this->is_free,
            'comp_cat_id'       => $this->comp_cat_id,
        ];
    }
}
