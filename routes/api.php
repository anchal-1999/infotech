<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\managementController;
use App\Http\Controllers\questionController;
use App\Http\Controllers\surveyController;
use App\Http\Controllers\staffController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//user controller
Route::post('userRegistration',[userController::class, 'userRegistration']);
Route::post('user_login',[userController::class, 'user_login']);

//management controller
Route::post('add_user',[managementController::class, 'add_user']);
Route::post('update_user',[managementController::class, 'update_user']);
Route::post('fetch_all_user',[managementController::class, 'fetch_all_user']);
Route::post('fetch_user_byid',[managementController::class, 'fetch_user_byid']);
Route::post('delete_user',[managementController::class, 'delete_user']);

//question controller
Route::post('add_question',[questionController::class, 'add_question']);
Route::post('update_question',[questionController::class, 'update_question']);
Route::get('fetch_all_question',[questionController::class, 'fetch_all_question']);
Route::get('fetch_question_byid',[questionController::class, 'fetch_question_byid']);
Route::post('delete_question',[questionController::class, 'delete_question']);


//survey controller
Route::post('add_survey',[surveyController::class, 'add_survey']);
Route::post('update_survey',[surveyController::class, 'update_survey']);
Route::get('fetch_all_survey',[surveyController::class, 'fetch_all_survey']);
Route::get('fetch_survey_byid',[surveyController::class, 'fetch_survey_byid']);
Route::post('delete_survey',[surveyController::class, 'delete_survey']);

//staff controller
Route::get('fetchAllSurvey_ForStaff ',[staffController::class, 'fetchAllSurvey_ForStaff']);
Route::get('fetchQuestionBySurveyTitle',[staffController::class, 'fetchQuestionBySurveyTitle']);
Route::post('save_answer',[staffController::class, 'save_answer']);
