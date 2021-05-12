<?php

namespace Cis\EducationalVisitBundle\Tests\Controller;

use Cis\EducationalVisitBundle\CommandBus\Payment\ChangeStudentParticipantPaymentAmountCommand;
use Cis\EducationalVisitBundle\CommandBus\Payment\TransferPaymentToStudentParticipantCommand;
use Cis\EducationalVisitBundle\Controller\Controller;
use Cis\EducationalVisitBundle\Controller\PaymentController;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Payment\ChangeStudentParticipantPaymentAmountFormType;
use Cis\EducationalVisitBundle\Form\Payment\FilterFormType;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\PaymentList;
use Cis\EducationalVisitBundle\View\TransferPaymentList;
use Petroc\Bridge\PhpUnit\ControllerTestCase;

class PaymentControllerTest extends ControllerTestCase
{
    private $visit;
    private $studentParticipant;
    private $util;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
        $this->studentParticipant = $this->prophesize(StudentParticipant::class);
        $this->util = $this->prophesize(ParticipantUtil::class)->reveal();
    }

    private function createController()
    {
        return new PaymentController();
    }

    public function testIndexAction()
    {
        $visit = $this->visit->reveal();
        $util = $this->util;

        $view = $this->createController()->indexAction($visit, $util);
        $this->assertCommandFormView($view, FilterFormType::class, PaymentList::class);
        $this->assertDataInstanceOf($view, PaymentList::class);
        $this->assertTemplateDataSame($view, 'visit', $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testChangeStudentParticipantPaymentAmountAction()
    {
        $visit = $this->visit->reveal();
        $studentParticipant = $this->studentParticipant;
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant->getFullPaymentAmount()->willReturn(null);
        $studentParticipant->getFirstPaymentAmount()->willReturn(null);
        $studentParticipant = $studentParticipant->reveal();

        $view = $this->createController()->changeStudentParticipantPaymentAmountAction($studentParticipant);
        $this->assertCommandFormView(
            $view,
            ChangeStudentParticipantPaymentAmountFormType::class,
            ChangeStudentParticipantPaymentAmountCommand::class
        );
        $this->assertDataSame($view, $studentParticipant);
        $this->assertTemplateDataSame($view, 'visit', $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
        $this->assertSuccessRoute($view, Controller::ROUTE_PAYMENTS, $visit);
    }

    public function testTransferStudentParticipantPaymentAction()
    {
        $visit = $this->visit->reveal();
        $studentParticipant = $this->studentParticipant;
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant = $studentParticipant->reveal();

        $view = $this->createController()->transferStudentParticipantPaymentAction($studentParticipant, $this->util);
        $this->assertDataInstanceOf($view, TransferPaymentList::class);
        $this->assertTemplateDataSame($view, 'visit', $visit);
        $this->assertTemplateDataSame($view, 'studentParticipant', $studentParticipant);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testTransferPaymentToStudentParticipantAction()
    {
        $visit = $this->visit->reveal();
        $toStudentParticipant = $this->studentParticipant;
        $toStudentParticipant->getVisit()->willReturn($visit);
        $toStudentParticipant = $toStudentParticipant->reveal();
        $fromStudentParticipant = $this->studentParticipant->reveal();

        $view = $this->createController()->transferPaymentToStudentParticipantAction($toStudentParticipant, $fromStudentParticipant);
        $this->assertCommandView($view, TransferPaymentToStudentParticipantCommand::class);
        $this->assertSuccessMessage($view, 'Transfer successful.');
        $this->assertSuccessRoute($view, Controller::ROUTE_PAYMENTS, $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }
}