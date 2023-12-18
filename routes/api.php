<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1/admin'], function(){
    Route::post('login', 'AuthController@adminLogin');

    // admin endpoints
    Route::middleware(['auth:sanctum', 'admin.auth.token'])->group(function () {
        // Match Related Routes
        Route::post('addMatch', 'Admin\AdminController@addMatch')->name('addMatch');
        Route::post('updateMatch', 'Admin\AdminController@updateMatch')->name('updateMatch');
        Route::post('updateMatchAstroStatus', 'Admin\AdminController@updateMatchAstroStatus')->name('updateMatchAstroStatus');
        Route::post('updateSeries', 'Admin\AdminController@updateSeries')->name('updateSeries');
        Route::get('seriesList', 'Admin\MatchController@seriesList');
        Route::get('upcomingMatches', 'Admin\MatchController@upcomingList');
        Route::get('recentMatches', 'Admin\MatchController@recentList');
        Route::get('liveMatches', 'Admin\MatchController@liveList');
        Route::post('saveMatchKundli', 'Admin\MatchController@saveMatchKundli')->name('saveMatchKundli');
        Route::post('matchInfo', 'Admin\MatchController@matchInfo')->name('matchInfo');
        Route::post('cupRates', 'Admin\AdminController@updateCupRates')->name('updateCupRates');
        Route::get('getAllCupRates', 'Admin\AdminController@getAllCupRates')->name('getAllCupRates');
        Route::get('getAllSettings', 'Admin\AdminController@getAllSettings')->name('getAllSettings');
        Route::post('updateSettings', 'Admin\AdminController@updateSettings')->name('updateSettings');
        Route::post('sendNotification', 'Admin\AdminController@sendNotification')->name('sendNotification');
        
        // Private ads routes
        Route::get('getAllPrivateAds', 'Admin\PrivateAdsController@getList')->name('getAllPrivateAds');
        Route::get('privateAd/{id}', 'Admin\PrivateAdsController@getAd')->name('getAd');
        Route::post('privateAd', 'Admin\PrivateAdsController@addNewAd')->name('adPrivateAd');
        Route::delete('privateAd/{id}', 'Admin\PrivateAdsController@deleteAd')->name('deletePrivateAd');
        Route::post('privateAd/{id}', 'Admin\PrivateAdsController@updateAd')->name('updatePrivateAd');

        // Reviews routes
        Route::get('getAllReviews', 'Admin\AdminController@getAllReviews')->name('getAllReviews');
        Route::get('review/{id}', 'Admin\AdminController@getReview')->name('getReview');
        Route::post('review', 'Admin\AdminController@addReview')->name('addReview');
        Route::delete('review/{id}', 'Admin\AdminController@deleteReview')->name('deleteReview');
        Route::post('review/{id}', 'Admin\AdminController@updateReview')->name('updateReview');

        // Players routes
        Route::get('getPlayersList', 'Admin\PlayersController@getPlayersList');
        Route::post('savePlayer', 'Admin\PlayersController@savePlayer');
        Route::get('getPlayer/{id}', 'Admin\PlayersController@getPlayer');
        Route::delete('deletePlayer/{id}', 'Admin\PlayersController@deletePlayer');

        // Kundli routes
        
        // Astrology routes
        Route::get('fetchUniqueYearsAndMonths', 'Admin\AstrologyController@fetchUniqueYearsAndMonths');
        Route::post('uploadAstrology', 'Admin\AstrologyController@uploadAstrology');
        Route::post('uploadEditedAstrology', 'Admin\AstrologyController@uploadEditedAstrology');
        Route::post('uploadMatchAstrology', 'Admin\AstrologyController@uploadMatchAstrology');
        Route::get('fetchByPanditAndMatch', 'Admin\AstrologyController@fetchByPanditAndMatch');
        
        //Pandit routes
        Route::prefix('pandits')->group(function () {
            Route::post('create', 'Admin\PanditController@create'); 
            Route::put('update/{id}', 'Admin\PanditController@update'); 
            Route::delete('delete/{id}', 'Admin\PanditController@delete'); 
            Route::get('getPanditById/{id}', 'Admin\PanditController@getPanditById');
            Route::get('getAllPandits', 'Admin\PanditController@getAllPandits'); 
        });
        
        // Users routes
        Route::post('users/create-token/{user_id}', 'UserController@createToken');
        Route::resource('users', 'UserController');        
        Route::get('getUserDetails/{id}', 'UserController@getUserDetails');
    });
});


