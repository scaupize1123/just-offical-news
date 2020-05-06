<?php 

Route::group(['prefix' => 'api','middleware' => ['jwt.auth']], function () {
 
    Route::get('/news', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsController@showPage')->name('news.showPage');
    Route::get('/news/{uuid}', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsController@showSingle')->name('news.showSingle');
    Route::delete('/news/{uuid}', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsController@delete')->name('news.delete');
    Route::post('/news', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsController@create')->name('news.create');
    Route::put('/news/{uuid}', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsController@update')->name('news.update');
    
    Route::get('/news-categories', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsCategoryController@show');
    Route::get('/news-categories/{id}', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsCategoryController@showSingle');
    Route::post('/news-categories', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsCategoryController@create');
    Route::put('/news-categories/{id}', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsCategoryController@update');
    Route::delete('/news-categories/{id}', 'Scaupize1123\JustOfficalNews\Controllers\Api\NewsCategoryController@delete');        
});