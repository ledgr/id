<?php
/**
 * This file is part of the STB package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 *
 * @package STB\ID
 */
namespace itbz\STB\ID;
use itbz\STB\Utils\Modulo10;
use itbz\STB\Exception\InvalidStructureException;
use itbz\STB\Exception\InvalidCheckDigitException;


/**
 * Swedish corporate identity numbers
 *
 * @package STB\ID
 */
class CorporateId
{

    /**
     * Group number
     *
     * @var string
     */
    private $_groupNr = '';


    /**
     * Serial number in tow parts, pre and post delimiter
     *
     * @var array
     */
    private $_serialNr = array('', '');


    /**
     * Check digit
     *
     * @var string
     */
    private $_check = '';


    /**
     * Construct and set id number
     *
     * @param string $id
     */
    public function __construct($id = '')
    {
        if ($id) {
            $this->setId($id);
        }
    }


    /**
     * Set id number
     *
     * @param string $id
     *
     * @return void
     *
     * @throws InvalidStructureException if structure is invalid
     *
     * @throws InvalidCheckDigitException if check digit is invalid
     */
    public function setId($id)
    {
        assert('is_string($id)');

        // Validate form
        $split = preg_split("/([-])/", $id, 2, PREG_SPLIT_DELIM_CAPTURE);
        if (
            count($split) != 3
            || strlen($split[0]) != 6
            || strlen($split[2]) != 4
            || !ctype_digit($split[0])
            || !ctype_digit($split[2])
        ) {
            $msg = 'IDs must use form NNNNNN-NNNN';
            throw new InvalidStructureException($msg);
        }

        // Validate 3rd digit
        if ($split[0][2] < 2) {
            $msg = "Third digit must be at lest 2";
            throw new InvalidStructureException($msg);
        }

        // Validate check digit
        $this->_groupNr = $split[0][0];
        $this->_serialNr = array(
            substr($split[0], 1),
            substr($split[2], 0, -1),
        );
        $this->_check = $split[2][3];
        
        $validCheck = $this->calcCheckDigit();
        if ($this->_check != $validCheck) {
            $msg = "Invalid check digit for '$id'";
            throw new InvalidCheckDigitException($msg);
        }
    }


    /**
     * Get full ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->_groupNr
            . $this->_serialNr[0]
            . '-'
            . $this->_serialNr[1]
            . $this->_check;
    }


    /**
     * To string magic method
     *
     * Get full ID
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->getId();
    }


    /**
     * Get string describing corporate group
     *
     * NOTE: this is just a hint and does not conclusively determine the legal
     * status of the corporation
     *
     * @return string
     */
    public function getGroupDescription()
    {
        switch ($this->_groupNr) {
            case "2":
                return "Stat, landsting, kommun eller församling";
            case "5":
                return "Aktiebolag";
            case "6":
                return "Enkelt bolag";
            case "7":
                return "Ekonomisk förening";
            case "8":
                return "Ideell förening eller stiftelse";
            case "9":
                return "Handelsbolag, kommanditbolag eller enkelt bolag";
            default:
                return "Okänd";
        }
    }


    /**
     * Calculate check digit
     *
     * @return string
     */
    private function calcCheckDigit()
    {
        $nr = $this->_groupNr . $this->_serialNr[0] . $this->_serialNr[1];
        $modulo = new Modulo10();

        return $modulo->getCheckDigit($nr);
    }

}
