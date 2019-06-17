<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserSite;
use App\Model\Category;
use App\Library\Helper;
use DB;

use App\Model\PartLocation;
use App\Http\Resources\PartLocation as PartLocationResource;
use App\Http\Resources\PartLocationCollection;
use App\Http\Resources\Postmix as PostmixResource;
use App\Http\Resources\PostmixCollection;

use App\Model\SitePart;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Storage;

class PartLocationController extends Controller
{ 
    private $helper;  

    public function __construct(Helper $helper)
    { 
        $this->helper         = new Helper; 
    }

    //
    public function groups(Request $request){

        $user = Auth::user();  
         
        /**
         * GET PRODUCT BELONGS TO OUTLET
         */ 
        $pl = PartLocation::with('group')
                ->where('outlet_id', 
                $user->current_outlet(
                    $this->helper->getClarionDate(now())
                )
            )->get();

        $val = config('settings.group_not_to_display');
        $val = explode(',',$val);

        $groups = $pl->unique('group')
            ->whereNotIn('group.group_id',$val)
            ->transform(function ($value) {  
            return [
                'group_id'      => $value->group_id,
                'description'   => $value->group->description
            ];  
        }); 

        /**
         * GET DISTINCT CATEGORY
         */
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'data'      => $groups
        ]); 
    }

    public function category(Request $request){
        $group_id   = $request->group_id; 
        $result     = Category::getByGroupId($group_id); 
        $result->transform( function($v){
            return [
                'category_id'       => $v->category_id,
                'description'       => $v->description
            ];
        });  
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'data'      => $result
        ]);
    }

    public function byGroupAndCategory(Request $request){
        try{
            $user = Auth::user(); 

            $result = PartLocation::where('outlet_id', 
                    $user->current_outlet(
                        $this->helper->getClarionDate(now())
                    )
                )
                ->where('group_id', $request->group_id)
                ->where('category_id', $request->category_id)
                ->simplePaginate(15);

            $result->getCollection() 
                ->transform(function ($value) { 
                
                $sitepart = SitePart::where('branch_id', config('settings.branch_id'))
                            ->where('sitepart_id',$value->product_id )
                            ->first();

                $parts_type = SitePart::getPartsTypeById($value->product_id);
                $kitchen_loc = SitePart::getKitchenLocationById($value->product_id);
                
                /**
                 * working on this shit
                 */
                $url = Storage::url($sitepart->part->img_url);
                return [
                    'product_id'    => $value->product_id,
                    'outlet_id'     => $value->outlet_id, 
                    // 'description'   => $value->description,
                    'description'   => $value->short_code,
                    'srp'           => $value->retail,
                    'category_id'   => $value->category,
                    'group'         => [
                        'group_code'    => $value->group->group_id,
                        'description'   => $value->group->description
                    ],  
                    'img_path'      => $url, 
                    'is_food'       => $value->is_food,
                    'is_postmix'    => $value->postmix,
                    'parts_type'    => $parts_type,
                    'kitchen_loc'   => $kitchen_loc
                ];
            });

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'result'    => $result,
                'base_url'  => url('/')
            ]);

        }catch(\Exception $e){
            return response()->json([
                'success'   => false,
                'status'    => 500,
                'data'      => $e->getMessage()
            ]);
            Log::error($e->getMessage());
        }
    }

    public function productByOutlet(Request $request){
        $product_id = $request->product_id;
        $outlet_id  = $request->outlet_id;
         

        $pl = PartLocation::byProductAndOutlet($product_id,$outlet_id);

        $result = new PartLocationResource($pl);
        
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'result'    => $result,
            'base_url'  => url('/')
        ]);
    }

    public function productComponents(Request $request){
        $product_id = $request->product_id;
        $outlet_id  = $request->outlet_id;  

        $pl = PartLocation::byProductAndOutlet($product_id,$outlet_id);  
        if( $request->group_by == 'mc' ){ // modifiable component
            $pl = $pl->postmixModifiableComponents()->paginate();  
        }else if( $request->group_by == 'nmc'){ // non modifiable component
            $pl = $pl->postmixNoneModifiableComponents()->paginate(); 
        }else{
            return response()->json([
                'success'   => false,
                'status'    => 400,
                'message'   => 'No Group has been set'
            ]);
        }

        $result = new PostmixCollection($pl);  
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'result'    => $result
        ]);
    }

    public function nonModifiable(Request $request){
        $product_id = $request->product_id;
        $outlet_id  = $request->outlet_id; 

        $pl = PartLocation::byProductAndOutlet($product_id,$outlet_id); 
        $pl = $pl->postmixNoneModifiableComponents()->paginate();  
        $result = new PostmixCollection($pl); 

        return response()->json([
            'success'   => true,
            'status'    => 200,
            'result'    => $result
        ]);
    }



    public function productByCategory(Request $request){ 

        $product_id = $request->product_id;
        $outlet_id  = $request->outlet_id; 
        
        $pl     = PartLocation::where('product_id',$product_id)
                    ->where('outlet_id', $outlet_id)
                    ->first();
         
        $pls    = PartLocation::byCategoryOfProductPerOutlet(
                        $pl->category_id,
                        $pl->product_id,
                        $outlet_id
                    ); 
        
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'result'    => [
                'product'       => new PartLocationResource($pl),
                'categories'    => new PartLocationCollection($pls)
            ]
        ]); 
    }

}
