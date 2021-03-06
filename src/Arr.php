<?php

/*
 * This file is part of the PHP Array Utils package.
 *
 * (c) Prince Dorcis <princedorcis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prinx;

/**
 * Array Utilities class.
 *
 * @author Prince Dorcis <princedorcis@gmail.com>
 */
class Arr
{
    /**
     * Get a deep value from a recursive array.
     *
     * @param string $key   The key follows the depth of the array to retrieve.
     * @param array  $array The array the value will be got from
     * @param string $sep   The key will be exploded according to the separator. The separator marks each time a new depth in the array
     *
     * @return mixed
     */
    public static function multiKeyGet($key, $array, $sep = '.')
    {
        $lookup = $array;
        $exploded = explode($sep, $key);

        if (count($exploded) === 1) {
            return $array[$key] ?? null;
        }

        foreach ($exploded as $value) {
            if (!is_array($lookup) || !isset($lookup[$value])) {
                return null;
            }

            $lookup = $lookup[$value];
        }

        return $lookup;
    }

    /**
     * Remove a deep value from a recursive array.
     *
     * @param string $key   The key follows the depth of the array.
     * @param array  $array The array the value will be removed from
     * @param string $sep   The key will be exploded according to the separator. The separator marks each time a new depth in the array
     *
     * @return array The modified array
     */
    public static function multiKeyRemove($key, $array, $sep = '.')
    {
        $exploded = explode($sep, $key);
        $keyToUnset = array_pop($exploded);
        $toUnsetContainerKey = implode('.', $exploded);
        $toUnsetContainer = self::multiKeyGet($toUnsetContainerKey, $array, $sep);

        if (null === $toUnsetContainer || !is_array($toUnsetContainer)) {
            return $array;
        }

        if (isset($toUnsetContainer[$keyToUnset])) {
            // unset($toUnsetContainer[$keyToUnset]);
            $toUnsetContainer[$keyToUnset] = null;
        }

        // print_r(self::multiKeySet($toUnsetContainerKey, $toUnsetContainer, $array, $sep));

        return self::multiKeySet($toUnsetContainerKey, $toUnsetContainer, $array, $sep);
    }

    /**
     * Set a deep value from in a recursive array.
     *
     * @param string $key   The key follows the depth of the array to retrieve.
     * @param mixed  $value The value to set
     * @param array  $array The array in which to set the value
     * @param string $sep   The key will be exploded according to the separator. The separator marks each time a new depth in the array
     *
     * @return array The modified array
     */
    public static function multiKeySet($key, $value, $array, $sep = '.')
    {
        $exploded = explode($sep, $key);
        $depth = count($exploded);

        if (1 === $depth) {
            $array[$key] = $value;

            return $array;
        }

        $toAdd = [
            $exploded[$depth - 1] => $value,
        ];

        for ($i = $depth - 2; $i >= 0; $i--) {
            $toAdd = [
                $exploded[$i] => $toAdd,
            ];
        }

        return array_replace_recursive($array, $toAdd);
    }
}
