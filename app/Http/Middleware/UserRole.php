<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\UserSite;
use App\Library\Helper;

class UserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ... $roles)
    {    
        $grant_type = $request->header('grant-type');
        $token      = $request->header('token');
        $helper     = new Helper;

        if( !isset($grant_type) || !isset($token)){
            return response()->json([
                'success'   => false,
                'status'    => 2,
                'message'   => 'Invalid Request'
            ]);
        }

        foreach ($roles as $role) { 
            if($role == $grant_type && $grant_type == 'ambulant'){ // Auth for ambulant
                $result = UserSite::findByToken( $token );
                if($result){

                    if( !$result->isOnDuty($helper->getClarionDate(now())) ){
                        return response()->json([
                            'success'   => false,
                            'status'    => 3,
                            'message'   => 'Not on duty'
                        ]);
                    }

                    return $next($request);
                }
            }
        }
        
        return response()->json([
            'success'   => false,
            'status'    => 3,
            'message'   => 'Unauthorized Access'
        ]);  
    }
}
