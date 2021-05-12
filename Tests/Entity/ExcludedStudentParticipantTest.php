<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use DateTime;

class ExcludedStudentParticipantTest extends TestCase
{
    private $visit;
    private $student;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class)->reveal();
        $this->student = $this->prophesize(Student::class)->reveal();
    }

    public function testConstructor()
    {
        $visit = $this->visit;
        $student = $this->student;

        $excludedStudentParticipant = new ExcludedStudentParticipant(
            $visit,
            $student
        );

        $this->assertNull($excludedStudentParticipant->getId());
        $this->assertInstanceOf(DateTime::class, $excludedStudentParticipant->getCreatedOn());
        $this->assertSame($visit, $excludedStudentParticipant->getVisit());
        $this->assertSame($student, $excludedStudentParticipant->getStudent());
    }
}