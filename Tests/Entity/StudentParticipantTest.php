<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use App\Entity\Cohort\Cohort;
use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use DateTime;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class StudentParticipantTest extends TestCase
{
    private $visit;
    private $student;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
        $this->student = $this->prophesize(Student::class);
    }

    protected function createStudentParticipant(Visit $visit, Student $student)
    {
        return new StudentParticipant(
            $visit,
            $student
        );
    }

    public function testConstructor()
    {
        $visit = $this->visit;
        $visit->addStudentParticipant(Argument::type(StudentParticipant::class))->shouldBeCalledOnce();
        $visit = $visit->reveal();
        $student = $this->student->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);

        $this->assertNull($studentParticipant->getId());
        $this->assertInstanceOf(DateTime::class, $studentParticipant->getCreatedOn());
        $this->assertSame($visit, $studentParticipant->getVisit());
        $this->assertSame($student, $studentParticipant->getStudent());
        $this->assertFalse($studentParticipant->isNoLongerGoing());
        $this->assertFalse($studentParticipant->isPaymentCompleteEmailSent());
    }

    public function testSetCohort()
    {
        $visit = $this->visit->reveal();
        $student = $this->student->reveal();
        $cohort = $this->prophesize(Cohort::class)->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $this->assertSetAndGet($studentParticipant, 'cohort', $cohort);
    }

    public function testSetFullPaymentAmount()
    {
        $visit = $this->visit->reveal();
        $student = $this->student->reveal();
        $amount = 10.35;

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $this->assertSetAndGet($studentParticipant, 'fullPaymentAmount', $amount);
    }

    public function testSetFirstPaymentAmount()
    {
        $visit = $this->visit->reveal();
        $student = $this->student->reveal();
        $amount = 5.20;

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $this->assertSetAndGet($studentParticipant, 'firstPaymentAmount', $amount);
    }

    public function testSetNoLongerGoing()
    {
        $visit = $this->visit->reveal();
        $student = $this->student->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $studentParticipant->setNoLongerGoing(true);
        $this->assertTrue($studentParticipant->isNoLongerGoing());
    }

    public function testSetPaymentCompleteEmailSent()
    {
        $visit = $this->visit->reveal();
        $student = $this->student->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $studentParticipant->setPaymentCompleteEmailSent(true);
        $this->assertTrue($studentParticipant->isPaymentCompleteEmailSent());
    }

    public function testHasVisitConsent()
    {
        // 18 or over no visit consent needed
        $visit = $this->visit->reveal();
        $student = $this->student;
        $student->getAgeToday()->willReturn(18);
        $student = $student->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $studentParticipant->setPaymentCompleteEmailSent(true);
        $this->assertSame('N/A', $studentParticipant->hasVisitConsent());

        // 17 or under and doesn't have visit consent - overnight
        $visit = $this->visit;
        $visit->addStudentParticipant(Argument::type(StudentParticipant::class))->shouldBeCalled();
        $visit->getCategory()->willReturn(Visit::CATEGORY_OVERNIGHT_DAY_TRIP_HR);
        $visit = $visit->reveal();

        $student = $this->student;
        $student->getAgeToday()->willReturn(17);
        $student = $student->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $studentParticipant->setHasVisitConsent(false);
        $this->assertSame('No', $studentParticipant->hasVisitConsent());

        // 17 or under and doesn't have visit consent - oversea
        $visit = $this->visit;
        $visit->addStudentParticipant(Argument::type(StudentParticipant::class))->shouldBeCalled();
        $visit->getCategory()->willReturn(Visit::CATEGORY_OVERSEAS_HR);
        $visit = $visit->reveal();

        $student = $this->student;
        $student->getAgeToday()->willReturn(17);
        $student = $student->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $studentParticipant->setHasVisitConsent(false);

        $this->assertSame('No', $studentParticipant->hasVisitConsent());

        // 17 or under and has visit consent
        $visit = $this->visit;
        $visit->addStudentParticipant(Argument::type(StudentParticipant::class))->shouldBeCalled();
        $visit->getCategory()->willReturn(Visit::CATEGORY_OVERSEAS_HR)->shouldBeCalled();
        $visit = $visit->reveal();
        $student = $this->student;
        $student->getAgeToday()->willReturn();
        $student = $student->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $studentParticipant->setHasVisitConsent(true);
        $this->assertSame('Yes', $studentParticipant->hasVisitConsent());

        // 17 or under and visit consent not required
        $visit = $this->visit;
        $visit->addStudentParticipant(Argument::type(StudentParticipant::class))->shouldBeCalled();
        $visit->getCategory()->willReturn(Visit::CATEGORY_DAY_TRIP_LR)->shouldBeCalled();
        $visit = $visit->reveal();
        $student = $this->student;
        $student->getAgeToday()->willReturn();
        $student = $student->reveal();

        $studentParticipant = $this->createStudentParticipant($visit, $student);
        $studentParticipant->setHasVisitConsent(false);
        $this->assertSame('N/A', $studentParticipant->hasVisitConsent());

    }
}