<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Library\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function username()
    {
        return 'username';
    }

    //
    public function login(Request $request){
        
        try{ 
            DB::beginTransaction();
            $helper = new Helper;  
            $uname  = $request->username;
            $upass  = $request->password;

            $token = Str::random(60);
            
            $user = User::findByUsername($uname); 
            if( is_null($user) ){
                DB::rollback();
                return response()->json([
                    'success'   => false,
                    'status'    => 401,
                    'message'   => 'Invalid Username'
                ]);
            }

            if ( $user->password != $upass ) {
                DB::rollback();
                return response()->json([
                    'success'   => false,
                    'status'    => 401,
                    'message'   => 'Invalid Password'
                ]);
            }

            // check if it is on duty
            $isOnDuty = $user->isOnDuty($helper->getClarionDate(now()));
            if( !$isOnDuty ){
                DB::rollback();
                return response()->json([
                    'success'   => false,
                    'status'    => 401,
                    'message'   => 'Not on duty'
                ]);
            }

            // create a token for this user 
            $user->api_token    = $helper->createToken($token);
            $user->update();
              
            /**
             * Committing all changes in the database
             */
            DB::commit(); 

            return response()->json([
                'success'       => true,
                'status'        => 200,
                'message'       => 'Success',
                'data'          => [
                    'name'      => $user->name,
                    'api_token' => $token,
                    'outlet'    => [
                        'id'    => $isOnDuty->storeOutlet->outlet_id,
                        'name'  => $isOnDuty->storeOutlet->description
                    ]
                ]
            ]);

        }catch( \Exception $e){
            DB::rollback();
            Log::debug($e->getMessage()); 
            return response()->json([ 
                'success'       => false,
                'status'        => 401,
                'message'       => $e->getMessage()
            ]);

        }

    }

    public function logout(Request $request){ 
        $user = Auth::user();
        $user->api_token = '';
        $user->save();
        return response()->json([
            'success'       => true,
            'status'        => 200,
            'message'       => 'Success'
        ]);
    }
}
