<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('filament.medical.auth.login'));
