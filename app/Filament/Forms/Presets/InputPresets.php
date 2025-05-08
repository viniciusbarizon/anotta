<?php

declare(strict_types=1);

namespace App\Filament\Forms\Presets;

use Filament\Forms\Components\TextInput;

final class InputPresets
{
    /**
     * Creates a configured TextInput instance for Filament forms.
     *
     * @param  string  $name  The name of the input field.
     * @param  string|null  $helperText  Optional helper text to display below the input.
     * @param  bool  $isRequired  Whether the input is required. Defaults to true.
     * @param  string|null  $label  Optional label for the input. If null, the label will be translated.
     * @param  int|null  $maxLength  Optional maximum length for the input value.
     * @return TextInput The configured TextInput instance.
     */
    public static function textInput(
        string $name,
        ?string $helperText = null,
        bool $isRequired = true,
        ?string $label = null,
        ?int $maxLength = null
    ): TextInput {
        $input = TextInput::make($name);

        if (is_string($helperText)) {
            $input->helperText(
                str('**É um profissional independente?** Indique o seu nome profissional.')
                    ->inlineMarkdown()
                    ->toHtmlString()
            );
        }

        if ($isRequired) {
            $input->required();
        }

        if (is_null($label)) {
            $input->translateLabel();
        }

        if (is_int($maxLength)) {
            $input->maxLength(100);
        }

        return $input;
    }
}
