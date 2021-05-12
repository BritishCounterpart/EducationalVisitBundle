<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\CommandBus\Participant\UnexcludeStudentCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\UnexcludeStudentHandler;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\StudentParticipantRepository;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;

class UnexcludeStudentHandlerTest extends TestCase
{
    private $orm;
    private $student;
    private $visit;
    private $excludedStudentParticipant;
    private $studentParticipant;


    protected function setUp()
    {
        $this->orm = $this->prophesize(Orm::class);
        $this->student = $this->prophesize(Student::class)->reveal();
        $this->visit = $this->prophesize(Visit::class)->reveal();
        $this->excludedStudentParticipant = $this->prophesize(ExcludedStudentParticipant::class);
        $this->studentParticipant = $this->prophesize(StudentParticipant::class);
    }

    public function testRemoveOnlyHandle()
    {
        $orm = $this->orm;
        $student = $this->student;
        $visit = $this->visit;

        $excludedStudentParticipant = $this->prophesize(ExcludedStudentParticipant::class);
        $excludedStudentParticipant->getStudent()->willReturn($student);
        $excludedStudentParticipant->getVisit()->willReturn($visit);
        $excludedStudentParticipant = $excludedStudentParticipant->reveal();

        $repo = $this->prophesize(StudentParticipantRepository::class);
        $repo->findOneByStudentAndVisit($student, $visit)->willReturn(null);
        $orm->getRepository(StudentParticipant::class)->willReturn($repo->reveal());

        $orm->remove(Argument::exact($excludedStudentParticipant))->shouldBeCalledOnce();

        $command = new UnexcludeStudentCommand($excludedStudentParticipant);
        $handler = new UnexcludeStudentHandler($orm->reveal());
        $handler->handle($command);
    }

    public function testSetNoLongerGoingAndRemoveHandle()
    {
        $orm = $this->orm;
        $student = $this->student;
        $visit = $this->visit;

        $studentParticipant = $this->studentParticipant;
        $studentParticipant->setNoLongerGoing(Argument::exact(false))->shouldBeCalledOnce();
        $studentParticipant = $studentParticipant->reveal();

        $excludedStudentParticipant = $this->prophesize(ExcludedStudentParticipant::class);
        $excludedStudentParticipant->getStudent()->willReturn($student);
        $excludedStudentParticipant->getVisit()->willReturn($visit);
        $excludedStudentParticipant = $excludedStudentParticipant->reveal();

        $repo = $this->prophesize(StudentParticipantRepository::class);
        $repo->findOneByStudentAndVisit($student, $visit)->willReturn($studentParticipant);
        $orm->getRepository(StudentParticipant::class)->willReturn($repo->reveal());

        $orm->remove(Argument::exact($excludedStudentParticipant))->shouldBeCalledOnce();

        $command = new UnexcludeStudentCommand($excludedStudentParticipant);
        $handler = new UnexcludeStudentHandler($orm->reveal());
        $handler->handle($command);
    }
}