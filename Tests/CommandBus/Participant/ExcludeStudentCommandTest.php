<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use Cis\EducationalVisitBundle\CommandBus\Participant\ExcludeStudentCommand;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Petroc\Bridge\PhpUnit\TestCase;

class ExcludeStudentCommandTest extends TestCase
{
    public function testConstructor()
    {
        $studentParticipant = $this->prophesize(StudentParticipant::class)->reveal();
        $command = new ExcludeStudentCommand($studentParticipant);

        $this->assertSame($studentParticipant, $command->getStudentParticipant());
    }

    public function testSetStaffParticipant()
    {
        $studentParticipant = $this->prophesize(StudentParticipant::class)->reveal();
        $excludedStudentParticipant = $this->prophesize(ExcludedStudentParticipant::class)->reveal();
        $command = new ExcludeStudentCommand($studentParticipant);

        $this->assertSetAndGet($command, 'excludedStudentParticipant', $excludedStudentParticipant);
    }

    public function testLoadValidatorMetadata()
    {
        $this->assertCanLoadValidatorMetadata(ExcludeStudentCommand::class);
    }
}