<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TaskController;

use App\Http\Controllers\ProjectController;

use App\Http\Controllers\SprintController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;



Route::middleware('auth')->group(function () {

    Route::get('/', function () {

        return redirect()->route('projects.index');

    });



    Route::get('/search', 'App\\Http\\Controllers\\SearchController@index')->name('search.index');
    Route::get('/notifications', 'App\\Http\\Controllers\\NotificationController@index')->name('notifications.index');
    Route::patch('/notifications/read-all', 'App\\Http\\Controllers\\NotificationController@markAllAsRead')->name('notifications.readAll');
    Route::patch('/notifications/{notification}/read', 'App\\Http\\Controllers\\NotificationController@markAsRead')->name('notifications.read');
    Route::delete('/notifications/{notification}', 'App\\Http\\Controllers\\NotificationController@destroy')->name('notifications.destroy');

    Route::resource('tasks', TaskController::class);

    Route::put('/updateStatus/{id}', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    Route::post('/tasks/{task}/move-to-sprint', [TaskController::class, 'moveToSprint'])->name('tasks.moveToSprint');

    Route::patch('/tasks/{task}/kanban', [TaskController::class, 'updateKanban'])->name('tasks.updateKanban');

    Route::get('/projects/{project}/sprints-json', [TaskController::class, 'sprintsForProject'])->name('tasks.sprintsForProject');



    Route::post('/projects/generate-ai', [ProjectController::class, 'generateAi'])->name('projects.generate-ai');

    Route::resource('projects', ProjectController::class);

    Route::get('/projects/{project}/backlog', [ProjectController::class, 'backlog'])->name('projects.backlog');



    Route::prefix('projects/{project}/sprints')->name('projects.sprints.')->group(function () {

        Route::get('/', [SprintController::class, 'index'])->name('index');

        Route::get('/create', [SprintController::class, 'create'])->name('create');

        Route::post('/', [SprintController::class, 'store'])->name('store');

        Route::get('/{sprint}', [SprintController::class, 'show'])->name('show');

        Route::get('/{sprint}/edit', [SprintController::class, 'edit'])->name('edit');

        Route::put('/{sprint}', [SprintController::class, 'update'])->name('update');

        Route::delete('/{sprint}', [SprintController::class, 'destroy'])->name('destroy');

        Route::post('/{sprint}/start', [SprintController::class, 'start'])->name('start');

        Route::post('/{sprint}/complete', [SprintController::class, 'complete'])->name('complete');

    });

});

