<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\ExpensesAndIncome;

use Cis\EducationalVisitBundle\CommandBus\ExpensesAndIncome\EditCommand;
use Cis\EducationalVisitBundle\CommandBus\ExpensesAndIncome\EditHandler;
use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Doctrine\Common\Collections\ArrayCollection;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;

class EditHandlerTest extends TestCase
{
    private $orm;
    private $messenger;
    private $visit;

    protected function setUp()
    {
        $this->orm = $this->prophesize(Orm::class);
        $this->messenger = $this->prophesize(EducationalVisitMessenger::class);
        $this->visit = $this->prophesize(Visit::class);
    }

    private function createHandler()
    {
        return new EditHandler($this->orm->reveal(), $this->messenger->reveal());
    }

    public function testAddHandle()
    {
        $studentsPays = 10.56;
        $collegePays = 45.67;
        $otherPay = 100.45;

        $visit = $this->visit;
        $visit->getStatus()->willReturn(Visit::STATUS_PENDING_APPROVAL);
        $visit->hasExpenses()->willReturn(true);
        $visit->getExpenses()->willReturn(new ArrayCollection([]));
        $visit->hasIncome()->willReturn(true);
        $visit->getIncome()->willReturn(null);
        $visit->setHasExpenses(Argument::exact(true))->shouldBeCalled();
        $visit->addExpense(Argument::type(Expense::class))->shouldBeCalled();
        $visit->removeExpense(Argument::type(Expense::class))->shouldNotBeCalled();

        $visit = $visit->reveal();

        $command = new EditCommand($visit);
        $command->anyIncome = true;
        $command->studentsPays = $studentsPays;
        $command->collegePays = $collegePays;
        $command->otherPay = $otherPay;

        $command->anyExpenses = true;
        $command->expenses = [20 => [
            'type' => 'Type',
            'description' => 'Description',
            'amount' => 98.45,
        ]];

        $this->orm
            ->persist(Argument::type(Expense::class))
            ->shouldBeCalled()
        ;

        $this->orm
            ->persist(Argument::type(Income::class))
            ->shouldBeCalled()
        ;

        $this->messenger->sendExpensesChangedOnApprovedVisit(
            Argument::type(ArrayCollection::class),
            Argument::type(Visit::class)
        )->shouldNotBeCalled();

        $handler = $this->createHandler();
        $handler->handle($command);
    }

    public function testAddEditHandle()
    {
        $id = 40;
        $type = 'Type';
        $description = 'Description';
        $amount = 98.45;

        $studentsPays = 10.56;
        $collegePays = 45.67;
        $otherPay = 100.45;
        $otherPayInfo = 'Info';

        $expense = $this->prophesize(Expense::class);
        $expense->getId()->willReturn($id);
        $expense->getType()->willReturn(null);
        $expense->getDescription()->willReturn(null);
        $expense->getAmount()->willReturn(0.0);
        $expense->setType(Argument::exact($type))->shouldBeCalled();
        $expense->setDescription(Argument::exact($description))->shouldBeCalled();
        $expense->setAmount(Argument::exact($amount))->shouldBeCalled();

        $income = $this->prophesize(Income::class);
        $income->getIncomeStudent()->willReturn(0.0);
        $income->getIncomeCollege()->willReturn(0.0);
        $income->getIncomeOther()->willReturn(0.0);
        $income->getIncomeOtherFrom()->willReturn(null);
        $income->setIncomeStudent(Argument::exact($studentsPays))->shouldBeCalled();
        $income->setIncomeCollege(Argument::exact($collegePays))->shouldBeCalled();
        $income->setIncomeOther(Argument::exact($otherPay))->shouldBeCalled();
        $income->setIncomeOtherFrom(Argument::exact($otherPayInfo))->shouldBeCalled();

        $visit = $this->visit;
        $visit->getStatus()->willReturn(Visit::STATUS_PENDING_APPROVAL);
        $visit->hasExpenses()->willReturn(true);
        $visit->getExpenses()->willReturn(new ArrayCollection([$expense->reveal()]));
        $visit->hasIncome()->willReturn(true);
        $visit->getIncome()->willReturn($income->reveal());
        $visit->setHasExpenses(Argument::exact(true))->shouldBeCalled();
        $visit->addExpense(Argument::type(Expense::class))->shouldBeCalledOnce();
        $visit->removeExpense(Argument::type(Expense::class))->shouldNotBeCalled();

        $visit = $visit->reveal();

        $command = new EditCommand($visit);
        $command->anyIncome = true;
        $command->studentsPays = $studentsPays;
        $command->collegePays = $collegePays;
        $command->otherPay = $otherPay;
        $command->otherPayInfo = $otherPayInfo;

        $command->anyExpenses = true;
        $command->expenses = [$id => [
            'type' => $type,
            'description' => $description,
            'amount' => $amount
        ],
        60 => [
            'type' => 'New Type',
            'description' => 'New Desc',
            'amount' => 58.55
        ]];

        $this->orm
            ->persist(Argument::type(Expense::class))
            ->shouldBeCalledOnce()
        ;

        $this->orm
            ->persist(Argument::type(Income::class))
            ->shouldNotBeCalled()
        ;

        $this->messenger->sendExpensesChangedOnApprovedVisit(
            Argument::type(ArrayCollection::class),
            Argument::type(Visit::class)
        )->shouldNotBeCalled();

        $handler = $this->createHandler();
        $handler->handle($command);
    }

