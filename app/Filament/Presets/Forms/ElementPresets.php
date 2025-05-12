<?php

declare(strict_types=1);

namespace App\Filament\Presets\Forms;

use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class ElementPresets
{
    public static ?string $helperText;

    private static DatePicker|Select|TextInput $element;

    private static bool $isRequired;

    private static string $label;

    private static ?string $requiredWith;

    private static ?array $rules;

    private static ?array $validationMessages;

    /**
     * Creates a configured DatePicker element for a form.
     *
     * @param  string  $id  The unique identifier for the DatePicker element.
     * @param  string  $afterOrEqual  The date from which the selected date must be after or equal to.
     * @param  string  $beforeOrEqual  The date until which the selected date must be before or equal to.
     * @param  string  $label  The label for the DatePicker element.
     * @param  Carbon  $minDate  The minimum allowed date.
     * @param  Carbon  $maxDate  The maximum allowed date.
     * @param  array  $validationMessages  Custom validation error messages.
     * @param  bool  $isRequired  Whether the DatePicker element is required. Defaults to true.
     * @return DatePicker The configured DatePicker element.
     */
    public static function datePicker(
        string $id,
        string $afterOrEqual,
        string $beforeOrEqual,
        string $label,
        Carbon $minDate,
        Carbon $maxDate,
        array $validationMessages,
        bool $isRequired = true
    ): DatePicker {
        self::$element = DatePicker::make($id)
            ->afterOrEqual($afterOrEqual)
            ->beforeOrEqual($beforeOrEqual)
            ->minDate($minDate)
            ->maxDate($maxDate);

        self::$isRequired = $isRequired;
        self::$label = $label;
        self::$validationMessages = $validationMessages;

        self::setRequired();
        self::setLabel();
        self::setValidationAttribute();
        self::setValidationMessages();

        return self::$element;
    }

    /**
     * Creates and configures a Select element with the specified parameters.
     *
     * @param  string  $columnValue  The column to use for the select options' display text.
     * @param  string  $id  The unique identifier for the Select element.
     * @param  string  $label  The label for the Select element.
     * @param  string  $model  The Eloquent model to fetch options from.
     * @param  Closure|null  $afterStateUpdated  Optional callback to execute after the select state is updated.
     * @param  string|null  $default  Optional default value for the select.
     * @param  string|null  $helperText  Optional helper text to display below the select field.
     * @param  bool  $isLive  Whether the select should update dynamically. Defaults to false.
     * @param  bool  $isRequired  Whether the select is required. Defaults to true.
     * @param  string|null  $requiredWith  Optional field name that makes this select required when the specified field is present.
     * @return Select The configured Select element.
     */
    public static function select(
        string $columnValue,
        string $id,
        string $label,
        string $model,
        ?Closure $afterStateUpdated = null,
        ?string $default = null,
        ?string $helperText = null,
        bool $isLive = false,
        bool $isRequired = true,
        ?string $requiredWith = null
    ): Select {
        $options = $model::pluck($columnValue, 'id');

        self::$element = Select::make($id)
            ->options($options)
            ->searchable();

        self::$helperText = $helperText;
        self::$isRequired = $isRequired;
        self::$label = $label;
        self::$requiredWith = $requiredWith;

        self::setAfterStateUpdated(afterStateUpdated: $afterStateUpdated);
        self::setHelperText();
        self::setLive(isLive: $isLive);
        self::setLabel();
        self::setRequired();
        self::setRequiredWith();
        self::setValidationAttribute();

        self::setSelectDefault(
            default: $default,
            options: $options
        );

        return self::$element;
    }

    /**
     * Creates a configured TextInput element with various customization options.
     *
     * @param  string  $id  The unique identifier for the TextInput.
     * @param  string  $label  The label for the TextInput field.
     * @param  Closure|int  $maxLength  Maximum length constraint for the input.
     * @param  array  $rules  Validation rules for the input.
     * @param  Closure|null  $extraInputAttributes  Optional additional input attributes.
     * @param  string|null  $helperText  Optional helper text to display below the input.
     * @param  bool  $isAscii  Whether to enforce ASCII-only input. Defaults to false.
     * @param  bool|Closure  $isDisabled  Whether the input is disabled. Defaults to false.
     * @param  bool  $isRequired  Whether the input is required. Defaults to true.
     * @param  string|null  $requiredWith  Optional field name that makes this input required when specified.
     * @return TextInput The configured TextInput element.
     */
    public static function textInput(
        string $id,
        string $label,
        Closure|int $maxLength,
        array $rules,
        ?Closure $extraInputAttributes = null,
        ?string $helperText = null,
        ?bool $isAscii = false,
        bool|Closure $isDisabled = false,
        bool $isRequired = true,

        ?string $requiredWith = null
    ): TextInput {
        self::$element = TextInput::make($id)
            ->disabled($isDisabled)
            ->maxLength($maxLength)
            ->string();

        self::$helperText = $helperText;
        self::$isRequired = $isRequired;
        self::$label = $label;
        self::$requiredWith = $requiredWith;
        self::$rules = $rules;

        self::setAscii(isAscii: $isAscii);
        self::setExtraInputAttributes(attributes: $extraInputAttributes);
        self::setHelperText();
        self::setLabel();
        self::setNullable();
        self::setRequired();
        self::setRequiredWith();
        self::setRules();
        self::setValidationAttribute();

        return self::$element;
    }

    /**
     * Sets the "after state updated" callback for the element.
     *
     * This method assigns a callback function to be executed after the state
     * of the element is updated. If the provided callback is null, the method
     * will return without making any changes.
     *
     * @param  Closure|null  $afterStateUpdated  The callback to be executed after
     *                                           the state is updated, or null to skip.
     */
    private static function setAfterStateUpdated(?Closure $afterStateUpdated): void
    {
        if (is_null($afterStateUpdated) === true) {
            return;
        }

        self::$element->afterStateUpdated($afterStateUpdated);
    }

    /**
     * Sets the ASCII validation for the element.
     *
     * This method enables ASCII-only validation for the element if the
     * provided parameter is true. If false, no action is taken.
     *
     * @param  bool  $isAscii  Flag to determine whether to apply ASCII validation.
     */
    private static function setAscii(bool $isAscii): void
    {
        if ($isAscii === false) {
            return;
        }

        self::$element->ascii();
    }

    /**
     * Sets extra input attributes for the element.
     *
     * This method applies additional input attributes to the element if a closure
     * providing those attributes is provided. If the attributes closure is null,
     * no action is taken.
     *
     * @param  Closure|null  $attributes  A closure that returns an array of extra input attributes,
     *                                    or null to skip setting attributes.
     */
    private static function setExtraInputAttributes(?Closure $attributes): void
    {
        if (is_null($attributes) === true) {
            return;
        }

        self::$element->extraInputAttributes($attributes);
    }

    /**
     * Sets the helper text for the current element if it is defined.
     *
     * This method checks if the static property `$helperText` is not null.
     * If it is defined, it applies the helper text to the static property
     * `$element` using the `helperText` method. The helper text is processed
     * by translating it, converting it to inline Markdown, and then converting
     * it to an HTML string.
     */
    private static function setHelperText(): void
    {
        if (is_null(self::$helperText) === true) {
            return;
        }

        self::$element->helperText(
            str(__(self::$helperText))
                ->inlineMarkdown()
                ->toHtmlString()
        );
    }

    /**
     * Sets the label for the current element.
     *
     * This method applies a translated label to the static `$element` property
     * using the `label()` method. The label is retrieved from the static `$label`
     * property and passed through the translation function.
     */
    private static function setLabel(): void
    {
        self::$element->label(
            __(self::$label)
        );
    }

    /**
     * Sets the "live" state of the element.
     *
     * If the provided parameter is `false`, the method will return early
     * without making any changes. Otherwise, it will enable the "live"
     * state on the element.
     *
     * @param  bool  $isLive  Determines whether the element should be set to "live".
     */
    private static function setLive(bool $isLive): void
    {
        if ($isLive === false) {
            return;
        }

        self::$element->live();
    }

    /**
     * Sets the element as nullable if it is not required.
     *
     * This method checks the static `$isRequired` property. If it is set to `true`,
     * the method exits without making any changes. Otherwise, it calls the `nullable()`
     * method on the static `$element` property to mark the element as nullable.
     */
    private static function setNullable(): void
    {
        if (self::$isRequired === true) {
            return;
        }

        self::$element->nullable();
    }

    /**
     * Sets the current element as required if the `$isRequired` property is true.
     *
     * This method checks the static `$isRequired` property. If it is set to `true`,
     * the `required()` method is called on the static `$element` property to mark
     * the element as required. If `$isRequired` is `false`, the method exits without
     * making any changes.
     */
    private static function setRequired(): void
    {
        if (self::$isRequired === false) {
            return;
        }

        self::$element->required();
    }

    /**
     * Sets the "requiredWith" property on the element if it is enabled.
     *
     * This method checks if the static property `$requiredWith` is not set to `false`.
     * If it is enabled, it applies the `requiredWith` rule to the static `$element`
     * using the value of `$requiredWith`.
     */
    private static function setRequiredWith(): void
    {
        if (is_null(self::$requiredWith) === true) {
            return;
        }

        self::$element->requiredWith(self::$requiredWith);
    }

    /**
     * Sets validation rules for the current form element.
     *
     * This method applies custom validation rules to the element if they are defined.
     * If no validation rules are set (null), the method returns without making any changes.
     */
    private static function setRules(): void
    {
        if (is_null(self::$rules) === true) {
            return;
        }

        self::$element->rules(self::$rules);
    }

    /**
     * Sets the default value for a select element based on the provided options.
     *
     * @param  string|null  $default  The default value to set, or null if no default is specified.
     * @param  Collection  $options  A collection of options for the select element.
     */
    private static function setSelectDefault(?string $default, Collection $options): void
    {
        if (is_null($default) === false) {
            self::$element->default(
                $options->search($default)
            );

            return;
        }

        if ($options->count() > 1) {
            return;
        }

        self::$element->default(
            $options->keys()->first()
        );
    }

    /**
     * Sets a custom validation attribute label for the current form element.
     *
     * This method applies a translated label as the validation attribute,
     * which is typically used in error messages to provide a more readable
     * field name during form validation.
     */
    private static function setValidationAttribute(): void
    {
        self::$element->validationAttribute(
            __(self::$label)
        );
    }

    /**
     * Sets validation messages for the current form element.
     *
     * This method applies custom validation messages to the element if they are defined.
     * If no validation messages are set (null), the method returns without making any changes.
     */
    private static function setValidationMessages(): void
    {
        if (is_null(self::$validationMessages) === true) {
            return;
        }

        self::$element->validationMessages(
            self::$validationMessages
        );
    }
}
