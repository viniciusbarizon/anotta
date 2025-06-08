<?php

declare(strict_types=1);

namespace App\Filament\Presets\Tables;

use App\Models\User;
use Closure;
use Filament\Tables\Columns\TextColumn;

final class ColumnPresets
{
    /**
     * Creates a preset for displaying the 'created_at' timestamp as a formatted date and time column.
     *
     * @return TextColumn The configured date and time column for the 'created_at' field with the label 'Criado'.
     */
    public static function createdAt(): TextColumn
    {
        return self::dateTime(
            column: 'created_at',
            label: 'Criado'
        );
    }

    /**
     * Creates a date column preset for a table.
     *
     * This method returns a `TextColumn` configured to display a date value with a tooltip and a "since" indicator.
     *
     * @param string $column      The name of the column to display.
     * @param string $label       The label to use for the column header.
     * @param bool   $isSortable  Whether the column should be sortable. Defaults to true.
     *
     * @return TextColumn         The configured date column.
     */
    public static function date(string $column, string $label, bool $isSortable = true): TextColumn
    {
        return self::text(
            column: $column,
            isSortable: $isSortable,
            label: $label
        )
            ->dateTooltip()
            ->since();
    }

    /**
     * Creates a TextColumn configured to display a date and time with a tooltip and "since" formatting.
     *
     * @param  string  $column  The name of the column to display.
     * @param  string  $label  The label to use for the column.
     * @return TextColumn The configured TextColumn instance with date and time tooltip and relative "since" formatting.
     */
    public static function dateTime(string $column, string $label): TextColumn
    {
        return self::text(
            column: $column,
            label: $label
        )
            ->dateTimeTooltip()
            ->since();
    }

    /**
     * Creates a preset for the 'id' column as a text column in a table.
     *
     * - The column is copyable.
     * - The column is not sortable.
     * - The column is only visible to super admin users.
     * - The column label is set to 'ID'.
     * - The displayed value is limited to 7 characters.
     *
     * @return TextColumn The configured text column for the 'id' field.
     */
    public static function id(): TextColumn
    {
        /** @var User $user */
        $user = auth()->user();

        return self::text(
            column: 'id',
            isCopyable: true,
            isSortable: false,
            label: 'ID',
            limit: 7,
            visible: $user->isImpersonated(),
        )->visibleFrom('md');
    }

    /**
     * Creates a TextColumn with customizable options for displaying text data in a table.
     *
     * @param  string  $column  The name of the column to display.
     * @param  bool  $isCopyable  Whether the column is copyable.
     * @param  bool  $isSortable  Whether the column is sortable.
     * @param  string|null  $label  The label for the column, if any.
     * @param  int|null  $limit  The character limit for the text, if any.
     * @param  array<string>|bool  $searchable  A closure to define custom search behavior, if any.
     * @param  bool|Closure  $visible  Whether the column is visible, can be a boolean or a closure.
     * @return TextColumn The configured TextColumn instance.
     */
    public static function text(
        string $column,
        bool $isCopyable = false,
        bool $isSortable = true,
        ?string $label = null,
        ?int $limit = null,
        array|bool $searchable = true,
        bool|Closure $visible = true,
    ): TextColumn {
        $textColumn = TextColumn::make($column)
            ->searchable($searchable)
            ->visible($visible);

        if ($isCopyable) {
            $textColumn->copyable();
        }

        if (is_int($limit)) {
            $textColumn->limit($limit);
        }

        if ($isSortable) {
            $textColumn->sortable();
        }

        if (is_null($label)) {
            return $textColumn->translateLabel();
        }

        return $textColumn->label(__($label));
    }
}
