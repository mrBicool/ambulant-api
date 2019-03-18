<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\Model\UserSite;
use Illuminate\Support\Facades\DB;
use App\Library\Helper;

class LoginController extends Controller
{
    //
    public function login(Request $request){

        try{
            DB::beginTransaction();
            $helper = new Helper;

            $grant_type     = $request->grant_type;
            $username       = $request->username;
            $password       = $request->password;

            if($grant_type  == null || $grant_type == ''){
                return response()->json([
                    'success'       => false,
                    'status'        => '',
                    'message'       => 'Grant Type is missing'
                ]);
            }
            
            if($grant_type == 'ambulant'){ 

                if( gettype($username) != 'integer' ){
                    DB::rollback();
                    return response()->json([
                        'success'   => false,
                        'status'    => 3,
                        'message'   => 'Username must be Integer'
                    ]);
                }
               
                $user = UserSite::findByUsername($username);

                if( is_null($user) ){
                    DB::rollback();
                    return response()->json([
                        'success'   => false,
                        'status'    => 3,
                        'message'   => 'Invalid Username'
                    ]);
                }

                if ( $user->password != $request->password ) {
                    DB::rollback();
                    return response()->json([
                        'success'   => false,
                        'status'    => 3,
                        'message'   => 'Invalid Password'
                    ]);
                }

                // check if it is on duty
                $isOnDuty = $user->isOnDuty($helper->getClarionDate(now()));
                if( !$isOnDuty ){
                    DB::rollback();
                    return response()->json([
                        'success'   => false,
                        'status'    => 3,
                        'message'   => 'Not on duty'
                    ]);
                }

                // create a token for this user
                $user->token        = $helper->createToken($username);
                $user->update();

                /**
                 * Committing all changes in the database
                 */
                DB::commit();

                return response()->json([
                    'success'       => true,
                    'status'        => 1,
                    'message'       => 'Success',
                    'data'          => [
                        'name'      => $user->name,
                        'token'     => $user->token,
                        'outlet'    => [
                            'id'    => $isOnDuty->storeOutlet->outlet_id,
                            'name'  => $isOnDuty->storeOutlet->description
                        ]
                    ]
                ]);
            }

            return response()->json([
                'success'       => true,
                'status'        => 1,
                'message'       => 'Grant Type is not available'
            ]);

        }catch(\Exception $e){
            DB::rollback();
            Log::debug($e->getMessage());
            return response()->json([
                'success'       => false,
                'status'        => 4,
                'message'       => $e->getMessage()
            ]);
        }
        
    }
}
