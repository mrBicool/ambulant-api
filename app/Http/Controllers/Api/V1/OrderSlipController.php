<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Model\OrderSlipHeader;
use App\Http\Resources\OrderSlipHeader as OrderSlipHeaderResource;
use App\Http\Resources\OrderSlipDetailCollection;
use App\Model\OrderSlipDetail;
use Illuminate\Support\Facades\Auth;
use App\Library\Helper;

class OrderSlipController extends Controller
{
    //
    public function store(Request $request){ 
        try{

            // init
            $helper = new Helper;
            $osh = new OrderSlipHeader;
            $user = Auth::user();
            $isOnDuty = $user->isOnDuty($helper->getClarionDate(now()));
            

            // begin transaction
            DB::beginTransaction();

            // check if this ambulant has an active sales order 
            $aso = $osh->getActiveOrder($user->username);

            // create slipheader
            if( is_null($aso) ){
                $osh->orderslip_header_id       = $osh->getNewId();
                $osh->branch_id                 = config('settings.branch_id');
                $osh->transaction_type_id       = 1;
                $osh->total_amount              = 0;
                $osh->discount_amount           = 0;
                $osh->net_amount                = 0;
                $osh->status                    = 'B'; //Pending

                $osh->created_at                = now();
                $osh->orig_invoice_date         = $helper->getClarionDate(now());
                $osh->encoded_date              = now();
                $osh->encoded_by                = $user->username;
                $osh->prepared_by               = $user->name;
                $osh->cce_name                  = $user->name;
                $osh->total_hc                  = 1;
                $osh->outlet_id                 = $isOnDuty->storeOutlet->outlet_id;
                $osh->save();

            }else{
                $osh = $aso;
            } 
            
            // save each of item in slipdetails  
            $osd = new OrderSlipDetail;
            $osd->orderslip_detail_id           = $osd->getNewId();
            $osd->orderslip_header_id           = $osh->orderslip_header_id;
            $osd->branch_id                     = config('settings.branch_id');
            $osd->remarks                       = $request->instruction; 
            $osd->order_type                    = $osd->getOrderTypeValue($request->is_take_out);
            $osd->product_id                    = $request->product_id;
            $osd->qty                           = $request->qty;
            $osd->srp                           = $request->price;
            $osd->amount                        = $request->qty * $request->price;
            $osd->net_amount                    = $request->qty * $request->price;
            $osd->status                        = 'B';
            $osd->postmix_id                    = $request->main_product_id;
            $osd->main_product_id               = $request->main_product_id;
            $osd->main_product_comp_id          = $request->main_product_comp_id;
            $osd->main_product_comp_qty         = $request->main_product_comp_qty;
            $osd->part_number                   = $request->part_number; 
            $osd->encoded_date                  = now();
            $osd->save();
            
            if( isset($request->others) ){
                foreach( $request->others as $other){

                    $other = (object)$other; 
                    $osd2 = new OrderSlipDetail; 
                    $osd2->orderslip_detail_id           = $osd2->getNewId();
                    $osd2->orderslip_header_id           = $osh->orderslip_header_id;
                    $osd2->branch_id                     = config('settings.branch_id');
                    $osd2->remarks                       = $request->instruction; 
                    $osd2->order_type                    = $osd2->getOrderTypeValue($request->is_take_out);
                    $osd2->product_id                    = $other->product_id;
                    $osd2->qty                           = $other->qty;
                    $osd2->srp                           = $other->price;
                    $osd2->amount                        = $other->qty * $other->price;
                    $osd2->net_amount                    = $other->qty * $other->price;
                    $osd2->status                        = 'B';
                    $osd2->postmix_id                    = $other->main_product_id;
                    $osd2->main_product_id               = $other->main_product_id;
                    $osd2->main_product_comp_id          = $other->main_product_component_id;
                    $osd2->main_product_comp_qty         = $other->main_product_component_qty;
                    $osd2->part_number                   = $other->part_number;
                    $osd2->encoded_date                  = now();
                    $osd2->save(); 

                    if( isset($other->others) ){
                        foreach( $other->others as $other2){
                            $other2 = (object)$other2;  
                            $osd3 = new OrderSlipDetail; 
                            $osd3->orderslip_detail_id           = $osd3->getNewId();
                            $osd3->orderslip_header_id           = $osh->orderslip_header_id;
                            $osd3->branch_id                     = config('settings.branch_id');
                            $osd3->remarks                       = $request->instruction; 
                            $osd3->order_type                    = $osd3->getOrderTypeValue($request->is_take_out);
                            $osd3->product_id                    = $other2->product_id;
                            $osd3->qty                           = $other2->qty;
                            $osd3->srp                           = $other2->price;
                            $osd3->amount                        = $other2->qty * $other2->price;
                            $osd3->net_amount                    = $other2->qty * $other2->price;
                            $osd3->status                        = 'B';
                            $osd3->postmix_id                    = $other2->main_product_id;
                            $osd3->main_product_id               = $other2->main_product_id;
                            $osd3->main_product_comp_id          = $other2->main_product_component_id;
                            $osd3->main_product_comp_qty         = $other->main_product_component_qty;
                            $osd3->part_number                   = $other2->part_number;
                            $osd3->encoded_date                  = now();
                            $osd3->save(); 
                        }
                    }
                }
            }

            //save the total into OrderSlipHeader
  
            // commit all changes
            DB::commit(); 

            return response()->json([
                'success'   => true,
                'status'    => 201,
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

    public function getActiveOrder(){
        try{
            // init
            $helper = new Helper;
            $osh = new OrderSlipHeader;
            $osd = new OrderSlipDetail;

            $user = Auth::user();
            $isOnDuty = $user->isOnDuty($helper->getClarionDate(now()));
            

            // begin transaction
            DB::beginTransaction();

            // check if this ambulant has an active sales order
            $header = $osh->getActiveOrder($user->username);
            $details = null;
            $_details = null;

            if($header){
                $header = new OrderSlipHeaderResource($header);
                $details = $osd->getByOrderSlipHeaderId($header->orderslip_header_id);
                $details = new OrderSlipDetailCollection($details);
                $_details = $details->groupBy('main_product_id'); //
            }
            
            return response()->json([
                'success'   => true,
                'status'    => 200,
                'result'    => [
                    'header' => $header,
                    '_details' => $details,
                    'details' => $_details
                ]
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