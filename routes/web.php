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

Route::domain('{subdomain}.' . env('SITE_DOMAIN', 'webclass.com'))->middleware('subdomain')
    ->prefix('/admin')->name('admin.')->namespace('Admin')->group(function() {

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

    Route::middleware('assign.guard:admin,admin/login')->group(function() {
        Route::get('/home','HomeController@index')->name('home');

        Route::get('/settings','SettingsController@index')->name('settings.index');
        Route::patch('/settings','SettingsController@update')->name('settings.update');

        Route::get('/subjects','SubjectsController@index')->name('subjects.index');
        Route::post('/subjects','SubjectsController@store')->name('subjects.store');
        Route::patch('/subjects/{subject}','SubjectsController@update')->name('subjects.update');
        Route::delete('/subjects/{subject}','SubjectsController@delete')->name('subjects.delete');

        Route::post('/sections','SectionsController@store')->name('sections.store');
        Route::patch('/sections/{section}','SectionsController@update')->name('sections.update');
        Route::delete('/sections/{section}','SectionsController@delete')->name('sections.delete');

        Route::get('/classes','ClassesController@index')->name('classes.index');
        Route::post('/classes','ClassesController@store')->name('classes.store');
        Route::patch('/classes/{iclass}','ClassesController@update')->name('classes.update');
        Route::delete('/classes/{iclass}','ClassesController@delete')->name('classes.delete');
    });

});
