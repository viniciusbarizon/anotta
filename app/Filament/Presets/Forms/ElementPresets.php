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

    private static ?string $label;

    private static ?string $requiredWith;

    /**
     * Creates and configures a DatePicker form element.
     *
     * @param  string  $id  The unique identifier for the DatePicker element.
     * @param  string  $label  The label to display for the DatePicker element.
     * @param  bool  $isRequired  Indicates whether the DatePicker element is required. Defaults to true.
     * @return DatePicker The configured DatePicker instance.
     */
    public static function datePicker(
        string $id,
        string $label,
        Carbon $minDate,
        Carbon $maxDate,
        bool $isRequired = true
    ): DatePicker {
        self::$element = DatePicker::make($id)
            ->beforeOrEqual('today')
            ->minDate($minDate)
            ->maxDate($maxDate);

        self::$isRequired = $isRequired;
        self::$label = $label;

        self::setRequired();
        self::setLabel();

        return self::$element;
    }

    /**
     * Creates a configured Select element for a form.
     *
     * @param  string  $columnValue  The column name to be used as the value for the options.
     * @param  string  $id  The unique identifier for the Select element.
     * @param  string  $label  The label for the Select element.
     * @param  string  $model  The model class used to fetch the options.
     * @param  Closure|null  $afterStateUpdated  A callback to be executed after the state is updated.
     * @param  string|null  $default  The default value for the Select element.
     * @param  string|null  $helperText  The helper text to display below the Select element.
     * @param  bool  $isLive  Whether the Select element should update live.
     * @param  bool  $isRequired  Whether the Select element is required.
     * @param  string|null  $requiredWith  The field that makes this Select element required when filled.
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

        self::setSelectDefault(
            default: $default,
            options: $options
        );

        return self::$element;
    }

    /**
     * Creates and configures a TextInput element with the specified parameters.
     *
     * @param  string  $id  The unique identifier for the TextInput element.
     * @param  Closure|int  $maxLength  The maximum length of the input value, or a Closure to dynamically determine it.
     * @param  string|null  $helperText  Optional helper text to display below the input field.
     * @param  bool|Closure  $isDisabled  Whether the input field is disabled, or a Closure to dynamically determine it.
     * @param  bool  $isRequired  Whether the input field is required. Defaults to true.
     * @param  string|null  $label  Optional label for the input field.
     * @param  string|null  $requiredWith  Optional field name that makes this input required when the specified field is present.
     * @return TextInput The configured TextInput element.
     */
    public static function textInput(
        string $id,
        Closure|int $maxLength,
        ?Closure $extraInputAttributes = null,
        ?string $helperText = null,
        ?bool $isAscii = false,
        bool|Closure $isDisabled = false,
        bool $isRequired = true,
        ?string $label = null,
        ?string $requiredWith = null
    ): TextInput {
        self::$element = TextInput::make($id)
            ->disabled($isDisabled)
            ->maxLength($maxLength);

        self::$helperText = $helperText;
        self::$isRequired = $isRequired;
        self::$label = $label;
        self::$requiredWith = $requiredWith;

        self::setAscii(isAscii: $isAscii);
        self::setExtraInputAttributes(attributes: $extraInputAttributes);
        self::setHelperText();
        self::setLabel();
        self::setRequired();
        self::setRequiredWith();

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
     * Sets the label for the element.
     *
     * If the static property `$label` is null, it calls the `translateLabel` method
     * on the `$element` to automatically translate the label. Otherwise, it explicitly
     * sets the label of the `$element` using the value of `$label`.
     */
    private static function setLabel(): void
    {
        if (is_null(self::$label) === true) {
            self::$element->translateLabel();

            return;
        }

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
}
