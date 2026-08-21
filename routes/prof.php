<?php

use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrectionController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenCodeController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenFichierController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenGlissesDeposesController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenImageController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenMotsCroisesController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenPointllerController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenQcmController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenRedactionController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenRelierController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfCorrigeExamenTextController;
use App\Http\Controllers\Prof\CorrigeExamen\ProfExamenCommentaireController;
use App\Http\Controllers\Prof\ProfDashboardController;
use App\Http\Controllers\Prof\ProfExamenCodeController;
use App\Http\Controllers\Prof\ProfExamenCodeQuestionController;
use App\Http\Controllers\Prof\ProfExamenController;
use App\Http\Controllers\Prof\ProfExamenFichierController;
use App\Http\Controllers\Prof\ProfExamenFichierQuestionController;
use App\Http\Controllers\Prof\ProfExamenGlisserDeposerController;
use App\Http\Controllers\Prof\ProfExamenGlisserDeposerQuestionController;
use App\Http\Controllers\Prof\ProfExamenImageController;
use App\Http\Controllers\Prof\ProfExamenImageQuestionController;
use App\Http\Controllers\Prof\ProfExamenMotsCroisesController;
use App\Http\Controllers\Prof\ProfExamenPointillerController;
use App\Http\Controllers\Prof\ProfExamenPointillerQuestionController;
use App\Http\Controllers\Prof\ProfExamenQcmController;
use App\Http\Controllers\Prof\ProfExamenQcmQuestionController;
use App\Http\Controllers\Prof\ProfExamenRedactionController;
use App\Http\Controllers\Prof\ProfExamenRelierController;
use App\Http\Controllers\Prof\ProfExamenRelierQuestionController;
use App\Http\Controllers\Prof\ProfExamenStudentController;
use App\Http\Controllers\Prof\ProfExamenTextController;
use App\Http\Controllers\Prof\ProfExamenTextQuestionController;
use App\Http\Controllers\Prof\ProfStudentController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Mailer\Transport\RoundRobinTransport;

