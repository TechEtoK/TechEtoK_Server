<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', 'WebController@index');
$app->get('/update', 'WebController@update');
$app->get('/{query}', 'WebController@detail');

$app->get('/api/test', 'ApiController@test');
$app->get('/api/search', 'ApiController@search');
$app->post('/api/add', 'ApiController@addWord');
$app->post('/api/edit', 'ApiController@editWord');
