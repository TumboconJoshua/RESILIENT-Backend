<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResearchController;
use Illuminate\Http\Exceptions\NotFoundHttpException;
use App\Http\Controllers\ClassessController;
use App\Http\Controllers\SubjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public Routes Here
Route::post('/login', [AuthController::class, 'login']);
Route::get('/profile', [ProfileController::class, 'getProfile']);

// Public route (no auth)
Route::put('/teacher/lesson-plans/{id}', [\App\Http\Controllers\LessonPlanController::class, 'update']);

// Protected Routes Here
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create-teacher', [TeacherController::class, 'createTeacherAccount']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    Route::post('/profile/research', [ProfileController::class, 'addResearch']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::get('/teacher/profile', [TeacherController::class, 'getProfile']);
    Route::put('/teacher/profile', [TeacherController::class, 'updateProfile']);
    Route::post('/teacher/avatar', [TeacherController::class, 'updateAvatar']);
    Route::post('/teacher/research', [ResearchController::class, 'store']);
    Route::delete('/teacher/research/{research}', [ResearchController::class, 'destroy']);
    Route::apiResource('/teacher/lesson-plans', \App\Http\Controllers\LessonPlanController::class);
});

// Classes endpoints
Route::get('/classes', [ClassessController::class, 'index']);
Route::get('/classes/{id}', [ClassessController::class, 'show']);
Route::post('/classes', [ClassessController::class, 'store']);
Route::put('/classes/{id}', [ClassessController::class, 'update']);
Route::delete('/classes/{id}', [ClassessController::class, 'destroy']);

// Subjects endpoints
Route::get('/subjects', [SubjectController::class, 'index']);
Route::get('/subjects/{id}', [SubjectController::class, 'show']);
Route::post('/subjects', [SubjectController::class, 'store']);
Route::put('/subjects/{id}', [SubjectController::class, 'update']);
Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);



