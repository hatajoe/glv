<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('/', 'Glv\Controller\ApiController@index');
Route::get('/milestones', 'Glv\Controller\ApiController@milestones');
Route::get('/issues/{projectId}', 'Glv\Controller\ApiController@issues');
