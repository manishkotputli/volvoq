<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\QuestionManagementController;
use App\Http\Controllers\Admin\QuestionCategoryController;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
 Route::get('notifications', function () {
            $notifications = auth()->user()
                ->notifications()
                ->latest()
                ->paginate(20);

            return view('admin.notifications.index', compact('notifications'));
        })->name('notifications.index');

            Route::post('notifications/read-all', function () {
    auth()->user()->unreadNotifications->markAsRead();
})->name('notifications.readAll');




    });

    
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('questions', [QuestionManagementController::class,'index'])->name('questions.index');
    Route::get('questions/create', [QuestionManagementController::class,'create'])->name('questions.create');
    Route::post('questions', [QuestionManagementController::class,'store'])->name('questions.store');
    Route::get('questions/{question}/edit', [QuestionManagementController::class,'edit'])->name('questions.edit');
    Route::put('questions/{question}', [QuestionManagementController::class,'update'])->name('questions.update');
    Route::delete('questions/{question}', [QuestionManagementController::class,'destroy'])->name('questions.destroy');

    Route::post('questions/bulk', [QuestionManagementController::class,'bulkAction'])->name('questions.bulk');

     Route::post('questions/{question}/approve',
        [QuestionManagementController::class,'approve'])
        ->name('questions.approve');

    Route::post('questions/{question}/reject',
        [QuestionManagementController::class,'reject'])
        ->name('questions.reject');
});


Route::middleware(['auth','admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('categories', QuestionCategoryController::class);
    })->name('question_categories.index');



Route::get('/preview/question/{slug}', function ($slug) {
    $question = \App\Models\Question::where('slug',$slug)->firstOrFail();
    return view('public.question-preview', compact('question'));
});