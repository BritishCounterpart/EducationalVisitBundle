<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use Cis\EducationalVisitBundle\CommandBus\Participant\UnexcludeStudentCommand;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Petroc\Bridge\PhpUnit\TestCase;

class UnexcludeStudentCommandTest extends TestCase
{
    public function testConstructor()
    {
        $excludedStudentParticipant = $this->prophesize(ExcludedStudentParticipant::class)->reveal();
        $command = new UnexcludeStudentCommand($excludedStudentParticipant);

        $this->assertSame($excludedStudentParticipant, $command->getExcludedStudentParticipant());
    }
}