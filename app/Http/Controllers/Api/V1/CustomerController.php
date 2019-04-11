<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Customer;
use App\Http\Resources\Customer as CustomerResource;
use App\Model\WebUser;
use Carbon\Carbon;
use DB;

class CustomerController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //
        try{
            DB::beginTransaction();

            if( WebUser::findByMobile($request->mnumber) ){
                DB::rollback();
                return response()->json([
                    'success'   => false,
                    'status'    => 400,
                    'message'   => 'Mobile number is used.'
                ]);
            }
            
            $web_user   =   new WebUser;
            $customer   =   new Customer; 

            $new_web_user_id    = $web_user->getNewId(); 
            
            $new_customer_id    = $customer->getNewId();
            //dd('test', $new_customer_id);
            
            // create new web user
            $web_user->id               = $new_web_user_id;
            $web_user->name             = $request->name;
            $web_user->mobile_number    = $request->mnumber;
            $web_user->password         = md5($request->mnumber);
            $web_user->save();

            // create new customer
            $customer->branch_id        = config('custom.branch_id');
            $customer->customer_id      = $new_customer_id;
            $customer->customer_code    = $new_customer_id;
            $customer->user_id          = $new_web_user_id;
            $customer->name             = $request->name;
            $customer->mobile_number    = $request->mnumber;
            $customer->birthdate        = $request->bdate;
            $customer->is_loyalty       = 1;
            $customer->wallet           = 0;
            $customer->points           = 0;
            $customer->save();

            /**
             * Committing all changes in the database
             */
            DB::commit();

            return response()->json([
                'success'       => true,
                'status'        => 200, 
                'message'       => 'success'
            ]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'success'   => false,
                'status'    => 400,
                'message'   => 'Server Error',
                'detail'    => $e->getMessage()
            ]);
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Searching of customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request){ 

        $customer = new Customer;

        if($request->search_by == 'mobile_number'){ 
            $result     = $customer->findByMobile($request->mobile_number); 
            if( is_null($result) ){
                return response()->json([
                    'success'   => false,
                    'status'    => 422,
                    'message'   => 'Customer not found'
                ]);
            }

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => 'success',
                'result'    => new CustomerResource($result)
            ]); 
        }
    }
}
