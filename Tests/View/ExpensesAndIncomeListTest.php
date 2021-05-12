<?php

namespace Cis\EducationalVisitBundle\Tests\View;

use App\Repository\Order\OrderCriteria;
use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\Util\PaymentUtil;
use Cis\EducationalVisitBundle\View\ExpensesAndIncomeList;
use Doctrine\Common\Collections\ArrayCollection;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class ExpensesAndIncomeListTest extends TestCase
{
    private $util;
    private $income;
    private $expense;

    protected function setUp()
    {
        $this->util = $this->prophesize(ParticipantUtil::class);
        $this->income = $this->prophesize(Income::class);
        $this->expense = $this->prophesize(Expense::class);
    }

    private function createList(ParticipantUtil $util, Visit $visit)
    {
        return new ExpensesAndIncomeList($util, $visit);
    }

    private function createStudentParticipant()
    {
        return $this->prophesize(StudentParticipant::class);
    }

    private function createVisit(array $studentParticipants = [], Income $income = null, array $expenses = [])
    {
        $visit = $this->prophesize(Visit::class);
        $visit->getStudentParticipants()->willReturn(new ArrayCollection($studentParticipants));
        $visit->getIncome()->willReturn($income);
        $visit->getExpenses()->willReturn(new ArrayCollection($expenses));
        return $visit;
    }

    public function testConstructor()
    {
        $income = $this->income->reveal();
        $expenses = new ArrayCollection([$this->expense->reveal()]);
        $studentParticipants = new ArrayCollection([$this->createStudentParticipant()->reveal()]);

        $visit = $this->prophesize(Visit::class);
        $visit->getStudentParticipants()->willReturn($studentParticipants);
        $visit->getIncome()->willReturn($income);
        $visit->getExpenses()->willReturn($expenses);
        $visit = $visit->reveal();

        $list = $this->createList($this->util->reveal(), $visit);

        $this->assertSame($visit, $list->getVisit());
        $this->assertSame($income, $list->getIncome());
        $this->assertSame($expenses, $list->getExpenses());
        $this->assertSame($studentParticipants, $list->getStudentParticipants());
    }

    public function testGetActualStudentIncomeAndActualTotalIncome()
    {
        $util = $this->util;

        // Should be included
        $studentParticipantOne = $this->createStudentParticipant();
        $studentParticipantOne->getId()->willReturn(1);
        $studentParticipantOne->isNoLongerGoing()->willReturn(false);
        $studentParticipantOne = $studentParticipantOne->reveal();
        $payment = ['amountPaid' => 10.0];
        $util->getStudentPayment(Argument::exact($studentParticipantOne))->willReturn($payment)->shouldBeCalled();
        $util->getMissingOSAConsent(Argument::exact($studentParticipantOne))->willReturn([])->shouldBeCalled();
        $util->isAbleToGo(Argument::exact($studentParticipantOne), Argument::exact([]), Argument::exact($payment))->willReturn(true)->shouldBeCalled();

        // Should be included
        $studentParticipantTwo = $this->createStudentParticipant();
        $studentParticipantTwo->getId()->willReturn(2);
        $studentParticipantTwo->isNoLongerGoing()->willReturn(false);
        $studentParticipantTwo = $studentParticipantTwo->reveal();
        $payment = ['amountPaid' => 20.0];
        $util->getStudentPayment(Argument::exact($studentParticipantTwo))->willReturn($payment)->shouldBeCalled();
        $util->getMissingOSAConsent(Argument::exact($studentParticipantTwo))->willReturn([])->shouldBeCalled();
        $util->isAbleToGo(Argument::exact($studentParticipantTwo), Argument::exact([]), Argument::exact($payment))->willReturn(true)->shouldBeCalled();

        // Should be excluded, is no longer going
        $studentParticipantThree = $this->createStudentParticipant();
        $studentParticipantThree->getId()->willReturn(3);
        $studentParticipantThree->isNoLongerGoing()->willReturn(true);
        $studentParticipantThree = $studentParticipantThree->reveal();
        $payment = ['amountPaid' => 30.0];
        $util->getStudentPayment(Argument::exact($studentParticipantThree))->shouldNotBeCalled();
        $util->getMissingOSAConsent(Argument::exact($studentParticipantThree))->shouldNotBeCalled();
        $util->isAbleToGo(Argument::exact($studentParticipantThree), Argument::exact([]), Argument::exact($payment))->shouldNotBeCalled();

        // Should be excluded, is not able to go
        $studentParticipantFour = $this->createStudentParticipant();
        $studentParticipantFour->getId()->willReturn(4);
        $studentParticipantFour->isNoLongerGoing()->willReturn(false);
        $studentParticipantFour = $studentParticipantFour->reveal();
        $payment = ['amountPaid' => 25.0];
        $util->getStudentPayment(Argument::exact($studentParticipantFour))->willReturn($payment)->shouldBeCalled();
        $util->getMissingOSAConsent(Argument::exact($studentParticipantFour))->willReturn(['Student'])->shouldBeCalled();
        $util->isAbleToGo(Argument::exact($studentParticipantFour), Argument::exact(['Student']), Argument::exact($payment))->willReturn(false)->shouldBeCalled();

        $studentParticipants = [
            $studentParticipantOne,
            $studentParticipantTwo,
            $studentParticipantThree,
            $studentParticipantFour
        ];

        $income = $this->income;
        $income->getIncomeStudent()->willReturn(5);
        $income->getIncomeCollege()->willReturn(10);
        $income->getIncomeOther()->willReturn(15);
        $income = $income->reveal();

        $visit = $this->createVisit(
            $studentParticipants,
            $income
        )->reveal();

        $list = $this->createList($util->reveal(), $visit);

        // Test getActualStudentIncome()
        $this->assertSame($list->getActualStudentIncome(), 30.0);
        // Test Cached
        $this->assertSame($list->getActualStudentIncome(), 30.0);

        // Test getActualTotalIncome()
        $this->assertSame($list->getActualTotalIncome(), 55.0);
    }

    public function testGetPlannedTotalIncome()
    {
        $income = $this->income;
        $income->getIncomeStudent()->willReturn(5);
        $income->getIncomeCollege()->willReturn(10);
        $income->getIncomeOther()->willReturn(15);

        $visit = $this->createVisit(
            [],
            $income->reveal()
        )->reveal();

        $list = $this->createList($this->util->reveal(), $visit);
        $this->assertSame($list->getPlannedTotalIncome(), 30);
    }

    public function testGetTotalExpense()
    {
        $expenseOne = $this->prophesize(Expense::class);
        $expenseTwo = $this->prophesize(Expense::class);

        $expenseOne->getAmount()->willReturn(10);
        $expenseOne = $expenseOne->reveal();

        $expenseTwo->getAmount()->willReturn(25);
        $expenseTwo = $expenseTwo->reveal();

        $expenses = [
            $expenseOne,
            $expenseTwo
        ];

        $visit = $this->createVisit(
            [],
            null,
            $expenses
        )->reveal();

        $list = $this->createList($this->util->reveal(), $visit);

        $this->assertSame(35.0, $list->getTotalExpense());
    }
}