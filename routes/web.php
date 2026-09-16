<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('projects', ProjectController::class);

Route::post(
    '/projects/{project}/members',
    [ProjectController::class, 'addMember']
)->name('projects.members.add');

Route::delete(
    '/projects/{project}/members/{user}',
    [ProjectController::class, 'removeMember']
)->name('projects.members.remove');
