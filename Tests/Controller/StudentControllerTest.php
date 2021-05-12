<?php

namespace Cis\EducationalVisitBundle\Tests\Controller;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\CommandBus\Participant\AddVisitConsentCommand;
use Cis\EducationalVisitBundle\Controller\Controller;
use Cis\EducationalVisitBundle\Controller\StudentController;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\StudentVisitList;
use Petroc\Bridge\PhpUnit\ControllerTestCase;

class StudentControllerTest extends ControllerTestCase
{
    private $visit;
    private $student;
    private $studentParticipant;
    private $util;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
        $this->student = $this->prophesize(Student::class);
        $this->studentParticipant = $this->prophesize(StudentParticipant::class);
        $this->util = $this->prophesize(ParticipantUtil::class)->reveal();
    }

    private function createController()
    {
        return new StudentController();
    }

    public function testIndexAction()
    {
        $student = $this->student->reveal();
        $util = $this->util;

        $view = $this->createController()->indexAction($student, $util);
        $this->assertDataInstanceOf($view, StudentVisitList::class);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_STUDENT, $student);
    }

    public function testAddVisitConsentAction()
    {
        $visit = $this->visit->reveal();
        $student = $this->student->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();

        $view = $this->createController()->addVisitConsentAction($studentParticipant);
        $this->assertCommandConfirmationView($view, AddVisitConsentCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_STUDENT, $student);
        $this->assertDataSame($view, $visit);
        $this->assertTemplateDataSame($view, 'student', $student);
        $this->assertSuccessMessage($view, 'Visit consent added.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_STUDENT_PARTICIPANT_VISIT_CONSENT, $student);
    }
}