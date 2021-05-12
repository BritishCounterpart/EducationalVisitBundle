<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use Cis\EducationalVisitBundle\CommandBus\Participant\AddVisitConsentCommand;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class AddVisitConsentCommandTest extends TestCase
{
    public function testHandle()
    {
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->setHasVisitConsent(Argument::exact(true))->shouldBeCalledOnce();

        $command = new AddVisitConsentCommand($studentParticipant->reveal());
        $command->handle();
    }
}