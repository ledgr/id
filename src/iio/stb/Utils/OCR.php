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
use iio\stb\Exception\InvalidLengthDigitException;
use iio\stb\Exception\InvalidCheckDigitException;

/**
 * OCR number generation and validation
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class OCR
{
    /**
     * @var string Internal ocr representation
     */
    private $ocr = '';

    /**
     * OCR number generation and validation
     * 
     * OCR must have a valid check and length digits
     *
     * @param  string                      $ocr
     * @throws InvalidStructureException   If ocr is not numerical or longer than 25 digits
     * @throws InvalidLengthDigitException If length digit is invalid
     * @throws InvalidCheckDigitException  If check digit is invalid
     */
    public function __construct($ocr)
    {
        // Validate length
        if (!is_string($ocr)
            || !ctype_digit($ocr)
            || strlen($ocr) > 25
            || strlen($ocr) < 2
        ) {
            $msg = "\$ocr must be numeric and contain between 2 and 25 digits";
            throw new InvalidStructureException($msg);
        }

        $arOcr = str_split($ocr);
        $check = array_pop($arOcr);
        $length = array_pop($arOcr);
        $base = implode('', $arOcr);

        // Validate length digit
        if ($length != self::calcLengthDigit($base)) {
            $msg = "Invalid length digit";
            throw new InvalidLengthDigitException($msg);
        }

        // Validate check digit
        $modulo = new Modulo10();
        if ($check != $modulo->getCheckDigit($base.$length)) {
            $msg = "Invalid check digit";
            throw new InvalidCheckDigitException($msg);
        }

        $this->ocr = $ocr;
    }

    /**
     * Get OCR as string
     *
     * @return string
     */
    public function getOCR()
    {
        return $this->ocr;
    }

    /**
     * Get OCR as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getOCR();
    }

    /**
     * Create OCR from number
     * 
     * Check and length digits are appended
     *
     * @param  string                    $nr
     * @return OCR
     * @throws InvalidStructureException If $nr is invalid
     */
    public static function create($nr)
    {
        if (!is_string($nr) || !ctype_digit($nr) || strlen($nr) > 23) {
            $msg = "\$nr must be numeric and contain a maximum of 23 digits";
            throw new InvalidStructureException($msg);
        }

        // Calculate and append length digit
        $nr .= self::calcLengthDigit($nr);

        // Calculate and append check digit
        $modulo = new Modulo10();
        $nr .= $modulo->getCheckDigit($nr);

        return new OCR($nr);
    }

    /**
     * Calculate length digit for string
     *
     * The length of $nr plus 2 is used, to take length and check digits into
     * account.
     *
     * @param  $nr
     * @return string
     */
    private static function calcLengthDigit($nr)
    {
        $length = strlen($nr) + 2;
        $length = $length % 10;

        return (string)$length;
    }
}
