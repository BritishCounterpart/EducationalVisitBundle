<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use App\Entity\Employee\Employee;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use DateTime;

class StaffParticipantTest extends TestCase
{
    public function testConstructor()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $employee = $this->prophesize(Employee::class)->reveal();

        $staffParticipant = new StaffParticipant($visit, $employee);

        $this->assertNull($staffParticipant->getId());
        $this->assertInstanceOf(DateTime::class, $staffParticipant->getCreatedOn());
        $this->assertSame($visit, $staffParticipant->getVisit());
        $this->assertSame($employee, $staffParticipant->getEmployee());
    }
}