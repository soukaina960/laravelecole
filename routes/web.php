<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/attestations/{id}/download', [StudentController::class, 'generateAttestation']);
