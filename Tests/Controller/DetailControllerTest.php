<?php

namespace Cis\EducationalVisitBundle\Tests\Controller;

use Cis\EducationalVisitBundle\CommandBus\Detail\CancelCommand;
use Cis\EducationalVisitBundle\CommandBus\Detail\CompleteCommand;
use Cis\EducationalVisitBundle\CommandBus\Detail\EditCommand;
use Cis\EducationalVisitBundle\CommandBus\Detail\EditFinanceCodesCommand;
use Cis\EducationalVisitBundle\Controller\Controller;
use Cis\EducationalVisitBundle\Controller\DetailController;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Detail\EditFinanceCodesFormType;
use Cis\EducationalVisitBundle\Form\Detail\EditFormType;
use Petroc\Bridge\PhpUnit\ControllerTestCase;
use Petroc\Component\CommandBus\DeleteEntityCommand;

class DetailControllerTest extends ControllerTestCase
{
    private $visit;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class)->reveal();
    }

    private function createController()
    {
        return new DetailController();
    }

    public function testIndexAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->indexAction($this->visit);
        $this->assertDataSame($view, $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testAddAction()
    {
        $view = $this->createController()->addAction();
        $this->assertCommandFormView($view, EditFormType::class, EditCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_EXPENSES_AND_INCOME_EDIT);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testEditAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->editAction($visit);
        $this->assertCommandFormView($view, EditFormType::class, EditCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_DETAIL, $visit);
        $this->assertDataSame($view, $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testFinanceCodesAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->financeCodesAction($visit);
        $this->assertCommandFormView($view, EditFinanceCodesFormType::class, EditFinanceCodesCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_DETAIL, $visit);
        $this->assertDataSame($view, $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_FINANCE);
    }

    public function testDeleteAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->deleteAction($visit);
        $this->assertCommandConfirmationView($view, DeleteEntityCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_INDEX);
        $this->assertSuccessMessage($view, 'Visit Deleted.');
        $this->assertDataSame($view, $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testCancelAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->cancelAction($visit);
        $this->assertCommandView($view, CancelCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_DETAIL, $visit);
        $this->assertSuccessMessage($view, 'Visit cancelled.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }


    public function testCompleteAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->completeAction($visit);
        $this->assertCommandView($view, CompleteCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_DETAIL, $visit);
        $this->assertSuccessMessage($view, 'Visit completed.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }
}