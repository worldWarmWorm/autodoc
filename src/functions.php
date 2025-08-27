<?php

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('arrayToYaml')) {
    /**
     * @param array<string|int, mixed> $array
     */
    function arrayToYaml(array $array, int $indent = 0): string
    {
        $yaml = '';
        $indentStr = str_repeat('  ', $indent);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (is_int($key)) {
                    $yaml .= arrayToYaml($value, $indent);
                } else {
                    $yaml .= "$indentStr$key:\n" . arrayToYaml($value, $indent + 1);
                }
            } else {
                if (is_bool($value)) {
                    $valueStr = $value ? 'true' : 'false';
                } elseif (is_null($value)) {
                    $valueStr = 'null';
                } elseif (is_numeric($value)) {
                    $valueStr = $value;
                } else {
                    $valueStr = '"' . addslashes($value instanceof ReflectionMethod ? $value->getName() : $value) . '"';
                }
                if (is_int($key)) {
                    $yaml .= "$indentStr- $valueStr\n";
                } else {
                    $yaml .= "$indentStr$key: $valueStr\n";
                }
            }
        }

        return $yaml;
    }
}
