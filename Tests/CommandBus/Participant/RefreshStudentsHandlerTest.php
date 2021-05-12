<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use App\Entity\Cohort\Cohort;
use App\Entity\Student\Student;
use App\Repository\Cohort\CohortRepository;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Repository\ExcludedStudentParticipantRepository;
use Cis\EducationalVisitBundle\Repository\StudentParticipantRepository;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\Helper\Orm;
use Cis\EducationalVisitBundle\CommandBus\Participant\RefreshStudentsCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\RefreshStudentsHandler;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class RefreshStudentsHandlerTest extends TestCase
{
    protected function setUp()
    {

    }

    public function testHandle()
    {
        $orm = $this->prophesize(Orm::class);;
        $util = $this->prophesize(ParticipantUtil::class);

        $cohort = $this->prophesize(Cohort::class)->reveal();

        // Should be excluded since already a participant
        $studentOne = $this->prophesize(Student::class);
        $studentOne->getId()->willReturn(1);
        $studentOne = $studentOne->reveal();
        $studentParticipantOne = $this->prophesize(StudentParticipant::class);
        $studentParticipantOne->getStudent()->willReturn($studentOne);
        $studentParticipantOne->getCohort()->willReturn(null);
        $studentParticipantOne = $studentParticipantOne->reveal();

        // Should not be imported or deleted as they aren't a cohort imported student
        $studentTwo = $this->prophesize(Student::class);
        $studentTwo->getId()->willReturn(2);
        $studentTwo = $studentTwo->reveal();
        $studentParticipantTwo = $this->prophesize(StudentParticipant::class);
        $studentParticipantTwo->getStudent()->willReturn($studentTwo);
        $studentParticipantTwo->getCohort()->willReturn(null);
        $studentParticipantTwo = $studentParticipantTwo->reveal();

        // Should not be imported but deleted as they are no longer in the cohort import but were before
        $studentThree = $this->prophesize(Student::class);
        $studentThree->getId()->willReturn(3);
        $studentThree = $studentThree->reveal();
        $studentParticipantThree = $this->prophesize(StudentParticipant::class);
        $studentParticipantThree->getStudent()->willReturn($studentThree);
        $studentParticipantThree->getCohort()->willReturn($cohort);
        $studentParticipantThree = $studentParticipantThree->reveal();

        // To check they're included within the $excludedStudents array
        $excludedStudent = $this->prophesize(Student::class);
        $excludedStudent->getId()->willReturn(4);
        $excludedStudent = $excludedStudent->reveal();

        $newStudent = $this->prophesize(Student::class);
        $newStudent->getId()->willReturn(5);
        $newStudent = $newStudent->reveal();

        $studentParticipants = [
            $studentParticipantOne,
            $studentParticipantTwo,
            $studentParticipantThree
        ];

        $excludedStudents = [
            $excludedStudent,
            $studentOne,
            $studentTwo,
            $studentThree
        ];

        $cohortStudents = [
            $studentOne,
            $newStudent
        ];

        $visit = $this->prophesize(Visit::class);
        $visit->getId()->willReturn(10);
        $visit = $visit->reveal();

        $repo = $this->prophesize(CohortRepository::class);
        $repo->findByObjectAndReference(Visit::COHORT_OBJECT, $visit->getId())->willReturn([$cohort]);
        $orm->getRepository(Cohort::class)->willReturn($repo->reveal());

        $repo = $this->prophesize(StudentParticipantRepository::class);
        $repo->findByVisit($visit)->willReturn($studentParticipants);
        $orm->getRepository(StudentParticipant::class)->willReturn($repo->reveal());

        $repo = $this->prophesize(ExcludedStudentParticipantRepository::class);
        $repo->findStudentsByVisit($visit)->willReturn([$excludedStudent]);
        $orm->getRepository(ExcludedStudentParticipant::class)->willReturn($repo->reveal());


        $util->getCohortStudents($cohort)->willReturn($cohortStudents)->shouldBeCalled($cohortStudents);

        $util->addStudents(Argument::type(Visit::class), Argument::type(Cohort::class), Argument::exact($cohortStudents), Argument::exact($excludedStudents))->shouldBeCalled();

        $studentPayment = ['paid' => 10.0];
        $util->getStudentPayment(Argument::exact($studentParticipantThree))->willReturn($studentPayment)->shouldBeCalledOnce();
        $util->removeStudent(Argument::exact($studentParticipantThree), Argument::exact($studentPayment))->shouldBeCalledOnce();

        $command = new RefreshStudentsCommand($visit);
        $handler = new RefreshStudentsHandler($orm->reveal(), $util->reveal());

        $handler->handle($command);
    }
}