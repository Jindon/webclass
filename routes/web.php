<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('/superadmin')->name('superadmin.')->namespace('Superadmin')->group(function() {

        Route::namespace('Auth')->group(function(){

            //Login Routes
            Route::get('/login','LoginController@showLoginForm')->name('loginForm');
            Route::post('/login','LoginController@login')->name('login');
            Route::post('/logout','LoginController@logout')->name('logout');

            //Forgot Password Routes
            Route::get('/password/reset','ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('/password/email','ForgotPasswordController@sendResetLinkEmail')->name('password.email');

            //Reset Password Routes
            Route::get('/password/reset/{token}','ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('/password/reset','ResetPasswordController@reset')->name('password.update');

        });

    Route::middleware('assign.guard:superadmin,superadmin/login')->group(function() {
        Route::get('/home','HomeController@index')->name('home');
        Route::get('/plans','PlansController@index')->name('plans.index');
        Route::post('/plans','PlansController@store')->name('plans.store');
        Route::patch('/plans/{plan}','PlansController@update')->name('plans.update');
        Route::delete('/plans/{plan}','PlansController@delete')->name('plans.delete');

        Route::get('/institutes','InstitutesController@index')->name('institutes.index');
        Route::post('/institutes','InstitutesController@store')->name('institutes.store');
        Route::patch('/institutes/{institute}','InstitutesController@update')->name('institutes.update');
        Route::patch('/institutes/subscription/{institute}','InstitutesController@manageSubscription')->name('institutes.manageSubscription');
        Route::delete('/institutes/{institute}','InstitutesController@delete')->name('institutes.delete');
    });

});