    public function testEditHandle()
    {
        $id = 40;
        $type = 'Type';
        $description = 'Description';
        $amount = 98.45;

        $studentsPays = 10.56;
        $collegePays = 45.67;
        $otherPay = 100.45;
        $otherPayInfo = 'Info';

        $expense = $this->prophesize(Expense::class);
        $expense->getId()->willReturn($id);
        $expense->getType()->willReturn(null);
        $expense->getDescription()->willReturn(null);
        $expense->getAmount()->willReturn(0.0);
        $expense->setType(Argument::exact($type))->shouldBeCalled();
        $expense->setDescription(Argument::exact($description))->shouldBeCalled();
        $expense->setAmount(Argument::exact($amount))->shouldBeCalled();

        $income = $this->prophesize(Income::class);
        $income->getIncomeStudent()->willReturn(0.0);
        $income->getIncomeCollege()->willReturn(0.0);
        $income->getIncomeOther()->willReturn(0.0);
        $income->getIncomeOtherFrom()->willReturn(null);
        $income->setIncomeStudent(Argument::exact($studentsPays))->shouldBeCalled();
        $income->setIncomeCollege(Argument::exact($collegePays))->shouldBeCalled();
        $income->setIncomeOther(Argument::exact($otherPay))->shouldBeCalled();
        $income->setIncomeOtherFrom(Argument::exact($otherPayInfo))->shouldBeCalled();

        $visit = $this->visit;
        $visit->getStatus()->willReturn(Visit::STATUS_PENDING_APPROVAL);
        $visit->hasExpenses()->willReturn(true);
        $visit->getExpenses()->willReturn(new ArrayCollection([$expense->reveal()]));
        $visit->hasIncome()->willReturn(true);
        $visit->getIncome()->willReturn($income->reveal());
        $visit->setHasExpenses(Argument::exact(true))->shouldBeCalled();
        $visit->addExpense(Argument::type(Expense::class))->shouldNotBeCalled();
        $visit->removeExpense(Argument::type(Expense::class))->shouldNotBeCalled();

        $visit = $visit->reveal();

        $command = new EditCommand($visit);
        $command->studentsPays = $studentsPays;
        $command->collegePays = $collegePays;
        $command->otherPay = $otherPay;
        $command->otherPayInfo = $otherPayInfo;

        $command->anyExpenses = true;
        $command->expenses = [$id => [
            'type' => $type,
            'description' => $description,
            'amount' => $amount,
        ]];

        $this->orm
            ->persist(Argument::type(Expense::class))
            ->shouldNotBeCalled()
        ;

        $this->orm
            ->persist(Argument::type(Income::class))
            ->shouldNotBeCalled()
        ;

        $this->messenger->sendExpensesChangedOnApprovedVisit(
            Argument::type(ArrayCollection::class),
            Argument::type(Visit::class)
        )->shouldNotBeCalled();

        $handler = $this->createHandler();
        $handler->handle($command);
    }

