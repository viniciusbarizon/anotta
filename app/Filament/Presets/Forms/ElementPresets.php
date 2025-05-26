<?php

declare(strict_types=1);

namespace App\Filament\Presets\Forms;

use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;

final class ElementPresets
{
    public static ?string $helperText = null;

    private static DatePicker|Select|TextInput $element;

    private static bool $isRequired;

    private static string $label;

    private static ?string $requiredWith = null;

    /**
     * The validation rules for the form element.
     *
     * This property can be an array of rules, a Closure that returns rules, or null if no rules are set.
     *
     * @var array<string>|Closure|null
     */
    private static array|Closure|null $rules = null;

    /**
     * Creates and configures a DatePicker element with the specified parameters.
     *
     * @param  string  $id  The unique identifier for the DatePicker element.
     * @param  string  $label  The label for the DatePicker element.
     * @param  string  $minDate  The minimum date allowed in the DatePicker.
     * @param  string  $maxDate  The maximum date allowed in the DatePicker.
     * @param  bool  $isRequired  Whether the DatePicker is required. Defaults to true.
     * @return DatePicker The configured DatePicker element.
     */
    public static function datePicker(
        string $id,
        string $label,
        string $minDate,
        string $maxDate,
        bool $isRequired = true
    ): DatePicker {
        self::$element = DatePicker::make($id)
            ->minDate($minDate)
            ->maxDate($maxDate);

        self::$isRequired = $isRequired;
        self::$label = $label;

        self::setRequired();
        self::setLabel();
        self::setValidationAttribute();

        return self::$element;
    }

    /**
     * Creates and configures a Select element with the specified parameters.
     *
     * @param  string  $columnValue  The column value to be used as the option value in the Select.
     * @param  string  $id  The unique identifier for the Select element.
     * @param  string  $label  The label for the Select element.
     * @param  string  $model  The model class from which to retrieve options.
     * @param  Closure|null  $afterStateUpdated  Optional callback to execute after state updates.
     * @param  string|null  $default  Optional default value for the Select element.
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
        ?string $default = null,
        ?string $helperText = null,
        bool $isLive = false,
        bool $isRequired = true,
        ?string $requiredWith = null,
        ?array $rules = null,
    ): Select {
        $options = $model::pluck($columnValue, 'id');

        self::$element = Select::make($id)
            ->options($options)
            ->searchable();

        self::$helperText = $helperText;
        self::$isRequired = $isRequired;
        self::$label = $label;
        self::$requiredWith = $requiredWith;
        self::$rules = $rules;

        self::setAfterStateUpdated(afterStateUpdated: $afterStateUpdated);
        self::setHelperText();
        self::setLive(isLive: $isLive);
        self::setLabel();
        self::setRequired();
        self::setRequiredWith();
        self::setRules();
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
     * @param  string  $label  The label for the TextInput.
     * @param  Closure|int  $maxLength  Maximum length constraint for the input.
     * @param  Closure|null  $extraInputAttributes  Optional additional input attributes.
     * @param  string|null  $helperText  Optional helper text to display below the input.
     * @param  bool  $isAscii  Whether to enforce ASCII-only input. Defaults to false.
     * @param  bool|Closure  $disabled  Whether the input is disabled. Defaults to false.
     * @param  bool  $isRequired  Whether the input is required. Defaults to true.
     * @param  string|null  $requiredWith  Optional field name that makes this input required.
     * @param  array<Closure|string>|Closure|null  $rules  Optional validation rules.
     * @param  array<string,string>|null  $validationMessages  Optional custom validation messages.
     * @return TextInput The configured TextInput element.
     */
    public static function textInput(
        string $id,
        string $label,
        Closure|int $maxLength,
        ?Closure $extraInputAttributes = null,
        ?string $helperText = null,
        bool $isAscii = false,
        bool|Closure $disabled = false,
        bool $isRequired = true,
        ?string $requiredWith = null,
        array|Closure|null $rules = null,
        ?array $validationMessages = null
    ): TextInput {
        self::$element = TextInput::make($id)
            ->maxLength($maxLength);

        self::$helperText = $helperText;
        self::$isRequired = $isRequired;
        self::$label = $label;
        self::$requiredWith = $requiredWith;
        self::$rules = $rules;

        self::setAscii(isAscii: $isAscii);
        self::setDisabled(disabled: $disabled);
        self::setExtraInputAttributes(attributes: $extraInputAttributes);
        self::setHelperText();
        self::setLabel();
        self::setNullable();
        self::setRequired();
        self::setRequiredWith();
        self::setRules();
        self::setValidationAttribute();
        self::setValidationMessages(messages: $validationMessages);

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
        if (is_null($afterStateUpdated)) {
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
     * Sets the disabled state for the current element.
     *
     * This method disables the static `$element` property if the provided
     * parameter is true or a closure that evaluates to true. If the parameter
     * is false, no action is taken.
     *
     * @param  bool|Closure  $disabled  Flag or closure to determine whether
     *                                  the element should be disabled.
     */
    private static function setDisabled(bool|Closure $disabled): void
    {
        if ($disabled === false) {
            return;
        }

        self::$element->disabled($disabled);
    }

    /**
     * Sets extra input attributes for the current element.
     *
     * This method applies additional input attributes to the static `$element` property
     * using the `extraInputAttributes()` method. If the provided closure is null,
     * the method will return early without making any changes.
     *
     * @param  Closure|null  $attributes  A closure that returns an array of extra input attributes,
     *                                    or null to skip setting additional attributes.
     */
    private static function setExtraInputAttributes(?Closure $attributes): void
    {
        if (is_null($attributes)) {
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
        if (is_null(self::$helperText)) {
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
        if (self::$isRequired) {
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
        if (is_null(self::$requiredWith)) {
            return;
        }

        self::$element->requiredWith(self::$requiredWith);
    }

    /**
     * Sets the validation rules for the current element.
     *
     * This method applies the static `$rules` property to the static `$element`
     * using the `rules()` method. If `$rules` is null, the method will return
     * early without making any changes.
     */
    private static function setRules(): void
    {
        if (is_null(self::$rules)) {
            return;
        }

        self::$element->rules(self::$rules);
    }

    /**
     * Sets the default value for a select element based on the provided options.
     *
     * @param  string|null  $default  The default value to set, or null if no default is specified.
     * @param  Collection<string,string>  $options  A collection of options for the select element.
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
     * Sets custom validation messages for the current form element.
     *
     * This method translates and applies custom validation messages
     * to the form element if any messages are provided.
     *
     * @param  array<string,string>|null  $messages  An array of custom validation messages to be translated.
     */
    private static function setValidationMessages(?array $messages): void
    {
        if (is_null($messages)) {
            return;
        }

        $translated = collect($messages)->map(fn (string $message) => __($message))->toArray();

        self::$element->validationMessages($translated);
    }
}
