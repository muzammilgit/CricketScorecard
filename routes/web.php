<?php

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

Route::get('/logout', function () {
    Auth::logout();
    return Redirect::to('login');
})->name('logout');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/teams', 'Teams@index')->name('teams')->middleware('auth');
Route::get('/teams/new', 'Teams@newTeam')->name('newTeam')->middleware('auth');
Route::post('/teams/new', 'Teams@newTeam')->name('newTeamForm')->middleware('auth');
Route::get('/teams/edit/{team_id}', 'Teams@editTeam')->name('editTeam')->middleware('auth');
Route::post('/teams/edit/{team_id}', 'Teams@editTeam')->name('editTeamForm')->middleware('auth');
Route::get('/teams/delete/{team_id}', 'Teams@deleteTeam')->name('deleteTeamForm')->middleware('auth');
Route::get('/getToken', function (){
    return csrf_token();
});

Route::get('/players', 'PlayersController@index')->name('players')->middleware('auth');
Route::get('/players/new', 'PlayersController@newPlayer')->name('newPlayer')->middleware('auth');
Route::post('/players/new', 'PlayersController@newPlayer')->name('newPlayerForm')->middleware('auth');
Route::get('/players/edit/{player_id}', 'PlayersController@editPlayer')->name('editPlayer')->middleware('auth');
Route::post('/players/edit/{player_id}', 'PlayersController@editPlayer')->name('editPlayerForm')->middleware('auth');
Route::get('/players/delete/{player_id}', 'PlayersController@deletePlayer')->name('deletePlayer')->middleware('auth');
Route::post('/getAllTeams', 'PlayersController@getAllTeams')->name('getAllTeams')->middleware('auth');

Route::get('/matches', 'MatchController@index')->name('matches')->middleware('auth');
Route::post('/matches/getBothTeamPlayers', 'MatchController@getBothTeamPlayers')->name('getBothTeamPlayers')->middleware('auth');
Route::get('/matches/new', 'MatchController@newMatch')->name('newmatch')->middleware('auth');
Route::get('/match/start/{match_id}', 'MatchController@startMatch')->name('startMatch')->middleware('auth');
Route::post('/matches/new', 'MatchController@newMatch')->name('newmatchPost')->middleware('auth');
Route::post('/matches/getOtherTeams', 'MatchController@getOtherTeams')->name('getOtherTeams')->middleware('auth');
Route::post('/match/{match_number}/add_over', 'MatchController@addOver')->name('addOver')->middleware('auth');
Route::post('/match/{match_number}/getPlayers', 'MatchController@getPlayers')->name('getPlayers');
Route::get('/match/{match_number}/getScorecard', 'MatchController@getScorecard')->name('getScorecard');
Route::post('/match/{match_number}/insertOver', 'MatchController@insertOver')->name('insertOver')->middleware('auth');
Route::post('/match/{match_number}/insertBall', 'MatchController@insertBall')->name('insertBall')->middleware('auth');
Route::post('/match/{match_number}/changeInnings', 'MatchController@changeInnings')->name('changeInnings')->middleware('auth');
Route::post('/match/{match_number}/matchFinish', 'MatchController@matchFinish')->name('matchFinish')->middleware('auth');
Route::post('/match/{match_number}/endMatch', 'MatchController@endMatch')->name('endMatch')->middleware('auth');
Route::post('/match/{match_number}/removeBall', 'MatchController@removeBall')->name('removeBall')->middleware('auth');
Route::post('/match/add_ball', 'MatchController@addBall')->name('addBall')->middleware('auth');
Route::get('/playing11', 'PlayersController@playing11')->name('playing11')->middleware('auth');
Route::get('/contactUs', 'ContactController@index')->name('contact')->middleware('auth');
