<?php

declare(strict_types=1);

namespace byrokrat\id;

use PHPUnit\Framework\TestCase;

class CoordinationIdTest extends TestCase
{
    public function invalidStructureProvider()
    {
        return [
            [''],
            ['123456'],
            ['123456-'],
            ['-1234'],
            ['123456-123'],
            ['123456-12345'],
            ['1234567-1234'],
            ['1234567-1234'],
            ['123456-1A34'],
            ['12A456-1234'],
            ['123456+'],
            ['+1234'],
            ['123456+123'],
            ['123456+12345'],
            ['1234567+1234'],
            ['1234567+1234'],
            ['123456+1A34'],
            ['12A456+1234'],
        ];
    }

    /**
     * @dataProvider invalidStructureProvider
     */
    public function testInvalidStructure($number)
    {
        $this->expectException(Exception\InvalidStructureException::CLASS);
        new CoordinationId($number);
    }

    public function invalidCheckDigitProvider()
    {
        return [
            ['820383-2770'],
            ['820383-2771'],
            ['820383-2775'],
            ['820383-2773'],
            ['820383-2774'],
            ['820383-2776'],
            ['820383-2777'],
            ['820383-2778'],
            ['820383-2779'],
            ['820383+2770'],
            ['820383+2771'],
            ['820383+2775'],
            ['820383+2773'],
            ['820383+2774'],
            ['820383+2776'],
            ['820383+2777'],
            ['820383+2778'],
            ['820383+2779'],
        ];
    }

    /**
     * @dataProvider invalidCheckDigitProvider
     */
    public function testInvalidCheckDigit($number)
    {
        $this->expectException(Exception\InvalidCheckDigitException::CLASS);
        new CoordinationId($number);
    }

    public function interchangeableFormulasProvider()
    {
        return [
            ['701063-2391', '7010632391'],
            ['19701063-2391', '197010632391'],
            ['19701063-2391', '19701063+2391'],
        ];
    }

    /**
     * @dataProvider interchangeableFormulasProvider
     */
    public function testInterchangeableFormulas($numberA, $numberB)
    {
        $this->assertSame(
            (string)new CoordinationId($numberA),
            (string)new CoordinationId($numberB)
        );
    }

    public function testGetDelimiter()
    {
        $this->assertEquals(
            '-',
            (new CoordinationId('19701063+2391'))->getDelimiter()
        );

        $this->assertEquals(
            '+',
            (new CoordinationId('18701063-2391'))->getDelimiter()
        );
    }

    public function testToString()
    {
        $this->assertEquals(
            '701063-2391',
            (string) new CoordinationId('701063-2391')
        );
    }

    public function testGetBirthDate()
    {
        $this->assertEquals(
            '1970-10-03',
            (new CoordinationId('701063-2391'))->getBirthDate()->format('Y\-m\-d')
        );
    }

    public function testGetCentury()
    {
        $this->assertEquals(
            '19',
            (new CoordinationId('701063-2391'))->getCentury()
        );

        $this->assertEquals(
            '18',
            (new CoordinationId('701063+2391'))->getCentury()
        );
    }

    public function testGetSex()
    {
        $this->assertEquals(
            Sexes::SEX_MALE,
            (new CoordinationId('701063-2391'))->getSex()
        );

        $this->assertEquals(
            Sexes::SEX_FEMALE,
            (new CoordinationId('770374-0345'))->getSex()
        );
    }

    public function testGetBirthCounty()
    {
        $this->assertEquals(
            Counties::COUNTY_UNDEFINED,
            (new CoordinationId('770374-0345'))->getBirthCounty()
        );
    }

    public function testComputeCenturyFromCurrentDate()
    {
        $this->assertSame(
            '1919',
            (new CoordinationId('1912902390', new \DateTime('19900101')))->format('Y')
        );
    }
}
