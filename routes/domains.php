<?php

use Illuminate\Support\Facades\Route;


Route::get('/', 'HomeController@index')->name('domains');
Route::get('/domains/create', 'HomeController@create')->name('domains.create');
Route::post('/domains/insert', 'HomeController@insert')->name('domains.insert');
Route::get('/domains/{domain}/delete', 'HomeController@delete')->name('domains.delete');

Route::get('/domainstoignore', 'HomeController@domainsToIgnoreList')->name('domainsToIgnore');
Route::post('/domainstoignore/crete', 'HomeController@domainsToIgnoreCreate')->name('domainsToIgnore.create');
Route::get('/domainstoignore/delete/{id}', 'HomeController@domainsToIgnoreDelete');

Route::get('/details/{domain}', 'HomeController@details')->name('details');
Route::get('/details/status/{domain}/{status}', 'HomeController@domainChangeStatus')->name('domain.change.status');

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/domainstocheck', 'HomeController@domainsToCheck')->name('domains.to.check');
Route::get('/domainstocheck-restart', 'HomeController@domainsToCheckRestart')->name('domains.to.check.restart');
Route::get('/domainsavalible', 'HomeController@domainsToCheckAvalibe')->name('domains.to.check.avalile');
Route::get('/list/free', 'HomeController@domainsFree')->name('domains.to.free');
Route::post('/domainstocheck/create', 'HomeController@domainsToCheckUpload')->name('domains.to.create');

Route::get('/free/{domain}', 'HomeController@free')->name('free');
Route::get('/priotiry/{id}', 'HomeController@priotiry')->name('priority');

Route::post('/domain/delete/{id}', 'Controller@deleteDomain')->name('domain.delete');

Route::post('/domain/checked', 'HomeController@checkedUpdateStatus')->name('domain.checked');
Route::post('/domain/{id}/update-tag', 'HomeController@updateDomainTag')->name('domain.update.tag');