Route::group(['prefix' => 'v1/cricketpanditji'], function(){
    // Before login endpoint (open endpoints)
    Route::post('sign-in', 'AuthController@signIn')->name('signIn');
    Route::post('sign-up', 'AuthController@signUp')->name('signUp');
    Route::post('forget-password', 'AuthController@sendFPLink')->name('sendFPLink');
    Route::post('reset-password', 'AuthController@resetPassword')->name('resetPassword');
    Route::get('allMatches', 'MatchController@allMatches')->name('allMatches');
    Route::post('fetchSeriesData', 'SeriesController@fetchSeriesData');
    Route::get('getAllReviews', 'UserController@getAllReviews')->name('getAllReviews');
    Route::get('getAllPrivateAds', 'PrivateAdsController@getAllPrivateAds')->name('getAllPrivateAds');
    
    // After login endpoints
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('user', 'UserController@getUser');
        Route::get('seriesList', 'SeriesController@getList');
        Route::get('pandits', 'PanditController@getAllPandits');
        Route::get('upcomingMatches', 'MatchController@getUpcomingList')->name('upcomingMatches');
        Route::get('recentMatches', 'MatchController@getRecentList')->name('recentMatches');
        Route::get('liveMatches', 'MatchController@getLiveList')->name('liveMatches');
        Route::get('dashboardMatches', 'MatchController@dashboardList')->name('dashboardMatches');
        Route::post('matchesBySeriesId', 'MatchController@getList');
        Route::post('scorecardByMatchId', 'MatchController@scorecard')->name('scorecard');
        Route::post('matchOddHistory', 'MatchController@getOddHistory')->name('matchOddHistory');
        Route::post('matchInfo', 'MatchController@matchInfo')->name('matchInfo');
        Route::post('squadByMatchId', 'MatchController@squadByMatchId')->name('squadByMatchId');
        Route::post('commentary', 'MatchController@commentary')->name('commentary');
        Route::get('news', 'NewsController@getNews')->name('news');
        Route::post('news', 'NewsController@getNewsDetail')->name('news.post');
        Route::post('pointsTable', 'SeriesController@getPointsTable')->name('pointsTable');
        Route::get('cupRates', 'CupRateController@cupRates')->name('cupRates');
        Route::get('me', 'AuthController@me');
        Route::get('/test', function () {
            return response('Test API', 200)->header('Content-Type', 'application/json');
        });
        
        Route::post('sendMessage', 'ContactController@sendMessage');
        
        // Payment routes
        Route::post('razorpay/create-order', 'PaymentController@createOrder');
        Route::post('razorpay/capture-payment', 'PaymentController@capturePayment');
        Route::post('webhook/razorpay', 'PaymentController@handleWebhook');

        
    });

    Route::get('/test', function () {
        return response('Test API', 200)->header('Content-Type', 'application/json');
    });
});


Route::group(['prefix' => 'v1/webservices'], function(){
    Route::middleware(['auth:sanctum', 'api.auth.token'])->group(function () {

        Route::get('series-list', 'SeriesController@getList');
        Route::get('upcoming-matches', 'MatchController@getUpcomingList')->name('upcomingMatches');
        Route::get('recent-matches', 'MatchController@getRecentList')->name('recentMatches');
        Route::get('live-matches', 'MatchController@getLiveList')->name('liveMatches');
        Route::get('dashboard-matches', 'MatchController@dashboardList')->name('dashboardMatches');
        Route::post('matches-by-series-id', 'MatchController@getList');
        Route::post('scorecard-by-match-id', 'MatchController@scorecard')->name('scorecard');
        Route::post('match-odd-history', 'MatchController@getOddHistory')->name('matchOddHistory');
        Route::post('match-info', 'MatchController@matchInfo')->name('matchInfo');
        Route::post('squad-by-match-id', 'MatchController@squadByMatchId')->name('squadByMatchId');
        Route::post('commentary', 'MatchController@commentary')->name('commentary');

        Route::get('news', 'NewsController@getNews')->name('news');
        Route::post('news', 'NewsController@getNewsDetail')->name('news.post');
        
        Route::post('points-table', 'SeriesController@getPointsTable')->name('pointsTable');
        Route::get('cup-rates', 'CupRateController@cupRates')->name('cupRates');

        Route::get('/test', function () {
            return response('Success', 200)->header('Content-Type', 'application/json');
        });
    });
});
