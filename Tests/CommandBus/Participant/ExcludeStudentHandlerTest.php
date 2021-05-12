<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use Cis\EducationalVisitBundle\CommandBus\Participant\ExcludeStudentCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\ExcludeStudentHandler;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;

class ExcludeStudentHandlerTest extends TestCase
{
    public function testHandle()
    {
        $orm = $this->prophesize(Orm::class);
        $util = $this->prophesize(ParticipantUtil::class);

        $visit = $this->prophesize(Visit::class)->reveal();
        $student = $this->prophesize(Student::class)->reveal();
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();
        $studentPayment = ['paid' => 10.0];

        $orm->persist(Argument::type(ExcludedStudentParticipant::class))->shouldBeCalledOnce();

        $util->getStudentPayment(Argument::exact($studentParticipant))->willReturn($studentPayment)->shouldBeCalledOnce();
        $util->removeStudent(Argument::exact($studentParticipant), Argument::exact($studentPayment))->shouldBeCalledOnce();

        $command = new ExcludeStudentCommand($studentParticipant);
        $handler = new ExcludeStudentHandler($orm->reveal(), $util->reveal());
        $handler->handle($command);

        $excludedStudentParticipant = $command->getExcludedStudentParticipant();
        $this->assertSame($visit, $excludedStudentParticipant->getVisit());
        $this->assertSame($student, $excludedStudentParticipant->getStudent());
    }
}