<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Library\Helper;

class CheckIfIsOnDuty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $helper = new Helper;
        
        if( !Auth::user()->isOnDuty($helper->getClarionDate(now())) ){
            return response()->json([
                'success'   => false,
                'status'    => 401,
                'message'   => 'Off Duty!'
            ]);
        }

        return $next($request);
    }
}
