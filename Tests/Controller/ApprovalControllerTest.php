<?php

namespace Cis\EducationalVisitBundle\Tests\Controller;

use App\Entity\User;
use Cis\EducationalVisitBundle\CommandBus\Approval\ApproveCommand;
use Cis\EducationalVisitBundle\CommandBus\Approval\RejectCommand;
use Cis\EducationalVisitBundle\CommandBus\Approval\RequestCommand;
use Cis\EducationalVisitBundle\Controller\ApprovalController;
use Cis\EducationalVisitBundle\Controller\Controller;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Approval\RejectFormType;
use Petroc\Bridge\PhpUnit\ControllerTestCase;

class ApprovalControllerTest extends ControllerTestCase
{
    private $visit;
    private $user;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class)->reveal();
        $this->user = $this->prophesize(User::class)->reveal();
    }

    private function createController()
    {
        return new ApprovalController();
    }

    public function testRequestAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->requestAction($visit);
        $this->assertCommandView($view, RequestCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_DETAIL, $visit);
        $this->assertSuccessMessage($view, 'Approval request sent.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_APPROVAL_REQUEST, $visit);
    }

    public function testApproveAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->approveAction($visit, $this->user);
        $this->assertCommandView($view, ApproveCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_REFRESH_ALERTS);
        $this->assertSuccessMessage($view, 'Visit approved.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_APPROVAL_CAN_APPROVE, $visit);
    }

    public function testRejectAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->rejectAction($visit, $this->user);
        $this->assertCommandFormView($view, RejectFormType::class, RejectCommand::class);
        $this->assertDataSame($view, $visit);
        $this->assertSuccessRoute($view, Controller::ROUTE_REFRESH_ALERTS);
        $this->assertSuccessMessage($view, 'Visit rejected.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_APPROVAL_CAN_APPROVE, $visit);
    }
}