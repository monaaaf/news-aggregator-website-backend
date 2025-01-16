<?php

namespace App\Traits;

trait EnumToArray {
    public static function labels(): array {
        $enumCases = self::cases();

        $names = [];

        foreach ($enumCases as $enumCase) {
            $names[] = $enumCase->label();
        }

        return $names;
    }

    public static function values($exclude = []): array {
        // this will retrieve the values of the enum as an array
        // this is mostly used in migrations
        $values = array_column(self::cases(), 'value');

        return !empty($exclude) ? array_diff($values, $exclude) : $values;
    }

    public static function valueToIdMap(): array {
        return [];
    }

    public static function getInstance($case, $withId = false): ?array {
        if (is_null($case)) return null;

        if ($withId) $valueToIdMap = self::valueToIdMap();

        return [
            'id'   => $withId ? $valueToIdMap[$case->value] : $case->value,
            'name' => $case->label(),
        ];
    }

    public static function tryFromId(int|string $id): ?object {
        $valueToIdMap = self::valueToIdMap();

        $value = array_search($id, $valueToIdMap);

        return ($value !== false) ? self::from($value) : null;
    }

    public static function toArray($withId = false, $exclude = []): array {
        $array = [];

        foreach (self::cases() as $case) {
            if (!in_array($case, $exclude)) {
                $array[] = self::getInstance($case, $withId);
            }
        }

        return $array;
    }
}
