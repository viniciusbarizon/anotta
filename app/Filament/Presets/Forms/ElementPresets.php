<?php

declare(strict_types=1);

namespace App\Filament\Presets\Forms;

use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;

final class ElementPresets
{
    /**
     * Creates and configures a DatePicker element with the specified parameters.
     *
     * @param  string  $id  The unique identifier for the DatePicker element.
     * @param  string  $label  The label for the DatePicker element.
     * @param  string  $minDate  The minimum date allowed in the DatePicker.
     * @param  string  $maxDate  The maximum date allowed in the DatePicker.
     * @return DatePicker The configured DatePicker element.
     */
    public static function datePicker(
        string $id,
        string $label,
        string $minDate,
        string $maxDate
    ): DatePicker {
        $label = __($label);

        return DatePicker::make($id)
            ->label($label)
            ->maxDate($maxDate)
            ->minDate($minDate)
            ->required()
            ->validationAttribute($label);
    }

    /**
     * Creates and configures a Select element with the specified parameters.
     *
     * @param  string  $columnValue  The column value to be used as the option value in the Select.
     * @param  string  $id  The unique identifier for the Select element.
     * @param  string  $label  The label for the Select element.
     * @param  string  $model  The model class from which to retrieve options.
     * @param  Closure|null  $afterStateUpdated  Optional callback to execute after state updates.
     * @param  string|null  $helperText  Optional helper text to display below the Select.
     * @param  bool  $isLive  Whether the Select should be live. Defaults to false.
     * @param  bool  $isRequired  Whether the Select is required. Defaults to true.
     * @param  string|null  $requiredWith  Optional field name that makes this input required.
     * @param  array<Closure|string>|null  $rules  Optional validation rules for the Select element.
     * @return Select The configured Select element.
     */
    public static function select(
        string $columnValue,
        string $id,
        string $label,
        string $model,
        ?Closure $afterStateUpdated = null,
        ?string $helperText = null,
        bool $isLive = false,
        bool $isRequired = true,
        ?string $requiredWith = null,
        ?array $rules = null,
    ): Select {
        $label = __($label);
        $options = $model::pluck($columnValue, 'id');

        $select = Select::make($id)
            ->label($label)
            ->options($options)
            ->required($isRequired)
            ->searchable()
            ->validationAttribute($label);

        if ($afterStateUpdated instanceof Closure) {
            $select->afterStateUpdated($afterStateUpdated);
        }

        if ($options->count() === 1) {
            $select->default(
                $options->keys()->first()
            );
        }

        if (is_string($helperText)) {
            $select->helperText(
                self::getHelperText(text: $helperText)
            );
        }

        if ($isLive) {
            $select->live();
        }

        if (is_string($requiredWith)) {
            $select->requiredWith($requiredWith);
        }

        if (is_array($rules)) {
            $select->rules($rules);
        }

        return $select;
    }

    /**
     * Creates a configured TextInput element with various customization options.
     *
     * @param  string  $id  The unique identifier for the TextInput.
     * @param  string  $label  The label for the TextInput.
     * @param  Closure|int  $maxLength  Maximum length constraint for the input.
     * @param  Closure|null  $extraInputAttributes  Optional additional input attributes.
     * @param  string|null  $helperText  Optional helper text to display below the input.
     * @param  bool  $isAscii  Whether to enforce ASCII-only input. Defaults to false.
     * @param  bool|Closure  $disabled  Whether the input is disabled. Defaults to false.
     * @param  bool  $isRequired  Whether the input is required. Defaults to true.
     * @param  string|null  $requiredWith  Optional field name that makes this input required.
     * @param  array<Closure|string>|Closure  $rules  Optional validation rules.
     * @param  array<string,string>|null  $validationMessages  Optional custom validation messages.
     * @return TextInput The configured TextInput element.
     */
    public static function textInput(
        string $id,
        string $label,
        Closure|int $maxLength,
        array|Closure $rules,
        ?Closure $extraInputAttributes = null,
        ?string $helperText = null,
        bool $isAscii = false,
        bool $isMinLengthEqualToMaxLength = false,
        bool|Closure $disabled = false,
        bool $isRequired = true,
        ?string $requiredWith = null,
        ?array $validationMessages = null
    ): TextInput {
        $label = __($label);

        $textInput = TextInput::make($id)
            ->disabled($disabled)
            ->label($label)
            ->maxLength($maxLength)
            ->required($isRequired)
            ->rules($rules)
            ->validationAttribute($label);

        if ($isAscii) {
            $textInput->ascii();
        }

        if ($extraInputAttributes instanceof Closure) {
            $textInput->extraInputAttributes($extraInputAttributes);
        }

        if (is_string($helperText)) {
            $textInput->helperText(
                self::getHelperText(text: $helperText)
            );
        }

        if ($isMinLengthEqualToMaxLength) {
            $textInput->minLength($maxLength);
        }


        if (is_string($requiredWith)) {
            $textInput->requiredWith($requiredWith);
        }

        if (is_array($validationMessages)) {
            $translated = collect($validationMessages)->map(fn (string $message) => __($message))->all();
            $textInput->validationMessages($translated);
        }

        return $textInput;
    }

    /**
     * Returns a formatted HTML string for the helper text.
     *
     * This method takes a string, translates it, converts it to inline Markdown,
     * and returns it as an HTML string. It is used to format helper text for form elements.
     *
     * @param  string  $text  The text to be formatted as helper text.
     * @return HtmlString The formatted HTML string.
     */
    private static function getHelperText(string $text): HtmlString
    {
        return str(__($text))
            ->inlineMarkdown()
            ->toHtmlString();
    }
}
