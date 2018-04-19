<?php
/**
 * Routes of this plugin
 */

Route::get('/', 'InstantArticlesController@index')->name('index');

Route::post('/login-callback', 'InstantArticlesController@loginCallback')->name('loginCallback');
Route::post('/save-configuration', 'InstantArticlesController@saveConfiguration')->name('saveConfiguration');
Route::get('/get-details', 'InstantArticlesController@getDetails')->name('getDetails');