<?php

namespace Cis\EducationalVisitBundle\Tests\Repository;

use App\Entity\Student\Student;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\EntityRepositoryTestCase;
use App\Tests\Fixture\Student\StudentTableFixture;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Tests\Fixture\ExcludedStudentParticipantTableFixture;
use Cis\EducationalVisitBundle\Tests\Fixture\VisitTableFixture;

class ExcludedStudentParticipantRepositoryTest extends EntityRepositoryTestCase
{
    protected function getTableFixtures()
    {
        return [
            new ExcludedStudentParticipantTableFixture(),
            new VisitTableFixture(),
            new StudentTableFixture(),
            new EmptyEntityTableFixture(Expense::class),
            new EmptyEntityTableFixture(Income::class),
            new EmptyEntityTableFixture(Itinerary::class),
            new EmptyEntityTableFixture(StudentParticipant::class),
            new EmptyEntityTableFixture(StaffParticipant::class)
        ];
    }

    private function getRepository()
    {
        return $this->em->getRepository(ExcludedStudentParticipant::class);
    }

    private function createVisit(int $id)
    {
        $area = $this->prophesize(Visit::class);
        $area->getId()->willReturn($id);
        return $area->reveal();
    }

    private function createStudent(int $id)
    {
        $area = $this->prophesize(Student::class);
        $area->getId()->willReturn($id);
        return $area->reveal();
    }

    public function testFindByVisit()
    {
        $visit = $this->createVisit(VisitTableFixture::DARTMOOR_TRIP_ID);
        $this->assertCount(2, $this->getRepository()->findByVisit($visit));
    }

    public function testFindStudentsByVisit()
    {
        $visit = $this->createVisit(VisitTableFixture::DARTMOOR_TRIP_ID);

        $students = $this->getRepository()->findStudentsByVisit($visit);
        $this->assertCount(2, $students);

        foreach($students as $student) {
            $this->assertInstanceOf(Student::class, $student);
        }
    }

    public function testFindOneByStudentAndVisit()
    {
        $visit = $this->createVisit(VisitTableFixture::DARTMOOR_TRIP_ID);
        $student = $this->createStudent(StudentTableFixture::JOHN_BARNES_ID);
        $excludedStudentParticipant = $this->getRepository()->findOneByStudentAndVisit($student, $visit);
        $this->assertSame($excludedStudentParticipant->getId(), ExcludedStudentParticipantTableFixture::DARTMOOR_TRIP_JOHN_BARNES_ID);
    }
}