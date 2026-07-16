<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/up'); // Redirect to health status or a simple API message
});

Route::get('/up', function () {
    return response()->json(['status' => 'API is running', 'version' => Application::VERSION]);
});
