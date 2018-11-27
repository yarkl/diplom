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

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});

$router->get('/json/{id}', 'GraphController@json');
$router->get('/show/{id}', 'GraphController@show');
$router->get('/tags', 'TagsController@index');
$router->get('/course-tags', 'CourseController@tags');
$router->get('/graph', 'GraphController@graph');
$router->get('/about', 'AboutProjectController@index');
$router->get('/course-show/{id}', 'CourseController@show');
$router->get('/course-json/{id}', 'CourseController@json');
$router->get('/course-graph', 'CourseController@graph');
$router->get('/course', 'CourseController@index');
$router->get('/restore', 'GraphController@restore');
$router->get('/', 'HomeController@home');
