<?php
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, token, grant-type, X-Auth-Token, Origin, Authorization');

use App\Library\Helper;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// }); 

Route::get('/get_clarion_date', function(){
    $h = new Helper; 
    echo $h->getClarionDate( now() );
});

Route::namespace('Api\V1')->group(function () {

    // PUBLIC
    Route::post('/login',   'LoginController@login');
    // AUTHORIZED
    Route::middleware('auth:api')->group(function () { 
        Route::middleware('is_on_duty')->group(function(){ 
            Route::post('/outlet/category',                 'PartLocationController@groups');
            Route::post('/outlet/category/sub-category',    'PartLocationController@category');
            Route::post('/outlet/products',                 'PartLocationController@byGroupAndCategory');
    
        }); 
    });

}); 