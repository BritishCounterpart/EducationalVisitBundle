<?php

namespace Cis\EducationalVisitBundle\Tests\Repository;

use App\Entity\Employee\Employee;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\EntityRepositoryTestCase;
use App\Tests\Fixture\Employee\EmployeeTableFixture;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Tests\Fixture\StaffParticipantTableFixture;
use Cis\EducationalVisitBundle\Tests\Fixture\VisitTableFixture;

class StaffParticipantRepositoryTest extends EntityRepositoryTestCase
{
    protected function getTableFixtures()
    {
        return [
            new StaffParticipantTableFixture(),
            new VisitTableFixture(),
            new EmployeeTableFixture(),
            new EmptyEntityTableFixture(Income::class),
            new EmptyEntityTableFixture(Itinerary::class)
        ];
    }

    private function getRepository()
    {
        return $this->em->getRepository(StaffParticipant::class);
    }

    private function createVisit(int $id)
    {
        $area = $this->prophesize(Visit::class);
        $area->getId()->willReturn($id);
        return $area->reveal();
    }

    private function createEmployee(int $id)
    {
        $area = $this->prophesize(Employee::class);
        $area->getId()->willReturn($id);
        return $area->reveal();
    }

    public function testFindByVisit()
    {
        $visit = $this->createVisit(VisitTableFixture::DARTMOOR_TRIP_ID);
        $this->assertCount(2, $this->getRepository()->findByVisit($visit));
    }

    public function testFindOneByEmployeeAndVisit()
    {
        $visit = $this->createVisit(VisitTableFixture::DARTMOOR_TRIP_ID);
        $employee = $this->createEmployee(EmployeeTableFixture::TED_SMITH_ID);
        $excludedStudentParticipant = $this->getRepository()->findOneByEmployeeAndVisit($employee, $visit);
        $this->assertSame($excludedStudentParticipant->getId(), StaffParticipantTableFixture::DARTMOOR_TRIP_TED_SMITH_ID);
    }
}