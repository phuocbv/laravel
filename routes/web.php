<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('admin.order.index'));
});

Route::group(['prefix'=>'cmanager', 'as'=>'admin.',  'middleware'=>'auth',  'namespace'=>'Admin'], function(){

    Route::get('/', 'DashboardController@index')->name('index');
    Route::post('/active', 'DashboardController@postActive')->name('active');
    Route::post('/paid', 'DashboardController@postPaid')->name('paid');
    Route::get('/setting', 'DashboardController@getSetting')->name('getsetting');
    Route::post('/setting', 'DashboardController@postSetting')->name('postsetting');



    Route::get('/order', 'OrderController@index')->name('order.index');
    Route::get('/order/create', 'OrderController@create')->name('order.create');
    Route::post('/order/store', 'OrderController@store')->name('order.store');
    Route::get('/order/{id}/edit', 'OrderController@edit')->name('order.edit');
    Route::put('/order/{id}', 'OrderController@update')->name('order.update');
    Route::delete('/order/destroy', 'OrderController@destroy')->name('order.destroy');
    Route::post('/order/loadorders', 'OrderController@loadOrders')->name('order.loadorders');

    Route::post('/order/changetrack', 'OrderController@changeTrack')->name('order.changetrack');
    Route::post('/order/checkonetrack', 'OrderController@checkOneTrack')->name('order.checkonetrack');
    Route::post('/order/checktracksession', 'OrderController@checkTrackSession')->name('order.checktracksession');
    Route::post('/order/checktrackcron', 'OrderController@checkTrackCron')->name('order.checktrackcron');
    Route::post('/order/addtrackwq', 'OrderController@addTrackingWithQty')->name('order.addtrackwq');

    Route::post('/order/changenote', 'OrderController@changeNotes')->name('order.changenotes');
    Route::post('/order/changestatus', 'OrderController@changeStatus')->name('order.changestatus');
    Route::post('/order/assignorder', 'OrderController@assignOrder')->name('order.assignorder');
    Route::post('/order/assignall', 'OrderController@assignAll')->name('order.assignall');
    Route::post('/order/deleteall', 'OrderController@deleteAll')->name('order.deleteall');
    Route::post('/order/payall', 'OrderController@payAll')->name('order.payall');
    Route::post('/order/uploadsheet', 'OrderController@uploadSheet')->name('order.uploadsheet');

    Route::post('/order/sheetall', 'OrderController@sheetAll')->name('order.sheetall');
    Route::post('/order/getsheettrack', 'OrderController@getSheetTrack')->name('order.getsheettrack');
    Route::post('/order/checksheetsession', 'OrderController@checkSheetSession')->name('order.checksheetsession');
    Route::post('/order/checksheetcron', 'OrderController@checkSheetCron')->name('order.checksheetcron');



    Route::get('/member', 'MemberController@index')->name('member');
    Route::get('/member/create', 'MemberController@getCreate')->name('member.create');
    Route::post('/member/create', 'MemberController@postCreate')->name('member.create');
    Route::get('/member/{id}/edit', 'MemberController@getEdit')->name('member.edit');
    Route::put('/member/{id}', 'MemberController@update')->name('member.update');

    Route::get('/email', 'EmailController@index')->name('email');
    Route::get('/email/create', 'EmailController@getCreate')->name('email.create');
    Route::post('/email/create', 'EmailController@postCreate')->name('email.create');
    Route::get('/email/{id}/edit', 'EmailController@getEdit')->name('email.edit');
    Route::post('/email/edit', 'EmailController@postEdit')->name('email.update');
    Route::post('/email/delete', 'EmailController@postDelete')->name('email.delete');

    Route::post('/email/grantcode', 'EmailController@postGrandCode')->name('email.grantcode');
    Route::post('/email/usertoken', 'EmailController@postUserToken')->name('email.usertoken');

    Route::get('users', function () {
        echo 'hello user';

        dd(route('admin.users'));
    })->name('users');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
