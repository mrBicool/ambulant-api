<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\OrderSlipHeader;

class Table extends JsonResource
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
        
        $osh = OrderSlipHeader::where('table_id', $this->id)
                ->whereIn('status',['P','B'])
                ->first();

        $is_available = true;

        if( $osh ){
            $is_available = false;
        }
        
        return [
            'branch_id'         => $this->branch_id,
            'id'                => $this->id,
            'number'            => $this->number,
            'code'              => $this->code,
            'description'       => $this->description,
            'guests'            => $this->guests,
            'status'            => $this->status,
            'status2'           => $this->status2,
            'is_available'      => $is_available
        ];
    }
}
