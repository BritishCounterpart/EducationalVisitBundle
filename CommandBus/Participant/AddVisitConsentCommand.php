<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Petroc\Component\CommandBus\SelfHandlingCommand;

class AddVisitConsentCommand extends SelfHandlingCommand
{
    private $studentParticipant;

    public function __construct(StudentParticipant $studentParticipant)
    {
        $this->studentParticipant = $studentParticipant;
    }

    public function handle()
    {
        $this->studentParticipant->setHasVisitConsent(true);
    }
}