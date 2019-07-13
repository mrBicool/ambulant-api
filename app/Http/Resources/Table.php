<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'branch_id'         => $this->branch_id,
            'id'                => $this->id,
            'number'            => $this->number,
            'code'              => $this->code,
            'description'       => $this->description,
            'guests'            => $this->guests,
            'status'            => $this->status,
            'status2'           => $this->status2
        ];
    }
}