Route::middleware(['auth', 'role:prof'])->group(function (){
    Route::get('/prof', [ProfDashboardController::class, 'index'])->name('prof.dashboard');

    Route::controller(ProfExamenController::class)->group( function(){
        Route::get('prof/examen/{slug}/show', 'show')->name('prof.examen.show');
        Route::get('prof/examen/{slug}/{examen}/types', 'showTypes')->name('prof.examen.showtypes');
        Route::get('prof/examen/{slug}/{examen}/assign-types', 'assignTypes')->name('prof.examen.assignTypes');
        Route::post('prof/examen/{slug}/{examen}/assign-types', 'storeTypes')->name('prof.examen.storeTypes');
        Route::post('prof/examen/{slug}/{examen}/terminer-creation', 'terminerCreation')->name('prof.examen.terminerCreation');
        Route::post('prof/examen/{slug}/{examenId}/modifier', 'remettreEnBrouillon')->name('prof.examen.remettreEnBrouillon');
    });

    Route::controller(ProfExamenQcmController::class)->group(function(){
        // ------
        Route::get('prof/{slug}/qcm', 'showbanque')->name('prof.question.qcm');
        Route::get('prof/{slug}/qcm/create', 'create')->name('prof.question.qcm.create');
        Route::post('prof/{slug}/qcm/store', 'store')->name('prof.question.qcm.store');
        Route::get('/prof/{slug}/qcm/{qcmId}/edit', 'edit')->name('prof.question.qcm.edit');
        Route::put('prof/{slug}/qcm/{qcmId}/update', 'update')->name('prof.question.qcm.update');
        Route::delete('prof/{slug}/qcm/{qcm}', 'destroy')->name('prof.question.qcm.destroy');
        // -------
        
        Route::get('prof/examen/{slug}/{examen}/qcm', 'show')->name('prof.examen.qcm');

        // ------
        Route::get('prof/examen/{slug}/{examenId}/qcm/select-questions', 'selectQuestionsForm')->name('prof.examen.qcm.selectQuestions.form');
        Route::post('prof/examen/{slug}/{examenId}/qcm/select-questions', 'storeSelectedQuestions')->name('prof.examen.qcm.selectQuestions.store');
        Route::delete('prof/examen/{slug}/{examenId}/qcm/question/{questionId}','removeQuestion')->name('prof.examen.qcm.question.remove');
        // -------
    });

    Route::controller(ProfExamenQcmQuestionController::class)->group(function () {
        // --------
        Route::get('prof/{slug}/qcm/{qcm}/question/create', 'create')->name('prof.qcm.question.create');
        Route::post('prof/{slug}/qcm/{qcm}/question/store', 'store')->name('prof.qcm.question.store');
        Route::get('/prof/{slug}/qcm/{qcm}/question/{question}/edit', 'edit')->name('prof.qcm.question.edit');
        Route::put('/prof/{slug}/qcm/{qcm}/question/{question}', 'update')->name('prof.qcm.question.update');
        Route::delete('/prof/{slug}/qcm/{qcm}/question/{question}', 'destroy')->name('prof.qcm.question.destroy');
        // --------
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
        // --------
        Route::get('prof/{slug}/relier', 'showbanque')->name('prof.question.relier');
        Route::get('prof/{slug}/relier/create', 'create')->name('prof.question.relier.create');
        Route::post('prof/{slug}/relier/store', 'store')->name('prof.question.relier.store');
        Route::get('/prof/{slug}/relier/{relierId}/edit', 'edit')->name('prof.question.relier.edit');
        Route::put('prof/{slug}/relier/{relierId}/update', 'update')->name('prof.question.relier.update');
        Route::delete('prof/{slug}/relier/{relierId}', 'destroy')->name('prof.question.relier.destroy');
        // --------

        Route::get('prof/examen/{slug}/{examen}/relier', 'show')->name('prof.examen.relier');

        // -------
        Route::get('prof/examen/{slug}/{examenId}/relier/select-questions', 'selectQuestionsForm')->name('prof.examen.relier.selectQuestions.form');
        Route::post('prof/examen/{slug}/{examenId}/relier/select-questions', 'storeSelectedQuestions')->name('prof.examen.relier.selectQuestions.store');
        Route::delete('prof/examen/{slug}/{examenId}/relier/question/{questionId}','removeQuestion')->name('prof.examen.relier.question.remove');
        // --------
    });

    Route::controller(ProfExamenRelierQuestionController::class)->group(function () {
        // -------
        Route::get('prof/{slug}/relier/{relierId}/question/create', 'create')->name('prof.relier.question.create');
        Route::post('prof/{slug}/relier/{relier}/question/store', 'store')->name('prof.relier.question.store');
        Route::get('/prof/{slug}/relier/{relier}/question/{question}/edit', 'edit')->name('prof.relier.question.edit');
        Route::put('/prof/{slug}/relier/{relier}/question/{question}', 'update')->name('prof.relier.question.update');
        Route::delete('/prof/{slug}/relier/{relier}/question/{question}', 'destroy')->name('prof.relier.question.destroy');
        // --------
        
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

    Route::controller(ProfExamenImageController::class)->group(function () {
        Route::get('prof/examen/{slug}/{examen}/image', 'show')->name('prof.examen.image');
        Route::get('prof/examen/{slug}/{examen}/image/create', 'create')->name('prof.examen.image.create');
        Route::post('prof/examen/{slug}/{examen}/image/store', 'store')->name('prof.examen.image.store');
        Route::get('prof/examen/{slug}/{examen}/image/{image}/edit', 'edit')->name('prof.examen.image.edit');
        Route::put('prof/examen/{slug}/{examen}/image/{image}', 'update')->name('prof.examen.image.update');
        Route::delete('prof/examen/{slug}/{examen}/image/{image}', 'destroy')->name('prof.examen.image.destroy');
    });

    Route::controller(ProfExamenImageQuestionController::class)->group(function () {
        Route::get('prof/examen/{slug}/{examen}/image/{image}/question/create', 'create')->name('prof.examen.image.question.create');
        Route::post('prof/examen/{slug}/{examen}/image/{image}/question/store', 'store')->name('prof.examen.image.question.store');
        Route::get('prof/examen/{slug}/{examen}/image/{image}/question/{question}/edit', 'edit')->name('prof.examen.image.question.edit');
        Route::put('prof/examen/{slug}/{examen}/image/{image}/question/{question}', 'update')->name('prof.examen.image.question.update');
        Route::delete('prof/examen/{slug}/{examen}/image/{image}/question/{question}', 'destroy')->name('prof.examen.image.question.destroy');
    });

    Route::controller(ProfStudentController::class)->group(function()
    {
        Route::get('prof/{slug}/student', 'show')->name('prof.student.show');
        Route::get('stat/{slug}/student/{student}', 'studentstatexam')->name('student.statexam');
    });

    Route::controller(ProfExamenStudentController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student', 'studentswithexamen')->name('prof.examen.studentswithexamen');
        Route::get('prof/examen/{slug}/{examen}/student/{student}', 'examenwherestudent')->name('prof.examen.examenwherestudent');
    });

    //corriger//
    Route::controller(ProfCorrigeExamenQcmController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/qcm' , 'showtache')->name('prof.examen.showtache.qcm');
    });
    Route::controller(ProfCorrigeExamenRedactionController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/redaction' , 'showtache')->name('prof.examen.showtache.redaction');
        Route::post('prof/correction/redaction/{reponse}/annoter', 'storeAnnotation')->name('prof.correction.redaction.annoter');
    });
    Route::controller(ProfCorrigeExamenRelierController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/relier' , 'showtache')->name('prof.examen.showtache.relier');
    });
    Route::controller(ProfCorrigeExamenTextController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/text' , 'showtache')->name('prof.examen.showtache.text');
        Route::post('prof/correction/text/{text}/annoter', 'storeAnnotation')->name('prof.correction.text.annoter');
    });
    Route::controller(ProfCorrigeExamenCodeController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/code' , 'showtache')->name('prof.examen.showtache.code');
        Route::post('prof/correction/code/{code}/annoter', 'storeAnnotation')->name('prof.correction.code.annoter');
    });
    Route::controller(ProfCorrigeExamenMotsCroisesController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/motscroises' , 'showtache')->name('prof.examen.showtache.motscroises');
    });
    Route::controller(ProfCorrigeExamenFichierController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/fichier' , 'showtache')->name('prof.examen.showtache.fichier');
        Route::post('prof/correction/fichier/{fichier}/annoter', 'storeAnnotation')->name('prof.correction.fichier.annoter');
    });
    Route::controller(ProfCorrigeExamenImageController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/image' , 'showtache')->name('prof.examen.showtache.image');
        Route::post('prof/correction/image/{imageExercice}/annoter', 'storeAnnotation')->name('prof.correction.image.annoter');
    });
    Route::controller(ProfCorrigeExamenGlissesDeposesController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/glisserdeposer' , 'showtache')->name('prof.examen.showtache.glisserdeposer');
    });
    Route::controller(ProfCorrigeExamenPointllerController::class)->group(function(){
        Route::get('prof/examen/{slug}/{examen}/student/{student}/pointiller' , 'showtache')->name('prof.examen.showtache.pointiller');
    });

    Route::post('prof/correction/commentaire', [ProfExamenCommentaireController::class, 'storeCommentaire'])
    ->name('prof.correction.storeCommentaire');
    
    Route::post('prof/correction/{slug}/{examen}/{student}/terminer', [ProfCorrectionController::class, 'terminerCorrection'])
    ->name('prof.correction.terminer');
});