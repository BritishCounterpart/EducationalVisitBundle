<?php

namespace Cis\EducationalVisitBundle\Tests\Controller;

use Cis\EducationalVisitBundle\CommandBus\Participant\AddStaffParticipantCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\AddVisitConsentCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\ExcludeStudentCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\RefreshStudentsCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\UnexcludeStudentCommand;
use Cis\EducationalVisitBundle\Controller\Controller;
use Cis\EducationalVisitBundle\Controller\ParticipantController;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Participant\AddStaffParticipantFormType;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\NoPortalAccessList;
use Cis\EducationalVisitBundle\View\ParticipantList;
use Petroc\Bridge\PhpUnit\ControllerTestCase;
use Petroc\Component\CommandBus\DeleteEntityCommand;

class ParticipantControllerTest extends ControllerTestCase
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
        return new ParticipantController();
    }

    public function testIndexAction()
    {
        $visit = $this->visit->reveal();
        $util = $this->util;
        $view = $this->createController()->indexAction($visit, $util);
        $this->assertDataInstanceOf($view, ParticipantList::class);
        $this->assertTemplateDataSame($view, 'visit', $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testRefreshStudentsAction()
    {
        $visit = $this->visit->reveal();
        $view = $this->createController()->refreshStudentsAction($visit);
        $this->assertCommandView($view, RefreshStudentsCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_PARTICIPANTS, $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testExcludeStudentAction()
    {
        $visit = $this->visit->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant = $studentParticipant->reveal();

        $view = $this->createController()->excludeStudentAction($studentParticipant);
        $this->assertCommandView($view, ExcludeStudentCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_PARTICIPANTS, $visit);
        $this->assertSuccessMessage($view, 'Student removed.');
        $this->assertFailureMessage($view, 'Student already removed.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testUnexcludeStudentAction()
    {
        $visit = $this->visit->reveal();

        $excludedStudentParticipant = $this->prophesize(ExcludedStudentParticipant::class);
        $excludedStudentParticipant->getVisit()->willReturn($visit);
        $excludedStudentParticipant = $excludedStudentParticipant->reveal();

        $view = $this->createController()->unexcludeStudentAction($excludedStudentParticipant);
        $this->assertCommandView($view, UnexcludeStudentCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_PARTICIPANTS, $visit);
        $this->assertSuccessMessage($view, 'Student unexcluded.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testNoPortalAccessAction()
    {
        $visit = $this->visit->reveal();
        $view = $this->createController()->noPortalAccessAction($visit);
        $this->assertDataInstanceOf($view, NoPortalAccessList::class);
        $this->assertTemplateDataSame($view, 'visit', $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testMarketingConsentAction()
    {
        $visit = $this->visit->reveal();
        $util = $this->util;
        $view = $this->createController()->marketingConsentAction($visit, $util);
        $this->assertDataInstanceOf($view, ParticipantList::class);
        $this->assertTemplateDataSame($view, 'visit', $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testOsa7ContactListAction()
    {
        $visit = $this->visit->reveal();
        $util = $this->util;
        $view = $this->createController()->osa7ContactListAction($visit, $util);
        $this->assertDataInstanceOf($view, ParticipantList::class);
        $this->assertTemplateDataSame($view, 'visit', $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testAddVisitConsentAction()
    {
        $visit = $this->visit->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant = $studentParticipant->reveal();

        $view = $this->createController()->addVisitConsentAction($studentParticipant);
        $this->assertCommandView($view, AddVisitConsentCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_PARTICIPANTS, $visit);
        $this->assertSuccessMessage($view, 'Visit consent added.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testAddStaffParticipantAction()
    {
        $visit = $this->visit->reveal();

        $view = $this->createController()->addStaffParticipantAction($visit);
        $this->assertCommandFormView($view, AddStaffParticipantFormType::class,AddStaffParticipantCommand::class);
        $this->assertDataSame($view, $visit);
        $this->assertSuccessRoute($view, Controller::ROUTE_PARTICIPANTS, $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testDeleteStaffParticipantAction()
    {
        $visit = $this->visit->reveal();

        $staffParticipant = $this->prophesize(StaffParticipant::class);
        $staffParticipant->getVisit()->willReturn($visit);
        $staffParticipant = $staffParticipant->reveal();

        $view = $this->createController()->deleteStaffParticipantAction($staffParticipant);
        $this->assertCommandView($view, DeleteEntityCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_PARTICIPANTS, $visit);
        $this->assertSuccessMessage($view, 'Staff member removed.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }
}