<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebController::class, 'index'])->name('index');
Route::post('/lang', [WebController::class, 'langSelect'])->name('lang');
Route::post('/checkreferance', [WebController::class, 'checkreferance'])->name('patient.search');
Route::get('/view/{hn}', [WebController::class, 'viewAppointment'])->name('patient.appointment');
Route::get('/new/{hn}/{type}', [WebController::class, 'newAppointment'])->name('appointment.new');

Route::post('/create', [WebController::class, 'AppointmentCreate'])->name('appointment.create');
Route::post('/delete', [WebController::class, 'AppointmentDelete'])->name('appointment.delete');
