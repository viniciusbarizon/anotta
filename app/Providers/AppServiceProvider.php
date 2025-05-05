<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Model;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Table::$defaultDateTimeDisplayFormat = 'd/m/Y H:i';

        Password::defaults(function () {
            return Password::min(12)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        Model::automaticallyEagerLoadRelationships();
    }
}
