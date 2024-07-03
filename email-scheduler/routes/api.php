<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\MailController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\EmailBatchController;

Route::post('/profiles', 'App\Http\Controllers\API\ProfileController@store');
Route::post('/mails', 'App\Http\Controllers\API\MailController@store');


