<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use Illuminate\Support\Facades\Mail;

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

$router->get('/mail', function () use ($router) {
    $details = [
        'title' => 'Mail from Nehal uddin shk',
        'body' => 'This is for testing email using smtp'
    ];
   
    Mail::to('nehalu23@gmail.com')->send(new \App\Mail\MyTestMail($details));
   
    dd("Email is Sent.");
});

$router->get('check', 'UserController@check');
$router->post('users/login', 'UserController@login');

$router->post('/forgotpassword', 'UserController@forgotPassword');
$router->post('/resetpassword/{token}', 'UserController@resetPassword');

$router->post('/verifyemail', 'UserController@verifyemail');
$router->post('/emailverification/{token}', 'UserController@emailVerification');

$router->group(['middleware' => 'auth'], function () use ($router) { 

    $router->group(['prefix' => 'roles'], function () use ($router)
    {
        $router->get('/', 'RoleController@view');
        $router->get('/{id}', 'RoleController@viewSpecific');
        $router->post('/add', 'RoleController@add');
        $router->post('/update/{id}', 'RoleController@update');
        $router->delete('/delete/{id}', 'RoleController@delete');
    });

    $router->group(['prefix' => 'users'], function () use ($router)
    {
        $router->get('/', 'UserController@view');
        $router->get('/{id}', 'UserController@viewSpecific');
        $router->post('/add', 'UserController@add');
        $router->post('/update/{id}', 'UserController@update');
        $router->delete('/delete/{id}', 'UserController@delete');
    });

    $router->group(['prefix' => 'posts'], function () use ($router) 
    {
        $router->get('/', 'PostController@view');
        $router->get('/{id}', 'PostController@viewSpecific');
        $router->post('/add', 'PostController@add');
        $router->post('/update/{id}', 'PostController@update');
        $router->delete('/delete/{id}', 'PostController@delete');
    });

});

