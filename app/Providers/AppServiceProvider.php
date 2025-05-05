<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
     * Bootstraps application services.
     *
     * - Sets the default date and time display format for tables to 'd/m/Y H:i'.
     * - Configures the default password validation rules:
     *   - Minimum length of 12 characters.
     *   - Must include letters, mixed case, numbers, symbols, and be uncompromised.
     * - Enables automatic eager loading of relationships for Eloquent models.
     *
     * @return void
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
