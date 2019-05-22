<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Model\OrderSlipHeader;
use App\Model\OrderSlipDetail;
use App\Http\Resources\OrderSlipHeader as OrderSlipHeaderResource;
use App\Http\Resources\OrderSlipDetailCollection; 
use App\Http\Resources\OrderSlipHeaderCollection; 
use Illuminate\Support\Facades\Auth;
use App\Library\Helper;
use App\Model\BranchLastIssuedNumber;
use App\Model\SitePart;
use App\Model\KitchenOrder;

class OrderSlipController extends Controller
{
    //
    public function store(Request $request){ 
        try{
            
            // init
            $helper     = new Helper;
            $osh        = new OrderSlipHeader;
            $user       = Auth::user(); 
            $isOnDuty   = $user->isOnDuty($helper->getClarionDate(now()));

            // $blin       = BranchLastIssuedNumber::first(); 

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
            
            $net_amount = 0;
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
            //$osd->is_modify                     = 1;
            $osd->status                        = 'B';
            $osd->postmix_id                    = $request->main_product_id;
            $osd->main_product_id               = $request->main_product_id;
            $osd->main_product_comp_id          = $request->main_product_comp_id;
            $osd->main_product_comp_qty         = $request->main_product_comp_qty;
            $osd->part_number                   = $request->part_number; 
            $osd->encoded_date                  = now();
            $osd->sequence                      = $osd->getNewSequence( config('settings.branch_id'), $osh->orderslip_header_id, $request->product_id );
            $osd->save();

            $net_amount += $osd->net_amount;

            
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
                    $osd2->is_modify                     = 1;
                    $osd2->status                        = 'B';
                    $osd2->postmix_id                    = $other->main_product_id;
                    $osd2->main_product_id               = $other->main_product_id;
                    $osd2->main_product_comp_id          = $other->main_product_component_id;
                    $osd2->main_product_comp_qty         = $other->main_product_component_qty;
                    $osd2->part_number                   = $other->part_number;
                    $osd2->encoded_date                  = now();
                    $osd2->sequence                      = $osd->sequence;
                    $osd2->save(); 
                    $net_amount += $osd2->net_amount;

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
                            $osd3->is_modify                     = 1;
                            $osd3->status                        = 'B';
                            $osd3->postmix_id                    = $other2->main_product_id;
                            $osd3->main_product_id               = $other2->main_product_id;
                            $osd3->main_product_comp_id          = $other2->main_product_component_id;
                            $osd3->main_product_comp_qty         = $other->main_product_component_qty;
                            $osd3->part_number                   = $other2->part_number;
                            $osd3->encoded_date                  = now();
                            $osd3->sequence                      = $osd->sequence;
                            $osd3->save(); 
                            $net_amount += $osd3->net_amount;
                        }
                    }
                }
            }

            // saving none modifiable component
            if( isset($request->none_modifiable_component) ){
                foreach( $request->none_modifiable_component as $nmc){ 
                    $nmc = (object)$nmc; 
                    $_osd = new OrderSlipDetail;  
                    $_osd->orderslip_detail_id           = $_osd->getNewId();
                    $_osd->orderslip_header_id           = $osh->orderslip_header_id;
                    $_osd->branch_id                     = config('settings.branch_id');
                    $_osd->remarks                       = $request->instruction; 
                    $_osd->order_type                    = $_osd->getOrderTypeValue($request->is_take_out);
                    $_osd->product_id                    = $nmc->product_id;
                    $_osd->qty                           = ($nmc->quantity * $osd->qty);
                    $_osd->srp                           = 0;
                    $_osd->amount                        = $_osd->qty * $_osd->srp;
                    $_osd->net_amount                    = $_osd->qty * $_osd->srp;
                    $_osd->is_modify                     = 0;
                    $_osd->status                        = 'B';
                    $_osd->postmix_id                    = $osd->product_id;
                    $_osd->main_product_id               = $osd->product_id;
                    $_osd->main_product_comp_id          = $_osd->product_id;
                    $_osd->main_product_comp_qty         = $_osd->qty;
                    $_osd->part_number                   = $nmc->product_partno;
                    $_osd->encoded_date                  = now();
                    $_osd->sequence                      = $osd->sequence;
                    $_osd->save(); 
                }
            }

            //save the total into OrderSlipHeader
            OrderSlipHeader::where('orderslip_header_id',$osh->orderslip_header_id)
                ->update(['NETAMOUNT'=> $net_amount]);
  
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
            $helper     = new Helper;
            $osh        = new OrderSlipHeader;
            $osd        = new OrderSlipDetail;

            $user       = Auth::user();
            $isOnDuty   = $user->isOnDuty($helper->getClarionDate(now()));
            
            // begin transaction
            DB::beginTransaction();

            // check if this ambulant has an active sales order
            $header     = $osh->getActiveOrder($user->username);
            $details    = null; 

            if($header){
                $header     = new OrderSlipHeaderResource($header);
                $details    = $osd->getByOrderSlipHeaderId($header->orderslip_header_id);
                $details    = new OrderSlipDetailCollection($details);
                $details    = $details->groupBy(['main_product_id','sequence']); //
            } 

            // commit all changes
            DB::commit(); 

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'result'    => [
                    'header' => $header, 
                    'details' => $details
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

    /**
     * @param Request
     * @return 
     */
    public function markAsDone(Request $request){
        try{

            // begin transaction
            DB::beginTransaction();

            // saving to kitchen
            $blin       = BranchLastIssuedNumber::first();
            $results    = OrderSlipDetail::where('orderslip_header_id', $request->orderslip_id)
                            ->where('branch_id', config('settings.branch_id'))
                            ->get(); 

            foreach ($results as $key => $item) {
                # code...
               
                $sp = SitePart::where('sitepart_id', $item->product_id)
                    ->where('branch_id', config('settings.branch_id'))
                    ->first();  
                
                if( strtolower($sp->parts_type) == 'y'){
                    //dd($item,$sp);
                    $blin->kitchen_order_no = $blin->kitchen_order_no + 1;
                    $this->saveToKitchen(
                        $blin->kitchen_order_no,
                        $item->orderslip_header_id,
                        $item->orderslip_detail_id,
                        $item->product_id,
                        $item->main_product_id,
                        $sp->kitchen_loc,
                        $item->qty,
                        0,
                        $item->remarks
                    );
                    $blin->save();
                }
            }  

            //dd('STOP');

            // update header status to P
            OrderSlipHeader::where('orderslip_header_id',$request->orderslip_id)
                ->where('branch_id', config('settings.branch_id'))
                ->update([
                    'STATUS' => 'P'
                ]);

            // update details status to P
            OrderSlipDetail::where('orderslip_header_id', $request->orderslip_id)
                ->where('branch_id', config('settings.branch_id'))
                ->update([
                    'STATUS' => 'P'
                ]);

            // commit all changes
            DB::commit(); 
            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => 'Marked as done successfully'
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

    /**
     * @return
     */
    public function pendingByOutlet(Request $request){

        $helper = new Helper;
        $result = OrderSlipHeader::where('status', 'P')
                    ->orWhere('status', 'B')
                    ->where('outlet_id', $request->outlet_id)  
                    ->orderBy('status')
                    ->orderBy('created_at','desc')
                    ->get();

        $result = new OrderSlipHeaderCollection($result);
        //$result = $result->groupBy('status');
        return response()->json([
            'success' => true,
            'status' => 200,
            'result' => $result
        ]);
    }

    /**
     * @return json
     */
    public function completedByOutlet(Request $request){
        $result = OrderSlipHeader::where('status', 'C') 
                    ->where('outlet_id', $request->outlet_id) 
                    ->orderBy('created_at','desc')
                    ->get();

        $result = new OrderSlipHeaderCollection($result);
        
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'result'    => $result
        ]);
    }

    public function changeOs(Request $request){
        try{

            // begin transaction
            DB::beginTransaction();

            $user = Auth::user(); 
            // check for current orderslip
            $osh = new OrderSlipHeader;
            $result = $osh->getActiveOrder($user->_id); 
                // if yes, change the status to 'P'
                if($result){
                    OrderSlipHeader::where('orderslip_header_id', $result->orderslip_header_id)
                        ->update([
                            'STATUS' => 'P'
                        ]);
                }
                
            // change the encoded by and name to current user
            OrderSlipHeader::where('orderslip_header_id', $request->header_id)
                ->update([
                    'PREPAREDBY' => $user->name,
                    'CCENAME'   => $user->name,
                    'ENCODEDBY' => $user->_id,
                    'STATUS'    => 'B'
                ]);  

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

    /**
     * @param Request
     * @json string [data, header_id, main_product_id, sequence]
     * @return json 
     */
    public function update(Request $request){ 

        try{

            // begin transaction
            DB::beginTransaction();

            $jsonOjb = json_decode($request->data); 
            $branch_id = config('settings.branch_id'); 

            // # TODO
            // // remove all items in detail
            $old_osd = new OrderSlipDetail;
            $old_osd->removeByHeaderIdAndBranchId(
                    $jsonOjb->header_id, 
                    $branch_id,
                    $jsonOjb->sequence,
                    $jsonOjb->main_product_id
                );


            // // save new items detail 
            $orders = $jsonOjb->data; 
            $net_amount = 0;
            // save each of item in slipdetails  
            $osd = new OrderSlipDetail;  
            $osd->orderslip_detail_id           = $osd->getNewId();
            $osd->orderslip_header_id           = $jsonOjb->header_id;
            $osd->branch_id                     = $branch_id;
            $osd->remarks                       = $orders->instruction; 
            $osd->order_type                    = $osd->getOrderTypeValue($orders->is_take_out);
            $osd->product_id                    = $orders->product_id;
            $osd->qty                           = $orders->qty;
            $osd->srp                           = $orders->price;
            $osd->amount                        = $orders->qty * $orders->price;
            $osd->net_amount                    = $orders->qty * $orders->price; 
            $osd->status                        = 'B';
            $osd->postmix_id                    = $orders->main_product_id;
            $osd->main_product_id               = $orders->main_product_id;
            $osd->main_product_comp_id          = $orders->main_product_component_id;
            $osd->main_product_comp_qty         = $orders->main_product_component_qty;
            $osd->part_number                   = $orders->part_number; 
            $osd->encoded_date                  = now();
            $osd->sequence                      = $osd->getNewSequence( $branch_id, $jsonOjb->header_id, $orders->product_id );
            $osd->save();

            $net_amount += $osd->net_amount;

            if( isset($orders->others) ){
                foreach( $orders->others as $other){ 
                    $other = (object)$other; 
                    $osd2 = new OrderSlipDetail;  
                    $osd2->orderslip_detail_id           = $osd2->getNewId();
                    $osd2->orderslip_header_id           = $jsonOjb->header_id;
                    $osd2->branch_id                     = $branch_id;
                    $osd2->remarks                       = $orders->instruction; 
                    $osd2->order_type                    = $osd2->getOrderTypeValue($orders->is_take_out);
                    $osd2->product_id                    = $other->product_id;
                    $osd2->qty                           = $other->qty;
                    $osd2->srp                           = $other->price;
                    $osd2->amount                        = $other->qty * $other->price;
                    $osd2->net_amount                    = $other->qty * $other->price;
                    $osd2->is_modify                     = 1;
                    $osd2->status                        = 'B';
                    $osd2->postmix_id                    = $other->main_product_id;
                    $osd2->main_product_id               = $other->main_product_id;
                    $osd2->main_product_comp_id          = $other->main_product_component_id;
                    $osd2->main_product_comp_qty         = $other->main_product_component_qty;
                    $osd2->part_number                   = $other->part_number;
                    $osd2->encoded_date                  = now();
                    $osd2->sequence                      = $osd->sequence;
                    $osd2->save(); 
                    $net_amount += $osd2->net_amount;

                    if( isset($other->others) ){
                        foreach( $other->others as $other2){
                            $other2 = (object)$other2;  
                            $osd3 = new OrderSlipDetail; 
                            $osd3->orderslip_detail_id           = $osd3->getNewId();
                            $osd3->orderslip_header_id           = $jsonOjb->header_id;
                            $osd3->branch_id                     = $branch_id;
                            $osd3->remarks                       = $request->instruction; 
                            $osd3->order_type                    = $osd3->getOrderTypeValue($orders->is_take_out);
                            $osd3->product_id                    = $other2->product_id;
                            $osd3->qty                           = $other2->qty;
                            $osd3->srp                           = $other2->price;
                            $osd3->amount                        = $other2->qty * $other2->price;
                            $osd3->net_amount                    = $other2->qty * $other2->price;
                            $osd3->is_modify                     = 1;
                            $osd3->status                        = 'B';
                            $osd3->postmix_id                    = $other2->main_product_id;
                            $osd3->main_product_id               = $other2->main_product_id;
                            $osd3->main_product_comp_id          = $other2->main_product_component_id;
                            $osd3->main_product_comp_qty         = $other->main_product_component_qty;
                            $osd3->part_number                   = $other2->part_number;
                            $osd3->encoded_date                  = now();
                            $osd3->sequence                      = $osd->sequence;
                            $osd3->save(); 
                            $net_amount += $osd3->net_amount;
                        }
                    }
                }
            }

            // saving none modifiable component
            if( isset($jsonOjb->none_modifiable_component) ){
                foreach( $jsonOjb->none_modifiable_component as $nmc){  
                    $_osd = new OrderSlipDetail;  
                    $_osd->orderslip_detail_id           = $_osd->getNewId();
                    $_osd->orderslip_header_id           = $jsonOjb->header_id;
                    $_osd->branch_id                     = config('settings.branch_id');
                    $_osd->remarks                       = $osd->remarks; 
                    $_osd->order_type                    = $_osd->getOrderTypeValue($orders->is_take_out);
                    $_osd->product_id                    = $nmc->product_id;
                    $_osd->qty                           = ($nmc->quantity * $osd->qty);
                    $_osd->srp                           = 0;
                    $_osd->amount                        = $_osd->qty * $_osd->srp;
                    $_osd->net_amount                    = $_osd->qty * $_osd->srp;
                    $_osd->is_modify                     = 0;
                    $_osd->status                        = 'B';
                    $_osd->postmix_id                    = $osd->product_id;
                    $_osd->main_product_id               = $osd->product_id;
                    $_osd->main_product_comp_id          = $_osd->product_id;
                    $_osd->main_product_comp_qty         = $_osd->qty;
                    $_osd->part_number                   = $nmc->product_partno;
                    $_osd->encoded_date                  = now();
                    $_osd->sequence                      = $osd->sequence;
                    $_osd->save(); 
                }
            }

            //save the total into OrderSlipHeader
            OrderSlipHeader::where('orderslip_header_id', $jsonOjb->header_id)
                ->update(['NETAMOUNT'=> $net_amount]);


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

    private function saveToKitchen(
        $ko_id,$header_id,$detail_id,
        $part_id,$comp_id,$location_id,$qty,$is_paid,$remarks){

        $helper     = new Helper;
        $ko = new KitchenOrder;
        $ko->branch_id          = config('settings.branch_id');
        $ko->ko_id              = $ko_id;
        $ko->transact_type      = 1;
        $ko->header_id          = $header_id;
        $ko->detail_id          = $detail_id;
        $ko->part_id            = $part_id;
        $ko->comp_id            = $comp_id;
        $ko->location_id        = $location_id;
        $ko->qty                = (int)$qty;
        $ko->balance            = (int)$qty;
        $ko->status             = 'P';
        
        $now = now(); 
        $ko->created_date       = $helper->getClarionDate($now);
        $ko->created_time       = $helper->getClarionTime($now);
        $ko->is_paid            = $is_paid;
        $ko->remarks            = $remarks;
        $ko->save();
        return $ko;
    }


}