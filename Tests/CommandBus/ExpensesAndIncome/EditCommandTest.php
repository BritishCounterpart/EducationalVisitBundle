<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\ExpensesAndIncome;

use Cis\EducationalVisitBundle\CommandBus\ExpensesAndIncome\EditCommand;
use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Bridge\PhpUnit\ValidatorTrait;

class EditCommandTest extends TestCase
{
    use ValidatorTrait;

    private $visit;
    private $expense;
    private $income;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
        $this->expense = $this->prophesize(Expense::class);
        $this->income = $this->prophesize(Income::class);
    }

    public function testConstants()
    {
        // Groups
        $this->assertSame('default', EditCommand::DEFAULT_GROUP);
        $this->assertSame('has_expenses', EditCommand::HAS_EXPENSES_GROUP);
    }

    public function testConstructor()
    {
        $incomeStudent = 90.0;
        $incomeCollege = 94.5;
        $incomeOtherFrom = 'Funding';
        $otherPay = 86.6;

        $expense = $this->expense->reveal();

        $income = $this->income;
        $income->getIncomeStudent()->willReturn($incomeStudent);
        $income->getIncomeCollege()->willReturn($incomeCollege);
        $income->getIncomeOtherFrom()->willReturn($incomeOtherFrom);
        $income->getIncomeOther()->willReturn($otherPay);
        $income = $income->reveal();

        $visit = $this->visit;
        $visit->hasExpenses()->willReturn(true);
        $visit->getExpenses()->willReturn([$expense]);
        $visit->getIncome()->willReturn($income);
        $visit->hasIncome()->willReturn(true);
        $visit = $visit->reveal();

        $command = new EditCommand($visit);
        $this->assertSame($visit, $command->getVisit());
        $this->assertSame($incomeStudent, $command->studentsPays);
        $this->assertSame($incomeCollege, $command->collegePays);
        $this->assertSame($incomeOtherFrom, $command->otherPayInfo);
        $this->assertSame($otherPay, $command->otherPay);
        $this->assertCount(1, $command->expenses);
    }

    /**
     * @dataProvider getValidationData
     */
    public function testValidation($command, $numViolations)
    {
        $this->setUpValidator();
        $this->assertCount(
            $numViolations,
            $this->getValidator()->validate($command)
        );
        $this->tearDownValidator();
    }

    public function getValidationData()
    {
        $expenseId = 10;

        $visit = $this->prophesize(Visit::class);
        $expense = $this->prophesize(Expense::class);
        $expense->getId()->willReturn($expenseId);
        $expense->getType()->willReturn(null);
        $expense->getDescription()->willReturn(null);
        $expense->getAmount()->willReturn(null);
        $visit->hasExpenses()->willReturn(true);
        $visit->getExpenses()->willReturn([$expense->reveal()]);
        $visit->getIncome()->willReturn(null);
        $visit = $visit->reveal();

        $command = new EditCommand($visit);
        $command->anyExpenses = false;
        $command->anyIncome = false;
        yield [$command, 0];

        $command = new EditCommand($visit);
        $command->anyIncome = true;
        $command->studentsPays = 0.0;
        yield [$command, 3];

        $command = new EditCommand($visit);
        $command->anyIncome = true;
        $command->studentsPays = 0.0;
        $command->collegePays = 0.0;
        yield [$command, 3];

        $command = new EditCommand($visit);
        $command->anyExpenses = false;
        $command->studentsPays = 0.0;
        $command->collegePays = 0.0;
        $command->otherPay = 0.0;
        yield [$command, 0];


        $command = new EditCommand($visit);
        $command->anyExpenses = true;
        $command->studentsPays = 0.0;
        $command->collegePays = 0.0;
        $command->otherPay = 0.0;
        yield [$command, 3];

        $command = new EditCommand($visit);
        $command->anyExpenses = true;
        $command->studentsPays = 0.0;
        $command->collegePays = 0.0;
        $command->otherPay = 0.0;
        $command->expenses = [$expenseId => [
            'type' => 'Test'
        ]];
        yield [$command, 2];


        $command = new EditCommand($visit);
        $command->anyExpenses = true;
        $command->studentsPays = 0.0;
        $command->collegePays = 0.0;
        $command->otherPay = 0.0;
        $command->expenses = [$expenseId => [
            'type' => 'Test',
            'description' => 'description'
        ]];
        yield [$command, 1];

        $command = new EditCommand($visit);
        $command->anyExpenses = true;
        $command->studentsPays = 0.0;
        $command->collegePays = 0.0;
        $command->otherPay = 0.0;
        $command->expenses = [$expenseId => [
            'type' => 'Test',
            'description' => 'description',
            'amount' => 90.56,
        ]];
        yield [$command, 0];

    }

    public function testLoadValidatorMetadata()
    {
        $this->assertCanLoadValidatorMetadata(EditCommand::class);
    }
}