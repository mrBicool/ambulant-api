<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GuestSelectionController extends Controller
{
    //
    public function guestselect(){
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'message'   => 'sucess',
            'test'      => "pota"
        ]);
    }
}
