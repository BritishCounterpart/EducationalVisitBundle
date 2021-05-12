<?php

namespace Cis\EducationalVisitBundle\Tests\Repository;

use App\Entity\Cohort\Cohort;
use App\Entity\Student\Student;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\EntityRepositoryTestCase;
use App\Tests\Fixture\Cohort\CohortTableFixture;
use App\Tests\Fixture\Student\StudentTableFixture;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Tests\Fixture\StudentParticipantTableFixture;
use Cis\EducationalVisitBundle\Tests\Fixture\VisitTableFixture;

class StudentParticipantRepositoryTest extends EntityRepositoryTestCase
{
    protected function getTableFixtures()
    {
        return [
            new StudentParticipantTableFixture(),
            new VisitTableFixture(),
            new StudentTableFixture(),
            new CohortTableFixture(),
            new EmptyEntityTableFixture(Income::class),
            new EmptyEntityTableFixture(Itinerary::class)
        ];
    }

    private function getRepository()
    {
        return $this->em->getRepository(StudentParticipant::class);
    }

    private function createVisit(int $id)
    {
        $area = $this->prophesize(Visit::class);
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

    public function testFindByVisitAndCohort()
    {
        $visit = $this->createVisit(VisitTableFixture::DARTMOOR_TRIP_ID);
        $cohort = $this->prophesize(Cohort::class);
        $cohort->getId()->willReturn(CohortTableFixture::COHORT_EDUCATIONAL_VISIT_DARTMOOR_ID);
        $cohort = $cohort->reveal();

        $students = $this->getRepository()->findByVisitAndCohort($visit, $cohort);
        $this->assertCount(1, $students);
    }

    public function testFindOneByStudentAndVisit()
    {
        $visit = $this->createVisit(VisitTableFixture::DARTMOOR_TRIP_ID);
        $student = $this->prophesize(Student::class);
        $student->getId()->willReturn(StudentTableFixture::JOHN_BARNES_ID);
        $student = $student->reveal();

        $studentParticipant = $this->getRepository()->findOneByStudentAndVisit($student, $visit);
        $this->assertSame($studentParticipant->getId(), StudentParticipantTableFixture::DARTMOOR_TRIP_JOHN_BARNES_ID);
    }

    public function testFindByStudent()
    {
        $student = $this->prophesize(Student::class);
        $student->getId()->willReturn(StudentTableFixture::JOHN_BARNES_ID);
        $student = $student->reveal();

        $studentParticipants = $this->getRepository()->findByStudent($student);
        $this->assertCount(1, $studentParticipants);
    }

}