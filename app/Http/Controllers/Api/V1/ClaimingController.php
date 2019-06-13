<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Model\SalesOrderDetail;
use App\Model\PartLocation;
use App\Http\Resources\PostmixCollection;
use Illuminate\Support\Facades\DB;
use App\Library\Helper;
use App\Model\KitchenOrder;
use App\Model\SitePart;
use App\Services\BranchLastIssuedNumberServices as BLIN;

class ClaimingController extends Controller
{
    //
    public function checkCode(Request $request){
        
        $branch_id  = config('settings.branch_id');

        $result = SalesOrderDetail::where('barcode',$request->code)
            ->where('branch_id', $branch_id)
            ->first(); 
        
        if(!$result){
            return response()->json([
                'success'   => false,
                'status'    => 404,
                'message'   => 'Code not found!'
            ]);
        }

        // check if this product is in this outlet
        $pl = PartLocation::byProductAndOutlet(
            $result->sitepart_id,
            $request->outlet_id);
          
        if(!$pl){
            return response()->json([
                'success'   => false,
                'status'    => 404,
                'message'   => 'Not available in this outlet!'
            ]);
        }
        
        // check if food
        if($result->is_food != 1){
            return response()->json([
                'success'   => false,
                'status'    => 404,
                'message'   => 'Item is not food!'
            ]);
        }

        // check if has qty remaining
        if($result->qty_remaining <= 0 ){
            return response()->json([
                'success'   => false,
                'status'    => 404,
                'message'   => 'Item is already claim!'
            ]);
        }
        


        // construct a object with component
        $product = [
            'product'       => $result,
            'components'    => new PostmixCollection($pl->postmixComponents()->get())
        ];
        
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'message'   => 'Success',
            'data'      => $product
        ]);
    }

    public function claim(Request $request){  
        try{

            // begin transaction
            DB::beginTransaction();

            $branch_id  = config('settings.branch_id');
            $blin       = new BLIN($branch_id);

            if($request->tag == 'food'){ 
                // code: "51-29"
                // product_id: "29"
                // qty: "1"
                // outlet_id: "3"

                // check if this product is in this outlet
                $pl = PartLocation::byProductAndOutlet(
                    $request->product_id,
                    $request->outlet_id
                );
                
                $components = $pl->postmixComponents()->get(); 

                if(!$pl){
                    return response()->json([
                        'success'   => false,
                        'status'    => 404,
                        'message'   => 'Not available in this outlet!'
                    ]);
                } 

                // check if this item is has been claimed.
                $sod = SalesOrderDetail::where('barcode',$request->code)
                        ->where('branch_id', $branch_id)
                        ->first();
                
                if($sod->qty_remaining <= 0 ){
                    return response()->json([
                        'success'   => false,
                        'status'    => 404,
                        'message'   => 'Item is already claim!'
                    ]);
                }

                // check if this item is in the kitchen
                $is_in_the_kitchen = KitchenOrder::findByBranchAndCode($branch_id, $request->code);
                if($is_in_the_kitchen){
                    return response()->json([
                        'success'   => false,
                        'status'    => 404,
                        'message'   => 'This item is already in the kitchen!'
                    ]);
                }

                $sp = SitePart::findByIdAndBranch($request->product_id, $branch_id); 

                // if this item belongs to kitchen save it
                if( strtolower($sp->parts_type) == 'y'){ 
                    $this->saveToKitchen(
                        $blin->getNewIdForKitchenOrder(),
                        0,
                        0,
                        $request->product_id,
                        $request->product_id,
                        $sp->kitchen_loc,
                        $request->qty,
                        1,
                        '',
                        2, // order type
                        $request->code
                    ); 
                }

                foreach($components as $component){
                    $sp2 = SitePart::findByIdAndBranch($component->product_id, $branch_id);  
                    if( strtolower($sp2->parts_type) == 'y'){ 
                        $this->saveToKitchen(
                            $blin->getNewIdForKitchenOrder(),
                            0,
                            0,
                            $component->product_id,
                            $request->product_id,
                            $sp2->kitchen_loc,
                            $component->quantity * $request->qty,
                            1,
                            '',
                            2, // order type,
                            $request->code
                        ); 
                    }
                }  

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

    private function saveToKitchen(
        $ko_id,$header_id,$detail_id,
        $part_id,$comp_id,$location_id,$qty,$is_paid,$remarks,$order_type,$code){

        $helper     = new Helper;
        $ko = new KitchenOrder;
        $ko->branch_id          = config('settings.branch_id');
        $ko->ko_id              = $ko_id;
        $ko->origin             = 2;
        $ko->order_type         = $order_type;
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
        $ko->barcode            = $code;
        $ko->save();
        return $ko;
    }
}
