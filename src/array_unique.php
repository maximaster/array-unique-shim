<?php

declare(strict_types=1);

namespace Maximaster\PhpShim;

use function array_unique as php_array_unique;

/**
 * Fixes https://github.com/php/doc-en/issues/1463.
 *
 * @template InputArray of array
 *
 * @psalm-param InputArray $elements
 * @psalm-return InputArray
 */
function array_unique(array $elements, int $flags = SORT_STRING): array
{
    if ($flags !== SORT_REGULAR) {
        return php_array_unique($elements, $flags);
    }

    $unique = php_array_unique($elements, $flags);

    // infinite loop protection
    $giveUpAfter = 10;
    do {
        $totallyUnique = php_array_unique($unique, $flags);
        if (count($unique) === count($totallyUnique)) {
            return $unique;
        }

        $unique = $totallyUnique;
    } while (--$giveUpAfter > 0);

    return $unique;
}
