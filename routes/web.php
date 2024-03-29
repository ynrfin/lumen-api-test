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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => '/api/v1', 'middleware' => 'auth'], function() use($router){
    $router->group(['prefix' => 'checklists'], function() use ($router){
        $router->get('/', ['as' => 'checklists.showAll', 'uses' => "ChecklistController@showAll"]);
        $router->get('/{id}', ['as' => 'checklists.showOne', 'uses' => "ChecklistController@showOne"]);
        $router->patch("/{id}", ['as' => 'checklists.patch', 'uses' => 'ChecklistController@patch']);
        $router->delete("/{id}", ['as' => 'checklists.patch', 'uses' => 'ChecklistController@delete']);
        $router->post("/", ['as' => 'checklists.create', 'uses' => 'ChecklistController@create']);
        $router->post("/{id}/items", ['as' => 'checklists.item.create', 'uses' => 'ItemController@create']);
        $router->get("/{checklistId}/items/{itemId}", ['as' => 'checklists.item.showOne', 'uses' => 'ItemController@showOne']);
        $router->delete("/{checklistId}/items/{itemId}", ['as' => 'checklists.item.delete', 'uses' => 'ItemController@delete']);
        $router->post("/complete", ['as' => 'checklists.item.complete', 'uses' => 'ItemController@complete']);
        $router->post("/incomplete", ['as' => 'checklists.item.incomplete', 'uses' => 'ItemController@incomplete']);
        $router->get("/items", ['as' => 'checklists.item.showAll', 'uses' => 'ItemController@showAll']);
    });

});
