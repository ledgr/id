<?php
/**
 * This file is part of the stb package
 *
 * Copyright (c) 2012-13 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\stb\Utils;

use iio\stb\Exception\InvalidStructureException;

/**
 * Modulo11 calculator
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Modulo11
{
    /**
     * Verify that the last digit of nr is a valid check digit
     *
     * @param  string                    $nr
     * @return bool
     * @throws InvalidStructureException If nr is invalid
     */
    public static function verify($nr)
    {
        if (!is_string($nr) || !preg_match("/^[0-9]*X?$/", $nr) || strlen($nr) < 1) {
            throw new InvalidStructureException("Number must consist of characters 0-9 and optionally end with X");
        }

        $weight = 0;
        $pos = strlen($nr);
        $sum = 0;

        while (true) {
            // Set string position
            $pos--;
            if ($pos < 0) {
                break;
            }
            // Set weight
            $weight++;
            if ($weight > 10) {
                $weight = 1;
            }
            // Add to sum
            $n = $nr[$pos];
            if ($n == 'X') {
                $n = 10;
            }
            $sum += $n * $weight;
        }

        // If remainder is 0 check digit is valid
        return $sum % 11 === 0;
    }

    /**
     * Calculate check digit for nr
     *
     * @param  string                    $nr
     * @return string
     * @throws InvalidStructureException If nr is not numerical
     */
    public static function getCheckDigit($nr)
    {
        if (!is_string($nr) || !ctype_digit($nr)) {
            throw new InvalidStructureException("Number must consist of characters 0-9");
        }

        $weight = 1;
        $pos = strlen($nr);
        $sum = 0;

        while (true) {
            // Set string position
            $pos--;
            if ($pos < 0) {
                break;
            }
            // Set weight
            $weight++;
            if ($weight > 10) {
                $weight = 1;
            }
            // Add to sum
            $n = $nr[$pos];
            $sum += $n * $weight;
        }

        // Calculate check digit from remainder
        $rest = $sum % 11;
        $check = 11 - $rest;
        $check = (string)$check;

        if ($check == '10') {
            $check = 'X';
        }

        if ($check == '11') {
            $check = '0';
        }

        return $check;
    }
}