    public function testRemoveHandle()
    {
        $studentsPays = 10.56;
        $collegePays = 45.67;
        $otherPay = 100.45;
        $otherPayInfo = 'Info';

        $expense = $this->prophesize(Expense::class);
        $expense->getId()->willReturn(50);
        $expense->getType()->willReturn(null);
        $expense->getDescription()->willReturn(null);
        $expense->getAmount()->willReturn(0.0);
        $expense->setType(Argument::any())->shouldNotBeCalled();
        $expense->setDescription(Argument::any())->shouldNotBeCalled();
        $expense->setAmount(Argument::any())->shouldNotBeCalled();

        $income = $this->prophesize(Income::class);
        $income->getIncomeStudent()->willReturn(0.0);
        $income->getIncomeCollege()->willReturn(0.0);
        $income->getIncomeOther()->willReturn(0.0);
        $income->getIncomeOtherFrom()->willReturn(null);
        $income->setIncomeStudent(Argument::exact($studentsPays))->shouldBeCalled();
        $income->setIncomeCollege(Argument::exact($collegePays))->shouldBeCalled();
        $income->setIncomeOther(Argument::exact($otherPay))->shouldBeCalled();
        $income->setIncomeOtherFrom(Argument::exact($otherPayInfo))->shouldBeCalled();

        $visit = $this->visit;
        $visit->getStatus()->willReturn(Visit::STATUS_APPROVED);
        $visit->hasExpenses()->willReturn(true);
        $visit->getExpenses()->willReturn(new ArrayCollection([$expense->reveal()]));
        $visit->hasIncome()->willReturn(true);
        $visit->getIncome()->willReturn($income->reveal());
        $visit->setHasExpenses(Argument::exact(true))->shouldBeCalled();
        $visit->addExpense(Argument::type(Expense::class))->shouldNotBeCalled();
        $visit->removeExpense(Argument::type(Expense::class))->shouldBeCalled();

        $visit = $visit->reveal();

        $command = new EditCommand($visit);
        $command->anyIncome = true;
        $command->studentsPays = $studentsPays;
        $command->collegePays = $collegePays;
        $command->otherPay = $otherPay;
        $command->otherPayInfo = $otherPayInfo;

        $command->anyExpenses = true;
        $command->expenses = [];

        $this->orm
            ->persist(Argument::type(Expense::class))
            ->shouldNotBeCalled()
        ;

        $this->orm
            ->persist(Argument::type(Income::class))
            ->shouldNotBeCalled()
        ;

        $this->messenger->sendExpensesChangedOnApprovedVisit(
            Argument::type(ArrayCollection::class),
            Argument::type(Visit::class)
        )->shouldBeCalled();

        $handler = $this->createHandler();
        $handler->handle($command);
    }

    public function testAnyExpensesHandle()
    {
        $visit = $this->visit;
        $visit->getStatus()->willReturn(Visit::STATUS_PENDING_APPROVAL);
        $visit->hasExpenses()->willReturn(true);
        $visit->getExpenses()->willReturn(new ArrayCollection([]));
        $visit->getIncome()->willReturn(null);
        $visit->hasIncome()->willReturn(false);
        $visit->setHasExpenses(Argument::exact(false))->shouldBeCalled();
        $visit->addExpense(Argument::type(Expense::class))->shouldNotBeCalled();
        $visit->removeExpense(Argument::type(Expense::class))->shouldNotBeCalled();

        $visit = $visit->reveal();

        $command = new EditCommand($visit);
        $command->anyIncome = false;

        $command->anyExpenses = false;
        $command->expenses = [20 => [
            'type' => 'Type',
            'description' => 'Description',
            'amount' => 98.45,
        ]];

        $this->orm
            ->persist(Argument::type(Expense::class))
            ->shouldNotBeCalled()
        ;

        $this->orm
            ->persist(Argument::type(Income::class))
            ->shouldNotBeCalled()
        ;

        $this->messenger->sendExpensesChangedOnApprovedVisit(
            Argument::type(ArrayCollection::class),
            Argument::type(Visit::class)
        )->shouldNotBeCalled();

        $handler = $this->createHandler();
        $handler->handle($command);
    }
}