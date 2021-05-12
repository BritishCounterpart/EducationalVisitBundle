<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use Cis\EducationalVisitBundle\CommandBus\Participant\AddStaffParticipantCommand;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;

class AddStaffParticipantCommandTest extends TestCase
{
    public function testConstructor()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $command = new AddStaffParticipantCommand($visit);

        $this->assertSame($visit, $command->getVisit());
    }

    public function testSetStaffParticipant()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $staffParticipant = $this->prophesize(StaffParticipant::class)->reveal();
        $command = new AddStaffParticipantCommand($visit);

        $this->assertSetAndGet($command, 'staffParticipant', $staffParticipant);
    }

    public function testLoadValidatorMetadata()
    {
        $this->assertCanLoadValidatorMetadata(AddStaffParticipantCommand::class);
    }
}