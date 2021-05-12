<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use DateTime;

class IncomeTest extends TestCase
{
    private $visit;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
    }

    private function createIncome()
    {
        return new Income(
            $this->visit->reveal()
        );
    }

    public function testConstants()
    {
        // Validation
        $this->assertSame(500, Income::MAX_LENGTH_INCOME_OTHER_FROM);
    }

    public function testConstructor()
    {
        $visit = $this->visit->reveal();
        $income = new Income($visit);

        $this->assertNull($income->getId());
        $this->assertInstanceOf(DateTime::class, $income->getCreatedOn());
        $this->assertSame($visit, $income->getVisit());
        $this->assertSame(0.0, $income->getIncomeStudent());
        $this->assertSame(0.0, $income->getIncomeCollege());
        $this->assertSame(0.0, $income->getIncomeOther());
        $this->assertNull($income->getIncomeOtherFrom());
    }

    public function testSetIncomeStudent()
    {
        $this->assertSetAndGet($this->createIncome(), 'incomeStudent', 500.0);
    }

    public function testSetIncomeCollege()
    {
        $this->assertSetAndGet($this->createIncome(), 'incomeCollege', 600.0);
    }

    public function testSetIncomeOther()
    {
        $this->assertSetAndGet($this->createIncome(), 'incomeOther', 700.0);
    }

    public function testSetIncomeOtherFrom()
    {
        $this->assertSetAndGet($this->createIncome(), 'incomeOtherFrom', 'Funds');
    }
}