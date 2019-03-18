<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserSite;
use App\Model\Category;
use App\Library\Helper;
use DB;
use App\Model\PartLocation;
use App\Model\SitePart;
use Illuminate\Support\Facades\Log;

class PartLocationController extends Controller
{
    private $grant_type;
    private $token;
    private $helper;
    private $current_duty;

    public function __construct(Helper $helper, Request $request, UserSite $user_site)
    {
        $this->grant_type     = $request->header('grant-type');
        $this->token          = $request->header('token');
        $this->helper         = new Helper;

        if( isset($this->token)  ){
            $user                   = $user_site::findByToken($this->token);
            $this->current_duty     = $user->isOnDuty($this->helper->getClarionDate(now())); 
        }
    }

    //
    public function groups(Request $request){   
        /**
         * GET PRODUCT BELONGS TO OUTLET
         */
        $pl = PartLocation::with('group')
                ->where('outlet_id', $this->current_duty->outlet)->get();

        $val = config('custom.group_not_to_display');
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
            'result'    => $result
        ]);
    }

    public function byGroupAndCategory(Request $request){
        try{

            $result = PartLocation::where('outlet_id', $this->current_duty->outlet)
                ->where('group_id', $request->group_id)
                ->where('category_id', $request->category_id)
                ->simplePaginate(15);

            $result->getCollection() 
                ->transform(function ($value) { 
                //$url = Storage::url($value->IMAGE);
                $parts_type = SitePart::getPartsTypeById($value->product_id);
                $kitchen_loc = SitePart::getKitchenLocationById($value->product_id);
                
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
                    'image'         => '', 
                    'is_food'       => $value->is_food,
                    'is_postmix'    => $value->postmix,
                    'parts_type'    => $parts_type,
                    'kitchen_loc'   => $kitchen_loc
                ];
            });

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'result'    => [
                    'data'  => $result
                ]
            ]);

        }catch(\Exception $e){
            Log::error($e->getMessage());
        }
    }

}
