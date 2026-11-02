<?php

namespace App\View;

use DateTimeInterface;

/**
 * Wertauflösung für die Formular-Komponenten (resources/views/components/form),
 * nach dem Vorbild von laravelcollective/html: old() vor explizitem Wert vor Model-Attribut.
 */
class FormValue
{
    /**
     * Feldname als Punkt-Schlüssel: "orders[1][name]" → "orders.1.name", "users[]" → "users".
     */
    public static function key(string $name): string
    {
        return trim(str_replace(['[]', '[', ']'], ['', '.', ''], $name), '.');
    }

    public static function resolve(string $key, mixed $value, mixed $model, string $type = 'text'): mixed
    {
        $old = old($key);
        if (! is_null($old)) {
            return $old;
        }
        $current = $value ?? data_get($model, $key);

        if ($current instanceof DateTimeInterface) {
            return match ($type) {
                'date' => $current->format('Y-m-d'),
                'time' => $current->format('H:i'),
                'datetime-local' => $current->format('Y-m-d\TH:i'),
                default => $current,
            };
        }

        return $current;
    }
}
