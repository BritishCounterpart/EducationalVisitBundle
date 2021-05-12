<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use DateTime;

class ExpenseTest extends TestCase
{
    private $visit;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
    }

    private function createExpense()
    {
        return new Expense(
            $this->visit->reveal(),
            'Type',
            'Description',
            0.0
        );
    }

    public function testConstants()
    {
        // Validation
        $this->assertSame(250, Expense::MAX_LENGTH_DESCRIPTION);
    }

    public function testConstructor()
    {
        $visit = $this->visit->reveal();
        $type = 'Type';
        $description = 'Description';
        $amount = 20.0;

        $expense = new Expense(
            $visit,
            $type,
            $description,
            $amount
        );

        $this->assertNull($expense->getId());
        $this->assertInstanceOf(DateTime::class, $expense->getCreatedOn());
        $this->assertSame($visit, $expense->getVisit());
        $this->assertSame($type, $expense->getType());
        $this->assertSame($description, $expense->getDescription());
        $this->assertSame($amount, $expense->getAmount());
    }

    public function testSetType()
    {
        $this->assertSetAndGet($this->createExpense(), 'type', 'Travel');
    }

    public function testSetDescription()
    {
        $this->assertSetAndGet($this->createExpense(), 'description', 'Travel Costs');
    }

    public function testSetAmount()
    {
        $this->assertSetAndGet($this->createExpense(), 'amount', 56.89);
    }

}