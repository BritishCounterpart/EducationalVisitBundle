<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use App\Entity\Employee\Employee;
use Cis\EducationalVisitBundle\CommandBus\Participant\AddStaffParticipantCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\AddStaffParticipantHandler;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;

class AddStaffParticipantHandlerTest extends TestCase
{
    public function testHandle()
    {
        $orm = $this->prophesize(Orm::class);

        $visit = $this->prophesize(Visit::class)->reveal();
        $employee = $this->prophesize(Employee::class)->reveal();

        $orm->persist(Argument::type(StaffParticipant::class))->shouldBeCalledOnce();

        $command = new AddStaffParticipantCommand($visit);
        $command->employee = $employee;
        $handler = new AddStaffParticipantHandler($orm->reveal());
        $handler->handle($command);

        $this->assertSame($visit, $command->getStaffParticipant()->getVisit());
        $this->assertSame($employee, $command->getStaffParticipant()->getEmployee());
    }
}