<?php declare(strict_types=1);

namespace Rector\DoctrinePhpDocParser\Array_;

/**
 * Helpers class for ordering items in values objects on call.
 * Beware of static methods as they might doom you on the edge of life.
 */
final class ArrayItemStaticHelper
{
    /**
     * @param string[] $items
     * @return string[]
     */
    public static function removeItemFromArray(array $items, string $itemToRemove): array
    {
        $itemPosition = array_search($itemToRemove, $items, true);
        if ($itemPosition !== null) {
            unset($items[$itemPosition]);
        }

        return $items;
    }

    /**
     * @param string[] $contentItems
     * @param string[] $orderedVisibleItems
     * @return string[]
     */
    public static function filterAndSortVisibleItems(array $contentItems, array $orderedVisibleItems): array
    {
        // 1. remove unused items
        foreach (array_keys($contentItems) as $key) {
            if (in_array($key, $orderedVisibleItems, true)) {
                continue;
            }

            unset($contentItems[$key]);
        }

        return self::sortItemsByOrderedListOfKeys($contentItems, $orderedVisibleItems);
    }

    /**
     * 2. sort item by prescribed key order
     * @see https://www.designcise.com/web/tutorial/how-to-sort-an-array-by-keys-based-on-order-in-a-secondary-array-in-php
     * @param string[] $contentItems
     * @param string[] $orderedVisibleItems
     * @return string[]
     */
    private static function sortItemsByOrderedListOfKeys(array $contentItems, array $orderedVisibleItems): array
    {
        uksort($contentItems, function ($firstContentItem, $secondContentItem) use ($orderedVisibleItems): int {
            $firstItemPosition = array_search($firstContentItem, $orderedVisibleItems, true);
            $secondItemPosition = array_search($secondContentItem, $orderedVisibleItems, true);

            return $firstItemPosition <=> $secondItemPosition;
        });

        return $contentItems;
    }
}
