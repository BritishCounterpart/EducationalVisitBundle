<?php

namespace Cis\EducationalVisitBundle\Tests\Controller;

use Cis\EducationalVisitBundle\CommandBus\ExpensesAndIncome\EditCommand;
use Cis\EducationalVisitBundle\Controller\Controller;
use Cis\EducationalVisitBundle\Controller\ExpensesAndIncomeController;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\ExpensesAndIncome\EditFormType;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\ExpensesAndIncomeList;
use Doctrine\Common\Collections\ArrayCollection;
use Petroc\Bridge\PhpUnit\ControllerTestCase;

class ExpensesAndIncomeControllerTest extends ControllerTestCase
{
    private $visit;
    private $util;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
        $this->util = $this->prophesize(ParticipantUtil::class)->reveal();
    }

    private function createController()
    {
        return new ExpensesAndIncomeController();
    }

    public function testIndexAction()
    {
        $visit = $this->visit->reveal();
        $util = $this->util;
        $view = $this->createController()->indexAction($visit, $util);
        $this->assertDataInstanceOf($view, ExpensesAndIncomeList::class);
        $this->assertTemplateDataSame($view, 'visit', $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testEditAction()
    {
        $visit = $this->visit;
        $visit->hasExpenses()->willReturn(true);
        $visit->getExpenses()->willReturn(new ArrayCollection());
        $visit->getIncome()->willReturn(null);
        $visit =  $visit->reveal();
        $view = $this->createController()->editAction($visit);
        $this->assertCommandFormView($view, EditFormType::class, EditCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_EXPENSES_AND_INCOME, $visit);
        $this->assertDataInstanceOf($view, Visit::class);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }
}