<?php

use App\Http\Controllers\Student\StudentExamenCodeController;
use App\Http\Controllers\Student\StudentExamenController;
use App\Http\Controllers\Student\StudentExamenDashboardController;
use App\Http\Controllers\Student\StudentExamenFichierController;
use App\Http\Controllers\Student\StudentExamenGlisserDeposerController;
use App\Http\Controllers\Student\StudentExamenHistoriqueController;
use App\Http\Controllers\Student\StudentExamenImageController;
use App\Http\Controllers\Student\StudentExamenMotsCroisesController;
use App\Http\Controllers\Student\StudentExamenPointillerController;
use App\Http\Controllers\Student\StudentExamenQcmController;
use App\Http\Controllers\Student\StudentExamenRedactionController;
use App\Http\Controllers\Student\StudentExamenRelierController;
use App\Http\Controllers\Student\StudentExamenTextController;
use App\Http\Controllers\Student\studentHomeCotroller;
use App\Http\Middleware\CheckExamenEnCours;
use Illuminate\Support\Facades\Route;

Route::get('/', [studentHomeCotroller::class, 'index'])
    ->name('home');

Route::middleware(['auth', 'role:student'])->group(function (){

Route::middleware(['auth'])->group(function () {

    Route::controller(StudentExamenController::class)->group(function(){
        Route::get('/examen/{slug}', 'index')->name('student.examen.show');
        Route::post('/examen/{examen}/start', 'start')->name('student.examen.start');
        Route::get('/student/examen/{examen}/terminer', 'terminer')->name('student.examen.terminer');
    });

    Route::middleware(CheckExamenEnCours::class)->group(function(){
        Route::controller(StudentExamenQcmController::class) ->group(function(){
            Route::get('examen/{examen}/{slug}/qcm/{qcm}', 'show')->name('examen.qcm.show');
            Route::post('examen/{examen}/{slug}/qcm/{qcm}/answer', 'answer')->name('examen.qcm.answer');
        });
        Route::controller(StudentExamenPointillerController::class)->group(function(){
            Route::get('examen/{examen}/{slug}/pointiller/{pointiller}', 'show')->name('examen.pointiller.show');
            Route::post('examen/{examen}/{slug}/pointiller/{pointiller}/store', 'store')->name('examen.pointiller.store');
        });
        Route::controller(StudentExamenRelierController::class)->group(function(){
            Route::get('examen/{examen}/{slug}/relier/{relier}', 'show')->name('examen.relier.show');
            Route::post('examen/{examen}/{slug}/relier/{relier}/store', 'store')->name('examen.relier.store'); 
        });
        Route::controller(StudentExamenCodeController::class)->group(function () {
            Route::get('examen/{examen}/{slug}/code/{code}', 'show')->name('examen.code.show');
            Route::post('examen/{examen}/{slug}/code/{code}/store', 'store')->name('examen.code.store');
        });
        Route::controller(StudentExamenFichierController::class)->group(function () {
            Route::get('examen/{examen}/{slug}/fichier/{fichier}', 'show')->name('examen.fichier.show');
            Route::post('examen/{examen}/{slug}/fichier/{fichier}/store', 'store')->name('examen.fichier.store');
        });

        Route::controller(StudentExamenTextController::class)->group(function () {
            Route::get('examen/{examen}/{slug}/text/{text}', 'show')->name('examen.text.show');
            Route::post('examen/{examen}/{slug}/text/{text}/store', 'store')->name('examen.text.store');
        });
        Route::controller(StudentExamenRedactionController::class)->group(function () {
            Route::get('examen/{examen}/{slug}/redaction/{redaction}', 'show')->name('examen.redaction.show');
            Route::post('examen/{examen}/{slug}/redaction/{redaction}/store', 'store')->name('examen.redaction.store');
        });
        Route::controller(StudentExamenMotsCroisesController::class)->group(function () {
            Route::get('examen/{examen}/{slug}/motscroises/{motscroises}', 'show')->name('examen.motscroises.show');
            Route::post('examen/{examen}/{slug}/motscroises/{motscroises}/store', 'store')->name('examen.motscroises.store');
        });

        Route::controller(StudentExamenGlisserDeposerController::class)->group(function(){
            Route::get('/examen/{examen}/{slug}/glisserdeposer/{glisserdeposer}', 'show')->name('examen.glisserdeposer.show');
            Route::post('/examen/{examen}/{slug}/glisserdeposer/{glisserdeposer}/store', 'store')->name('examen.glisserdeposer.store');
        });

        Route::controller(StudentExamenImageController::class)->group(function(){
            Route::get('/examen/{examen}/{slug}/image/{image}', 'show')->name('examen.image.show');
            Route::post('/examen/{examen}/{slug}/image/{image}/store', 'store')->name('examen.image.store');
        });
    });

    Route::controller(StudentExamenHistoriqueController::class)->group(function(){
        Route::get('/dashboard', 'dashboard')->name('student.dashboard');
        Route::get('/mes-examen/{attempt}', 'show')->name('student.examen.historique.show');
    });

});

});