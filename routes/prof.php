<?php

use App\Http\Controllers\Prof\ProfDashboardController;
use App\Http\Controllers\Prof\ProfExamenCodeController;
use App\Http\Controllers\Prof\ProfExamenCodeQuestionController;
use App\Http\Controllers\Prof\ProfExamenController;
use App\Http\Controllers\Prof\ProfExamenFichierController;
use App\Http\Controllers\Prof\ProfExamenFichierQuestionController;
use App\Http\Controllers\Prof\ProfExamenGlisserDeposerController;
use App\Http\Controllers\Prof\ProfExamenGlisserDeposerQuestionController;
use App\Http\Controllers\Prof\ProfExamenMotsCroisesController;
use App\Http\Controllers\Prof\ProfExamenPointillerController;
use App\Http\Controllers\Prof\ProfExamenPointillerQuestionController;
use App\Http\Controllers\Prof\ProfExamenQcmController;
use App\Http\Controllers\Prof\ProfExamenQcmQuestionController;
use App\Http\Controllers\Prof\ProfExamenRedactionController;
use App\Http\Controllers\Prof\ProfExamenRelierController;
use App\Http\Controllers\Prof\ProfExamenRelierQuestionController;
use App\Http\Controllers\Prof\ProfExamenTextController;
use App\Http\Controllers\Prof\ProfExamenTextQuestionController;
use App\Http\Controllers\Prof\ProfStudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/prof', [ProfDashboardController::class, 'index'])->name('prof.dashboard');

    Route::controller(ProfExamenController::class)->group( function(){
        Route::get('prof/examen/{slug}/show', 'show')->name('prof.examen.show');
        Route::get('prof/examen/{slug}/{examen}/types', 'showTypes')->name('prof.examen.showtypes');
        Route::get('prof/examen/{slug}/{examen}/assign-types', 'assignTypes')->name('prof.examen.assignTypes');
        Route::post('prof/examen/{slug}/{examen}/assign-types', 'storeTypes')->name('prof.examen.storeTypes');
    });

    Route::controller(ProfExamenQcmController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/qcm', 'show')->name('prof.examen.qcm');
        Route::get('prof/examen/{slug}/{examen}/qcm/create', 'create')->name('prof.examen.qcm.create');
        Route::post('prof/examen/{slug}/{examen}/qcm/store', 'store')->name('prof.examen.qcm.store');
        Route::get('/prof/examen/{slug}/{examen}/qcm/{qcm}/edit', 'edit')->name('prof.examen.qcm.edit');
        Route::put('/prof/examen/{slug}/{examen}/qcm/{qcm}', 'update')->name('prof.examen.qcm.update');
        Route::delete('prof/examen/{slug}/{examen}/qcm/{qcm}', 'destroy')->name('prof.examen.qcm.destroy');
    });

    Route::controller(ProfExamenQcmQuestionController::class)->group(function () {
        Route::get('prof/examen/{slug}/{examen}/qcm/{qcm}/question', 'show')->name('prof.examen.qcm.question.show');
        Route::get('prof/examen/{slug}/{examen}/qcm/{qcm}/question/create', 'create')->name('prof.examen.qcm.question.create');
        Route::post('prof/examen/{slug}/{examen}/qcm/{qcm}/question/store', 'store')->name('prof.examen.qcm.question.store');
        Route::delete('/prof/examen/{slug}/{examen}/qcm/{qcm}/question/{question}', 'destroy')->name('prof.examen.qcm.question.destroy');
        Route::get('/prof/examen/{slug}/{examen}/qcm/{qcm}/question/{question}/edit', 'edit')->name('prof.examen.qcm.question.edit');
        Route::put('/prof/examen/{slug}/{examen}/qcm/{qcm}/question/{question}', 'update')->name('prof.examen.qcm.question.update');
    });

    Route::controller(ProfExamenPointillerController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/pointiller', 'show')->name('prof.examen.pointiller');
        Route::get('prof/examen/{slug}/{examen}/pointiller/create', 'create')->name('prof.examen.pointiller.create');
        Route::post('prof/examen/{slug}/{examen}/pointiller/store', 'store')->name('prof.examen.pointiller.store');
        Route::get('prof/examen/{slug}/{examen}/pointiller/{pointiller}/edit', 'edit')->name('prof.examen.pointiller.edit');
        Route::put('/prof/examen/{slug}/{examen}/pointiller/{pointiller}', 'update')->name('prof.examen.pointiller.update');
        Route::delete('prof/examen/{slug}/{examen}/pointiller/{pointiller}', 'destroy')->name('prof.examen.pointiller.destroy');
    });

    Route::controller(ProfExamenPointillerQuestionController::class)->group(function () {
        Route::get('prof/examen/{slug}/{examen}/pointiller/{pointiller}/question', 'show')->name('prof.examen.pointiller.question.show');
        Route::get('prof/examen/{slug}/{examen}/pointiller/{pointiller}/question/create', 'create')->name('prof.examen.pointiller.question.create');
        Route::post('prof/examen/{slug}/{examen}/pointiller/{pointiller}/question/store', 'store')->name('prof.examen.pointiller.question.store');
        Route::get('/prof/examen/{slug}/{examen}/pointiller/{pointiller}/question/{question}/edit', 'edit')->name('prof.examen.pointiller.question.edit');
        Route::put('/prof/examen/{slug}/{examen}/pointiller/{pointiller}/question/{question}', 'update')->name('prof.examen.pointiller.question.update');
        Route::delete('/prof/examen/{slug}/{examen}/pointiller/{pointiller}/question/{question}', 'destroy')->name('prof.examen.pointiller.question.destroy');
    });

    Route::controller(ProfExamenRelierController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/relier', 'show')->name('prof.examen.relier');
        Route::get('prof/examen/{slug}/{examen}/relier/create', 'create')->name('prof.examen.relier.create');
        Route::post('prof/examen/{slug}/{examen}/relier/store', 'store')->name('prof.examen.relier.store');
        Route::delete('prof/examen/{slug}/{examen}/relier/{relier}', 'destroy')->name('prof.examen.relier.destroy');
    });

    Route::controller(ProfExamenRelierQuestionController::class)->group(function () {
        Route::get('prof/examen/{slug}/{examen}/relier/{relier}/question', 'show')->name('prof.examen.relier.question.show');
        Route::get('prof/examen/{slug}/{examen}/relier/{relier}/question/create', 'create')->name('prof.examen.relier.question.create');
        Route::post('prof/examen/{slug}/{examen}/relier/{relier}/question/store', 'store')->name('prof.examen.relier.question.store');
        Route::get('prof/examen/{slug}/{examen}/relier/{relier}/question/{question}/edit', 'edit')->name('prof.examen.relier.question.edit');
        Route::put('prof/examen/{slug}/{examen}/relier/{relier}/question/{question}', 'update')->name('prof.examen.relier.question.update');
        Route::delete('/prof/examen/{slug}/{examen}/relier/{relier}/question/{question}', 'destroy')->name('prof.examen.relier.question.destroy');
    });

    Route::controller(ProfExamenFichierController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/fichier', 'show')->name('prof.examen.fichier');
        Route::get('prof/examen/{slug}/{examen}/fichier/create', 'create')->name('prof.examen.fichier.create');
        Route::post('prof/examen/{slug}/{examen}/fichier/store', 'store')->name('prof.examen.fichier.store');
        Route::get('prof/examen/{slug}/{examen}/fichier/{fichier}/edit', 'edit')->name('prof.examen.fichier.edit');
        Route::put('prof/examen/{slug}/{examen}/fichier/{fichier}', 'update')->name('prof.examen.fichier.update');
        Route::delete('prof/examen/{slug}/{examen}/fichier/{fichier}', 'destroy')->name('prof.examen.fichier.destroy');
    });

    Route::controller(ProfExamenFichierQuestionController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/fichier/{fichier}/question', 'show')->name('prof.examen.fichier.qeustion.show');
        Route::get('prof/examen/{slug}/{examen}/fichier/{fichier}/question/create', 'create')->name('prof.examen.fichier.qeustion.create');
        Route::post('prof/examen/{slug}/{examen}/fichier/{fichier}/question/store', 'store')->name('prof.examen.fichier.qeustion.store');
        Route::get('prof/examen/{slug}/{examen}/fichier/{fichier}/question/{question}/edit', 'edit')->name('prof.examen.fichier.qeustion.edit');
        Route::put('prof/examen/{slug}/{examen}/fichier/{fichier}/question/{question}', 'update')->name('prof.examen.fichier.qeustion.update');
        Route::delete('prof/examen/{slug}/{examen}/fichier/{fichier}/question/{question}', 'destroy')->name('prof.examen.fichier.qeustion.destroy');
    });

    Route::controller(ProfExamenCodeController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/code', 'show')->name('prof.examen.code');
        Route::get('prof/examen/{slug}/{examen}/code/create', 'create')->name('prof.examen.code.create');
        Route::post('prof/examen/{slug}/{examen}/code/store', 'store')->name('prof.examen.code.store');
        Route::get('prof/examen/{slug}/{examen}/code/{code}/edit', 'edit')->name('prof.examen.code.edit');
        Route::put('/prof/examen/{slug}/{examen}/code/{code}', 'update')->name('prof.examen.code.update');
        Route::delete('prof/examen/{slug}/{examen}/code/{code}', 'destroy')->name('prof.examen.code.destroy');
    });

    Route::controller(ProfExamenCodeQuestionController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/code/{code}/question', 'show')->name('prof.examen.code.question.show');
        Route::get('prof/examen/{slug}/{examen}/code/{code}/question/create', 'create')->name('prof.examen.code.question.create');
        Route::post('prof/examen/{slug}/{examen}/code/{code}/question/store', 'store')->name('prof.examen.code.question.store');
        Route::get('prof/examen/{slug}/{examen}/code/{code}/question/{question}/edit', 'edit')->name('prof.examen.code.question.edit');
        Route::put('prof/examen/{slug}/{examen}/code/{code}/question/{question}', 'update')->name('prof.examen.code.question.update');
        Route::delete('prof/examen/{slug}/{examen}/code/{code}/question/{question}', 'destroy')->name('prof.examen.code.question.destroy');
    });

    Route::controller(ProfExamenTextController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/text', 'show')->name('prof.examen.text');
        Route::get('prof/examen/{slug}/{examen}/text/create', 'create')->name('prof.examen.text.create');
        Route::post('prof/examen/{slug}/{examen}/text/store', 'store')->name('prof.examen.text.store');
        Route::get('prof/examen/{slug}/{examen}/text/{text}/edit', 'edit')->name('prof.examen.text.edit');
        Route::put('/prof/examen/{slug}/{examen}/text/{text}', 'update')->name('prof.examen.text.update');
        Route::delete('prof/examen/{slug}/{examen}/text/{text}', 'destroy')->name('prof.examen.text.destroy');
    });

    Route::controller(ProfExamenTextQuestionController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/text/{text}/question/create', 'create')->name('prof.examen.text.question.create');
        Route::post('prof/examen/{slug}/{examen}/text/{text}/question/store', 'store')->name('prof.examen.text.question.store');
        Route::get('prof/examen/{slug}/{examen}/text/{text}/question/{question}/edit', 'edit')->name('prof.examen.text.question.edit');
        Route::put('prof/examen/{slug}/{examen}/text/{text}/question/{question}', 'update')->name('prof.examen.text.question.update');
        Route::delete('prof/examen/{slug}/{examen}/text/{text}/question/{question}', 'destroy')->name('prof.examen.text.question.destroy');
    });

    Route::controller(ProfExamenRedactionController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/redaction', 'show')->name('prof.examen.redaction');
        Route::get('prof/examen/{slug}/{examen}/redaction/create', 'create')->name('prof.examen.redaction.create');
        Route::post('prof/examen/{slug}/{examen}/redaction/store', 'store')->name('prof.examen.redaction.store');
        Route::get('prof/examen/{slug}/{examen}/redaction/{redaction}/edit', 'edit')->name('prof.examen.redaction.edit');
        Route::get('prof/examen/{slug}/{examen}/redaction/{redaction}/show', 'detail')->name('prof.examen.redaction.show');
        Route::put('/prof/examen/{slug}/{examen}/redaction/{redaction}', 'update')->name('prof.examen.redaction.update');
        Route::delete('prof/examen/{slug}/{examen}/redaction/{redaction}', 'destroy')->name('prof.examen.redaction.destroy');
    });

    Route::controller(ProfExamenMotsCroisesController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/motscroises', 'show')->name('prof.examen.motscroises');
        Route::get('prof/examen/{slug}/{examen}/motscroises/create', 'create')->name('prof.examen.motscroises.create');
        Route::post('prof/examen/{slug}/{examen}/motscroises/store', 'store')->name('prof.examen.motscroises.store');
        Route::get('prof/examen/{slug}/{examen}/motscroises/{motscroises}/edit', 'edit')->name('prof.examen.motscroises.edit');
        Route::put('/prof/examen/{slug}/{examen}/motscroises/{motscroises}', 'update')->name('prof.examen.motscroises.update');
        Route::delete('prof/examen/{slug}/{examen}/motscroises/{motscroises}', 'destroy')->name('prof.examen.motscroises.destroy');
    });

    Route::controller(ProfExamenGlisserDeposerController::class)->group(function () {
        Route::get('prof/examen/{slug}/{examen}/glisserdeposer', 'show')->name('prof.examen.glisserdeposer');
        Route::get('prof/examen/{slug}/{examen}/glisserdeposer/create', 'create')->name('prof.examen.glisserdeposer.create');
        Route::post('prof/examen/{slug}/{examen}/glisserdeposer/store', 'store')->name('prof.examen.glisserdeposer.store');
        Route::get('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}/edit', 'edit')->name('prof.examen.glisserdeposer.edit');
        Route::put('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}', 'update')->name('prof.examen.glisserdeposer.update');
        Route::delete('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}', 'destroy')->name('prof.examen.glisserdeposer.destroy');
    });

    Route::controller(ProfExamenGlisserDeposerQuestionController::class)->group(function () {
        Route::get('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}/question', 'show')->name('prof.examen.glisserdeposer.question.index');
        Route::get('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}/question/create', 'create')->name('prof.examen.glisserdeposer.question.create');
        Route::post('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}/question/store', 'store')->name('prof.examen.glisserdeposer.question.store');
        Route::get('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}/question/{question}/edit', 'edit')->name('prof.examen.glisserdeposer.question.edit');
        Route::put('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}/question/{question}', 'update')->name('prof.examen.glisserdeposer.question.update');
        Route::delete('prof/examen/{slug}/{examen}/glisserdeposer/{glisserdeposer}/question/{question}', 'destroy')->name('prof.examen.glisserdeposer.question.destroy');
    });

    Route::controller(ProfStudentController::class)->group(function()
    {
        Route::get('prof/student/{slug}', 'show')->name('prof.student.show');
    });
});