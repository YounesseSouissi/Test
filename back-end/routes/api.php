<?php

use App\Http\Controllers\Admin\ChapitreController as AdminChapitreController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamenController as AdminExamenController;
use App\Http\Controllers\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\ChapitreController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
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
Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);
    Route::post('/photo', [ProfileController::class, 'updatePhoto']);

});
Route::middleware(['auth:sanctum','ability:user'])->group(function(){
    Route::apiResource('modules',ModuleController::class);
    Route::apiResource('chapitres',ChapitreController::class);
    Route::apiResource('questions',QuestionController::class);
    Route::apiResource('examens',ExamenController::class);
    Route::get('getchapitresbymodule/{id}',[ChapitreController::class,'getChapitresByModule']);
    Route::get('getmodulebychapitre/{id}',[ModuleController::class,'getModuleByChapitre']);
    Route::get('examen/generate',[ExamenController::class,'generate']);
    Route::get('examen/update',[ExamenController::class,'updateExam']);
    Route::get('examen/delete',[ExamenController::class,'deleteExam']);

});
Route::middleware(['auth:sanctum','ability:admin'])->prefix('admin')->group(function(){
    Route::apiResource('users',UserController::class);
    Route::apiResource('modules',AdminModuleController::class);
    Route::apiResource('chapitres',AdminChapitreController::class);
    Route::apiResource('questions',AdminQuestionController::class);
    Route::apiResource('examens',AdminExamenController::class);
    Route::apiResource('dashboard',DashboardController::class);
    Route::get('number_question_non_confirme',[AdminQuestionController::class,'NumberQuestionsNonConfirme']);
    Route::get('getquestions/non-confirme',[AdminQuestionController::class,'getQuestionsNonConfirme']);
    Route::patch('confirme-question/{question}',[AdminQuestionController::class,'ConfirmeQuestion']);
    Route::get('getmodulebychapitre/{id}',[AdminModuleController::class,'getModuleByChapitre']);
    Route::get('getchapitresbymodule/{id}',[AdminChapitreController::class,'getChapitresByModule']);
    Route::get('examen/generate',[AdminExamenController::class,'generate']);
    Route::get('examen/update',[AdminExamenController::class,'updateExam']);
    Route::get('examen/delete',[AdminExamenController::class,'deleteExam']);

});
require __DIR__.'/auth.php';
