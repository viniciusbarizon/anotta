<?php

declare(strict_types=1);

namespace App\Filament\Presets\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;

final class ElementPresets
{
    public static ?string $helperText;

    private static Select|TextInput $element;

    private static bool $isRequired;

    private static ?string $label;

    /**
     * Creates a Filament Select form element with specified configurations.
     *
     * @param  string  $columnValue  The column name to be used as the value for the options.
     * @param  string  $id  The unique identifier for the select element.
     * @param  string  $label  The label for the select element.
     * @param  string  $model  The model class to fetch the options from.
     * @param  string|null  $default  The default value for the select element (optional).
     * @param  string|null  $helperText  The helper text to display below the select element (optional).
     * @param  bool  $isRequired  Whether the select element is required (default: true).
     * @return Select The configured Filament Select form element.
     */
    public static function select(
        string $columnValue,
        string $id,
        string $label,
        string $model,
        ?string $default = null,
        ?string $helperText = null,
        bool $isLive = false,
        bool $isRequired = true
    ): Select {
        $options = $model::pluck($columnValue, 'id');

        self::$element = Select::make($id)
            ->options($options)
            ->searchable();

        self::$helperText = $helperText;
        self::$isRequired = $isRequired;
        self::$label = $label;

        self::setHelperText();
        self::setLive(isLive: $isLive);
        self::setLabel();
        self::setRequired();

        self::setSelectDefault(
            default: $default,
            options: $options
        );

        return self::$element;
    }

    /**
     * Creates a configured TextInput element.
     *
     * @param  string  $id  The unique identifier for the TextInput element.
     * @param  int  $maxLength  The maximum length of the input value.
     * @param  string|null  $helperText  Optional helper text to display below the input field.
     * @param  bool  $isRequired  Whether the input field is required (default: true).
     * @param  string|null  $label  Optional label for the input field.
     * @return TextInput The configured TextInput element.
     */
    public static function textInput(
        string $id,
        ?int $maxLength = null,
        ?string $helperText = null,
        bool $isRequired = true,
        ?string $label = null,
    ): TextInput {
        self::$element = TextInput::make($id)
            ->maxLength($maxLength);

        self::$helperText = $helperText;
        self::$isRequired = $isRequired;
        self::$label = $label;

        self::setHelperText();
        self::setLabel();
        self::setRequired();

        return self::$element;
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
     * @param bool $isLive Determines whether the element should be set to "live".
     * @return void
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
