<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderSlipHeader;
use App\Http\Resources\OrderSlipHeader as OrderSlipHeaderResource;
use DB;
class OrderSlipHeaderController extends Controller
{
    //
    public function update(Request $request){
        try{
            // begin transaction
            DB::beginTransaction();

            // check if table no. is currently used
            $checkTable = OrderSlipHeader::where('branch_id', $request->branch_id)
                            ->where('orderslip_header_id', '!=',  $request->orderslip_header_id)
                            ->where('table_id', $request->table_id)
                            ->whereIn('status',['B','P']) 
                            ->first();

            if($checkTable){
                DB::rollBack(); 
                return response()->json([
                    'success'   => false,
                    'status'    => 200,
                    'message'   => 'Table No. is currently used.'
                ]); 
            }
            //dd($checkTable);

            //logic
            $data   = $request->except(['branch_id','orderslip_header_id','_method']); 
            $result = OrderSlipHeader::where('branch_id', $request->branch_id)
                        ->where('orderslip_header_id',$request->orderslip_header_id)
                        ->update(
                            $data
                        );
            
            if(!$result){
                DB::rollBack(); 
                return response()->json([
                    'success'   => false,
                    'status'    => 200,
                    'message'   => 'Update failed'
                ]); 
            }

            // commit all changes
            DB::commit(); 

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => 'Success'
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
}
