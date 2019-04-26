<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\OrderSlipDetail;
use App\Http\Resources\OrderSlipDetailCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderSlipDetailController extends Controller
{
    //
    public function destroy(Request $request){

        try{

            $result = OrderSlipDetail::where( 'branch_id', $request->branch_id )
                        ->where('orderslip_header_id', $request->header_id)
                        ->where('main_product_id', $request->main_product_id)
                        ->where('sequence', $request->sequence)
                        ->delete();
            
            return response()->json([
                'success'   => true,
                'status'    => 200, 
                'message'   => 'Removed Successfully'
            ]); 

        }catch( \Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'success'   => false,
                'status'    => 500,
                'message'   => $e->getMessage() 
            ]);
        }
    }
    
    public function getByHeader(Request $request){ 
        
        try{
            $osd = new OrderSlipDetail; 
            $result = $osd->getSingleOrder(
                $request->header_id,
                $request->main_product_id,
                $request->sequence
            );

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'result'    => new OrderSlipDetailCollection($result)
            ]);

        }catch( \Exception $e){ 
            Log::error($e->getMessage());
            return response()->json([
                'success'   => false,
                'status'    => 500,
                'message'   => $e->getMessage() 
            ]);
        }
    }
}