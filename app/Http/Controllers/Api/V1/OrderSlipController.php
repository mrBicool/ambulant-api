<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderSlipController extends Controller
{
    //
    public function store(Request $request){
        return response()->json([
            $request->data
        ]);
    }
}
