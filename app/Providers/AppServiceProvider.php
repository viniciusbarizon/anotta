<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
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
     */
    public function boot(): void
    {
        $this->configureTable();
        $this->setDefaultForDateTime();
        $this->setDefaultsForModels();
        $this->setDefaultsForPasswords();
    }

    /**
     * Configures the default settings for all Table instances.
     *
     * This method applies a global configuration to the Table component,
     * setting it to use striped rows by default for improved readability.
     */
    private function configureTable(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table->striped();
        });
    }

    /**
     * Sets the default display format for DateTime values in tables.
     *
     * This method configures the default format for displaying DateTime
     * values in the application. The format is set to 'd/m/Y H:i', which
     * represents the day, month, year, hour, and minute.
     *
     * Example format: 25/12/2023 14:30
     */
    private function setDefaultForDateTime(): void
    {
        Table::$defaultDateTimeDisplayFormat = 'd/m/Y H:i';
    }

    /**
     * Sets default configurations for all Eloquent models.
     *
     * - Enables automatic eager loading of relationships for models.
     * - Disables mass assignment protection by unguarding all models.
     */
    private function setDefaultsForModels(): void
    {
        Model::automaticallyEagerLoadRelationships();
        Model::unguard();
    }

    /**
     * Set default password requirements for the application.
     *
     * This method configures the default password validation rules
     * using Laravel's Password validation builder. The rules include:
     * - Minimum length of 12 characters.
     * - At least one letter.
     * - At least one uppercase and one lowercase letter.
     * - At least one number.
     * - At least one symbol.
     * - Ensures the password has not been compromised in known data breaches.
     */
    private function setDefaultsForPasswords(): void
    {
        Password::defaults(fn () => Password::min(12)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised());
    }
}
