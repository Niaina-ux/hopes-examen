<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDasboardController;
use App\Http\Controllers\Admin\AdminEmailStudentController;
use App\Http\Controllers\Admin\AdminExamenController;
use App\Http\Controllers\Admin\AdminExamenStudentController;
use App\Http\Controllers\Admin\AdminProfController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminStudentExameQcmController;
use App\Http\Controllers\Admin\AdminTypeExerciceController;
use Illuminate\Support\Facades\Route;

Route::get('/admin', [AdminDasboardController::class, 'index'])->name('admin.dashboard');
Route::controller(AdminProfController::class)->group(function(){
    Route::get('/admin/prof/index',  'index')
        ->name('admin.prof.index');
    Route::get('/admin/prof/create',  'create')
        ->name('admin.prof.create');
    Route::post('/admin/prof/store',  'store')
        ->name('admin.prof.store');
    Route::delete('/admin/prof/{prof}',  'destroy')
        ->name('admin.prof.destroy');
    Route::get('/admin/prof/{prof}/assign-categorie',  'assignCategorie')
        ->name('admin.prof.assignCategorie');
    Route::post('/admin/prof/{prof}/assign-categorie',  'storeCategorie')
        ->name('admin.prof.storeCategorie');
});

Route::controller(AdminStudentController::class)->group(function(){
    Route::get('/admin/student/index',  'index')->name('admin.student.index');
    Route::get('admin/student/create', 'create')->name('admin.student.create');
    Route::post('/admin/student/store',  'store')->name('admin.student.store');
    Route::delete('/admin/student/{student}',  'destroy')->name('admin.student.destroy');
    Route::get('/admin/student/{student}/assign-categorie',  'assignCategorie')->name('admin.student.assignCategorie');
    Route::post('/admin/student/{student}/assign-categorie',  'storeCategorie')->name('admin.student.storeCategorie');
    Route::get('admin/student/{studentId}/show', 'examenallstudent')->name('admin.student.show');
});

Route::get('/admin/student/examen/{examen}/qcm', [AdminStudentExameQcmController::class, 'index'])
    ->name('admin.student.examen.qcm');

Route::controller(AdminExamenController::class)->group(function(){
    Route::get('/admin/examen/{slug}/show', 'show')->name('admin.examen.show');
    Route::get('/admin/examen/{slug}/create', 'create')->name('admin.examen.create');
    Route::post('/admin/examen/{slug}/store', 'store')->name('admin.examen.store');
    Route::get('/admin/examen/{slug}/{examen}/edit', 'edit')->name('admin.examen.edit');
    Route::put('/admin/examen/{slug}/{examen}', 'update')->name('admin.examen.update');
    Route::delete('/admin/examen/{slug}/{examen}', 'destroy')->name('admin.examen.destroy');
});
    

Route::controller(AdminCategoryController::class)->group(function(){
    Route::get('/admin/categorie', 'index')->name('admin.categorie.index');
    Route::get('/admin/categorie/create', 'create')->name('admin.categorie.create');
    Route::post('/admin/categorie/store', 'store')->name('admin.categorie.store');
    Route::get('/admin/categorie/{categorie:id}/edit', 'edit')->name('admin.categorie.edit');
    Route::put('/admin/categorie/{categorie:id}/update', 'update')->name('admin.categorie.update');
    Route::delete('/admin/categorie/{categorie}', 'destroy')->name('admin.categorie.destroy');
    
});

Route::controller(AdminTypeExerciceController::class)->group(function(){
    // Route::get('/admin/type-exercice', 'index')->name('admin.typeExercice.index');
    Route::get('/admin/type-exercice/create', 'create')->name('admin.typeExercice.create');
    Route::post('/admin/type-exercice/store', 'store')->name('admin.typeExercice.store');
    Route::get('/admin/type-exercice/{typeExercice}/edit', 'edit')->name('admin.typeExercice.edit');
    Route::put('/admin/type-exercice/{typeExercice}', 'update')->name('admin.typeExercice.update');
    Route::delete('/admin/type-exercice/{typeExercice}', 'destroy')->name('admin.typeExercice.destroy');
    Route::get('admin/categorie/{categorie:id}/types-exercice', 'editTypesExercice')->name('admin.categorie.editTypesExercice');
    Route::post('admin/categorie/{categorie:id}/types-exercice', 'updateTypesExercice')->name('admin.categorie.updateTypesExercice');
    Route::get('/admin/type-exercice', 'index')->name('admin.typeExercice.index');
});


Route::controller(AdminExamenStudentController::class)->group(function () {
    Route::get('admin/examen/{slug}/{examen}/student/show', 'show')->name('admin.examen.student.show');
    Route::get('admin/examen/{slug}/{examen}/student/create', 'create')->name('admin.examen.student.create');
    Route::post('admin/examen/{slug}/{examen}/student/store', 'store')->name('admin.examen.student.store');
    Route::delete('admin/examen/{slug}/{examen}/student/{studentExamen}', 'destroy')->name('admin.examen.student.destroy');
    Route::get('admin/examen/{slug}/{examen}/student/{studentId}',  'examenwherestudent')->name('admin.examen.student.examenwherestudent');
});

Route::controller(AdminEmailStudentController::class)->group(function(){
    Route::post('admin/examen/{slug}/{examenId}/student/{studentId}/notifier', 'notifierEtudiant')->name('admin.examen.student.notifier');
    Route::post('admin/examen/{slug}/{examenId}/notifier-groupe', 'notifierGroupe')->name('admin.examen.student.notifierGroupe');
});