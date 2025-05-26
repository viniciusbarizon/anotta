<?php

declare(strict_types=1);

namespace App\Filament\Presets\Tables;

use Filament\Tables\Columns\TextColumn;

final class ColumnPresets
{
    /**
     * Creates a preset for a text column with optional configurations.
     *
     * @param  string  $name  The name of the column.
     * @param  bool  $isCopyable  Indicates whether the column is copyable. Defaults to false.
     * @param  bool  $isVisible  Indicates whether the column is visible. Defaults to true.
     * @param  string|null  $label  An optional label for the column. If null, the label will be translated.
     * @return TextColumn The configured text column instance.
     */
    public static function text(
        string $name,
        bool $isCopyable = false,
        bool $isSortable = true,
        bool $isVisible = true,
        ?string $label = null
    ): TextColumn {
        $textColumn = TextColumn::make($name)
            ->searchable()
            ->visible($isVisible);

        if ($isCopyable) {
            $textColumn->copyable();
        }

        if ($isSortable) {
            $textColumn->sortable();
        }

        if (is_null($label)) {
            return $textColumn->translateLabel();
        }

        return $textColumn->label($label);
    }

    /**
     * Creates a preset for a text column with date-time formatting.
     *
     * This method generates a `TextColumn` with the specified name and label,
     * and applies additional formatting for date-time display, including a tooltip
     * and a "since" indicator.
     *
     * @param  string  $name  The name of the column.
     * @param  string  $label  The label to display for the column.
     * @return TextColumn The configured text column with date-time formatting.
     */
    public static function dateTime(string $name, string $label): TextColumn
    {
        return self::text(name: $name, label: $label)
            ->dateTimeTooltip()
            ->since();
    }
}
