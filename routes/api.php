<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventtypeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FacilityController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/getEvents',[EventController::class, 'index']);
Route::get('/getPackages',[EventController::class, 'packages']);
Route::post('/updateDeposit/{id}',[EventController::class, 'deposit']);


Route::get('/getPayments',[PaymentController::class, 'index']);
Route::post('/receiptForm/{id}',[PaymentController::class, 'receipt']);

Route::get('/getEventtypes',[EventtypeController::class,'index']);


Route::get('/getFacilities',[FacilityController::class,'index']);
Route::get('/getFacilities/{id}',[FacilityController::class,'show']);
Route::post('/storeFacility',[FacilityController::class,'store']);
Route::post('/eventfacility/{id}',[FacilityController::class,'Link']);
Route::post('/deletelink/{id}',[FacilityController::class,'deleteLink']);




// Route::get('/getBooks/{id}',[BookingController::class, 'show']);
// Route::post('/storeBook',[BookingController::class, 'store']);
// Route::post('/updateBook/{id}',[BookingController::class, 'update']);
// Route::post('deleteBook/{id}',[BookingController::class, 'destroy']);
// Route::post('confirmBook/{id}',[BookingController::class, 'confirm']);
// Route::post('unconfirmBook/{id}',[BookingController::class, 'unconfirm']);
// Route::post('/reportBooking',[BookingController::class, 'report']);